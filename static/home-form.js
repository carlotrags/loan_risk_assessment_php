document.addEventListener("DOMContentLoaded", () => {
    // Select form elements for manipulation
    const form = document.querySelector("form");
    const birthdateInput = document.getElementById("birthdate");
    const hiredDateInput = document.getElementById("hired");
    const incomeInput = document.getElementById("income");
    const debtInput = document.getElementById("debt");
    
    // Auto-calculate Age based on Birthdate
    //Listens for changes to the birthdate field and updates the read-only age field
    birthdateInput.addEventListener("change", () => {
        const birthDate = new Date(birthdateInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        // Adjust age if the birthday hasn't occurred yet in the current year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        document.getElementById("age").value = age > 0 ? age : 0;
    });

    // Auto-calculate Years Employed based on Date Hired
    //Determines the length of professional tenure to help assess stability
    hiredDateInput.addEventListener("change", () => {
        const hiredDate = new Date(hiredDateInput.value);
        const today = new Date();
        let years = today.getFullYear() - hiredDate.getFullYear();
        const monthDiff = today.getMonth() - hiredDate.getMonth();
        
        // Adjust years if the work anniversary hasn't passed yet this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < hiredDate.getDate())) {
            years--;
        }
        
        document.getElementById("years_employed").value = years >= 0 ? years : 0;
    });

    // Auto-calculate DTI (Debt-to-Income) Ratio
    //Calculates the ratio of monthly debt payments to gross monthly income
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

    // Trigger DTI calculation whenever income or debt values are typed
    incomeInput.addEventListener("input", calculateDTI);
    debtInput.addEventListener("input", calculateDTI);

    // Form Validation before submission
    //Checks for eligibility criteria and prevents submission if rules are violated
    form.addEventListener("submit", (e) => {
        const age = parseInt(document.getElementById("age").value);
        const loanAmount = parseFloat(document.querySelector('input[name="loan_amount"]').value);
        const income = parseFloat(incomeInput.value);

        // Simple Validation Rules
        // Rule: Applicant must be within the acceptable age range (21-65)
        if (age < 21 || age > 65) {
            alert("Applicant must be between 21 and 65 years old for home loan eligibility.");
            e.preventDefault();
            return;
        }

        // Rule: Enforce a minimum loan threshold
        if (loanAmount < 1000000) {
            alert("Minimum loan amount for Home Loan is Php 1,000,000.");
            e.preventDefault();
            return;
        }

        // Rule: Ensure financial data is provided
        if (income <= 0) {
            alert("Please enter a valid monthly income.");
            e.preventDefault();
            return;
        }

        // --- UI LOADING STATE --
        // Show loading state sa button
        // Disable the submit button and show a spinner to prevent double-submission
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> PROCESSING ASSESSMENT...';
    });
});