document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('.business-form-container form');
    if (!form) return;

    const steps = Array.from(document.querySelectorAll('.step'));
    const hiddenStep = form.querySelector('input[name="step"]');

    let currentStepIndex = hiddenStep ? parseInt(hiddenStep.value) - 1 : 0;
    showStep(currentStepIndex);

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        let hasError = false;

        const currentStep = steps[currentStepIndex];
        const inputs = Array.from(
            currentStep.querySelectorAll('input, select, textarea')
        );

        clearAllErrors(currentStep);

        // ========== TEXT & NUMBER VALIDATION ==========
        inputs.forEach(input => {
            const val = input.value.trim();

            // Skip radios (handled later)
            if (input.type === "radio" || input.type === "checkbox") return;

            // Required check
            if (!val) {
                markError(input, "This field is required.");
                hasError = true;
                return;
            }

            // Auto-validate number fields
            if (input.type === "number") {
                const num = parseFloat(val);
                if (isNaN(num)) {
                    markError(input, "Please enter a valid number.");
                    hasError = true;
                    return;
                }
                if (num < 0) {
                    markError(input, "Value cannot be negative.");
                    hasError = true;
                }
            }

            // Special rule: Loan term
            if (input.name === "loan_term") {
                const term = parseInt(val);
                if (isNaN(term) || term < 1 || term > 60) {
                    markError(input, "Loan term must be between 1 and 60 months.");
                    hasError = true;
                }
            }
        });

        // ========== RADIO BUTTON VALIDATION ==========
        const radioGroups = new Set(
            inputs.filter(i => i.type === "radio").map(i => i.name)
        );

        radioGroups.forEach(groupName => {
            const radios = currentStep.querySelectorAll(`input[name="${groupName}"]`);
            const isChecked = Array.from(radios).some(r => r.checked);

            if (!isChecked) {
                markRadioGroupError(radios[0], "Please select an option.");
                hasError = true;
            }
        });

        // ========== SUBMIT OR MOVE TO NEXT STEP ==========
        if (!hasError) {
            if (currentStepIndex < steps.length - 1) {
                currentStepIndex++;
                if (hiddenStep) hiddenStep.value = currentStepIndex + 1;
                showStep(currentStepIndex);
            } else {
                form.submit();
            }
        }
    });

    // =========================
    // Helper Functions
    // =========================

    function showStep(index) {
        steps.forEach((step, i) => {
            step.style.display = (i === index) ? "block" : "none";
        });
    }

    function markError(input, message) {
        input.classList.add("input-error");

        let error = input.parentElement.querySelector(".error-msg");
        if (!error) {
            error = document.createElement("div");
            error.classList.add("error-msg");
            input.parentElement.appendChild(error);
        }
        error.textContent = message;
    }

    function markRadioGroupError(radio, message) {
        const row = radio.closest("tr");
        if (!row) return;

        let error = row.nextElementSibling;
        if (!error || !error.classList.contains("error-msg")) {
            error = document.createElement("tr");
            const td = document.createElement("td");
            td.colSpan = row.children.length;
            td.classList.add("error-msg");
            error.appendChild(td);
            row.insertAdjacentElement("afterend", error);
        }

        error.querySelector("td").textContent = message;
    }

    function clearAllErrors(step) {
        step.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));
        step.querySelectorAll(".error-msg").forEach(el => el.remove());
    }
});
