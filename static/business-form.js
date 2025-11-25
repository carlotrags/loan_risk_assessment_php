document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const stepContainer = document.querySelector('.step-container');
    const steps = stepContainer ? stepContainer.querySelectorAll('.step') : [];
    const hiddenStep = form ? form.querySelector('input[name="hiddenStep"]') : null;

    // Show current step based on hiddenStep value
    let currentStepIndex = hiddenStep ? parseInt(hiddenStep.value) - 1 : 0;
    showStep(currentStepIndex);

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        let hasError = false;

        const currentInputs = steps[currentStepIndex] ? 
            Array.from(steps[currentStepIndex].querySelectorAll('input, select, textarea')) : [];

        // Clear previous errors
        currentInputs.forEach(input => {
            input.classList.remove("input-error");
            clearError(input);
        });

        // Validate text/number fields
        currentInputs.forEach(input => {
            const val = input.value.trim();
            if (input.type !== 'radio' && input.type !== 'checkbox') {
                if (!val) {
                    markError(input, "This field is required.");
                    hasError = true;
                    return;
                }

                if (input.name === "income" || input.name === "loan_amount") {
                    if (isNaN(val) || parseFloat(val) <= 0) {
                        markError(input, "Must be a positive number.");
                        hasError = true;
                    }
                }

                if (input.name === "loan_term") {
                    if (isNaN(val) || parseInt(val) <= 0 || parseInt(val) > 60) {
                        markError(input, "Loan term must be between 1 and 60 months.");
                        hasError = true;
                    }
                }
            }
        });

        // Validate radio groups in current step
        const radioNames = new Set(
            currentInputs.filter(input => input.type === 'radio').map(input => input.name)
        );

        radioNames.forEach(name => {
            const radios = steps[currentStepIndex].querySelectorAll(`input[name="${name}"]`);
            const isChecked = Array.from(radios).some(r => r.checked);
            if (!isChecked) {
                markError(radios[0], "Please select an option.");
                hasError = true;
            }
        });

        if (!hasError) {
            // Update hidden step
            if (hiddenStep) hiddenStep.value = currentStepIndex + 1;
            form.submit();
        }
    });

    function showStep(index) {
        steps.forEach((step, i) => {
            step.style.display = i === index ? 'block' : 'none';
        });
    }

    function markError(el, msg) {
        el.classList.add("input-error");
        let error = el.nextElementSibling;
        if (!error || !error.classList.contains('error-msg')) {
            error = document.createElement('div');
            error.className = 'error-msg';
            el.parentNode.insertBefore(error, el.nextSibling);
        }
        error.textContent = msg;
    }

    function clearError(el) {
        const error = el.nextElementSibling;
        if (error && error.classList.contains('error-msg')) {
            error.remove();
        }
    }
});