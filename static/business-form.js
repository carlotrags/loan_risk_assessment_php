document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('.business-form-container form');
    if (!form) return;

    // Use .step-content for finding inputs in the current step
    const stepContents = Array.from(document.querySelectorAll('.step-content'));
    // Get the current step index (0-based) from the hidden input added in PHP
    const hiddenStepInput = form.querySelector('input[name="current_step_for_js_validation"]'); 

    let currentStepIndex = hiddenStepInput ? parseInt(hiddenStepInput.value) - 1 : 0;
    
    // NOTE: All client-side navigation functions (showStep, nextButtons.forEach, etc.) 
    // are REMOVED. PHP handles which step is visible after submission.

    form.addEventListener("submit", (e) => {
        
        let hasError = false;

        // Skip validation on the 'Preview' step (Step 5 - index 4)
        if (currentStepIndex === 4) {
             // Allow submission (PHP will handle final_submit)
             return; 
        }

        const currentStep = stepContents[currentStepIndex];
        if (!currentStep) return; 

        const inputs = Array.from(
            currentStep.querySelectorAll('input, select, textarea')
        );

        clearAllErrors(currentStep);

        // ========== TEXT & NUMBER VALIDATION ==========
        inputs.forEach(input => {
            const val = input.value.trim();

            // Skip radios/checkboxes (handled later)
            if (input.type === "radio" || input.type === "checkbox") return;

            // Required check
            if (input.required && !val) {
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
                    return;
                }
            }

            // Special rule: Loan term (Step 1)
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

        // Prevent form submission if there are validation errors.
        // If no errors, the form is ALLOWED to submit to business-form.php, 
        // which saves the data and advances the step (or submits the final application).
        if (hasError) {
            e.preventDefault();
        }
    });

    // =========================
    // Helper Functions
    // =========================

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

        let errorRow = row.nextElementSibling;
        if (!errorRow || !errorRow.classList.contains("validation-error-row")) {
            errorRow = document.createElement("tr");
            errorRow.classList.add("validation-error-row");
            
            const td = document.createElement("td");
            td.colSpan = row.children.length;
            td.classList.add("error-msg");
            errorRow.appendChild(td);
            
            row.insertAdjacentElement("afterend", errorRow);
        }

        errorRow.querySelector(".error-msg").textContent = message;
    }

    function clearAllErrors(step) {
        // Clear input errors
        step.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));

        // Remove error message divs
        step.querySelectorAll(".error-msg").forEach(el => {
            if (el.parentElement && el.parentElement.tagName === 'DIV') {
                el.remove();
            }
            if (el.closest(".validation-error-row")) {
                el.closest(".validation-error-row").remove();
            }
        });
    }
});