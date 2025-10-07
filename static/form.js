document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  const inputs = form.querySelectorAll("input, select");

  form.addEventListener("submit", (e) => {
    let hasError = false;

    // Clear previous errors
    inputs.forEach((input) => input.classList.remove("input-error"));

    inputs.forEach((input) => {
      const val = input.value.trim();

      // Basic required field check
      if (!val) {
        markError(input, "This field is required.");
        hasError = true;
        return;
      }

      // Validate input types
      if (input.name === "income" || input.name === "loan_amount") {
        if (isNaN(val) || parseFloat(val) <= 0) {
          markError(input, "Must be a positive number.");
          hasError = true;
        }
      }

      if (input.name === "credit_score") {
        const score = parseInt(val);
        if (isNaN(score) || score < 300 || score > 850) {
          markError(input, "Credit score must be between 300 and 850.");
          hasError = true;
        }
      }

      if (input.name === "loan_term") {
        if (isNaN(val) || parseInt(val) <= 0 || parseInt(val) > 60) {
          markError(input, "Loan term must be between 1 and 60 months.");
          hasError = true;
        }
      }

      if (input.name === "name") {
        if (/[^a-zA-Z\s'.-]/.test(val)) {
          markError(input, "Invalid characters in name.");
          hasError = true;
        }
      }

      if (input.name === "previous_defaults") {
        if (!["0", "1"].includes(val)) {
          markError(input, "Invalid value for defaults.");
          hasError = true;
        }
      }
    });

    if (hasError) {
      e.preventDefault(); // Block submission if validation fails
    }
  });

  function markError(input, message) {
    input.classList.add("input-error");
    input.setCustomValidity(message);
    input.reportValidity();
  }
});
