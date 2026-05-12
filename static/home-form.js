document.addEventListener("DOMContentLoaded", () => {
    // Select form elements for manipulation
    const form = document.querySelector("form");
    const birthdateInput = document.getElementById("birthdate");
    const hiredDateInput = document.getElementById("hired");
    const incomeInput = document.getElementById("income");
    const debtInput = document.getElementById("debt");
    const tinInput = document.getElementById("tin_no");

    // Auto-calculate Age based on Birthdate
    birthdateInput.addEventListener("change", () => {
        const birthDate = new Date(birthdateInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        document.getElementById("age").value = age > 0 ? age : 0;
    });

    // Auto-calculate Years Employed
    hiredDateInput.addEventListener("change", () => {
        const hiredDate = new Date(hiredDateInput.value);
        const today = new Date();
        let years = today.getFullYear() - hiredDate.getFullYear();
        const monthDiff = today.getMonth() - hiredDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < hiredDate.getDate())) {
            years--;
        }

        document.getElementById("years_employed").value = years >= 0 ? years : 0;
    });

    // Auto-calculate DTI
    const calculateDTI = () => {
        const monthlyIncome = parseFloat(incomeInput.value) || 0;
        const monthlyDebt = parseFloat(debtInput.value) || 0;

        if (monthlyIncome > 0) {
            const ratio = (monthlyDebt / monthlyIncome).toFixed(2);
            document.getElementById("dti").value = ratio;
        } else {
            document.getElementById("dti").value = "0.00";
        }
    };

    incomeInput.addEventListener("input", calculateDTI);
    debtInput.addEventListener("input", calculateDTI);

    // ✅ TIN AUTO FORMAT (WORKING 123-123-123)
    if (tinInput) {
        tinInput.addEventListener("input", function () {
            let value = this.value;

            // remove non-digits
            value = value.replace(/\D/g, "");

            // limit to 9 digits
            value = value.substring(0, 9);

            // build format manually
            let formatted = "";

            for (let i = 0; i < value.length; i++) {
                if (i === 3 || i === 6) {
                    formatted += "-";
                }
                formatted += value[i];
            }

            this.value = formatted;
        });
    }

    // Form Validation before submission
    form.addEventListener("submit", (e) => {
        const age = parseInt(document.getElementById("age").value);
        const loanAmount = parseFloat(document.querySelector('input[name="loan_amount"]').value);
        const income = parseFloat(incomeInput.value);

        if (age < 21 || age > 65) {
            alert("Applicant must be between 21 and 65 years old for home loan eligibility.");
            e.preventDefault();
            return;
        }

        if (loanAmount < 1000000) {
            alert("Minimum loan amount for Home Loan is Php 1,000,000.");
            e.preventDefault();
            return;
        }

        if (income <= 0) {
            alert("Please enter a valid monthly income.");
            e.preventDefault();
            return;
        }

        // Loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> PROCESSING ASSESSMENT...';
    });
});