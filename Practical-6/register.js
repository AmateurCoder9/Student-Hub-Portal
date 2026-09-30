const registerForm = document.getElementById("registerForm");
const registrationMessage = document.getElementById("message");
const passwordInput = document.getElementById("password");
const passwordStrength = document.getElementById("passwordStrength");
const passwordStrengthText = document.getElementById("passwordStrengthText");
let hasTriedSubmit = false;

function setError(fieldId, message) {
  const field = document.getElementById(fieldId) || document.getElementById(fieldId + "Group");
  const error = document.getElementById(fieldId + "Error");

  error.textContent = message;
  if (field) {
    field.setAttribute("aria-invalid", message ? "true" : "false");
  }
}

function getPasswordStrength(password) {
  let score = 0;
  if (password.length >= 8) score++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
  if (/\d/.test(password)) score++;
  if (/[^A-Za-z0-9]/.test(password)) score++;
  return score;
}

function updatePasswordStrength() {
  const score = getPasswordStrength(passwordInput.value);
  const labels = ["not entered", "weak", "fair", "good", "strong"];

  passwordStrength.value = score;
  passwordStrength.setAttribute("aria-valuetext", labels[score]);
  passwordStrengthText.textContent = "Password strength: " + labels[score];
}

function validateForm() {
  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const mobile = document.getElementById("mobile").value.trim();
  const password = passwordInput.value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const course = document.getElementById("course").value;
  const year = document.getElementById("year").value;
  const gender = document.querySelector('input[name="gender"]:checked');
  const termsAccepted = document.getElementById("terms").checked;
  const passwordMeetsRequirements = password.length >= 8 && /[a-z]/.test(password) && /[A-Z]/.test(password) && (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password));
  const errors = {
    name: /^[A-Za-z][A-Za-z .'-]{1,59}$/.test(name) ? "" : "Enter a name using at least 2 letters.",
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? "" : "Enter a valid email address.",
    mobile: /^[6-9]\d{9}$/.test(mobile) ? "" : "Enter a valid 10-digit mobile number.",
    password: passwordMeetsRequirements && getPasswordStrength(password) >= 3 ? "" : "Use at least 8 characters, upper- and lowercase letters, and a number or symbol.",
    confirmPassword: password && password === confirmPassword ? "" : "Passwords do not match.",
    course: course ? "" : "Select a course.",
    year: year ? "" : "Select your year.",
    gender: gender ? "" : "Select an option.",
    terms: termsAccepted ? "" : "Accept the terms to continue."
  };

  Object.keys(errors).forEach(function (fieldId) {
    setError(fieldId, errors[fieldId]);
  });

  const firstInvalidField = Object.keys(errors).find(function (fieldId) {
    return errors[fieldId];
  });

  if (firstInvalidField) {
    const firstField = document.getElementById(firstInvalidField);
    if (firstField) {
      firstField.focus();
    } else if (firstInvalidField === "gender") {
      document.querySelector('input[name="gender"]').focus();
    } else {
      document.getElementById("terms").focus();
    }
    return false;
  }

  return true;
}

passwordInput.addEventListener("input", function () {
  updatePasswordStrength();
});

registerForm.addEventListener("input", function () {
  registrationMessage.textContent = "";
  if (hasTriedSubmit) {
    validateForm();
  }
});

registerForm.addEventListener("change", function () {
  registrationMessage.textContent = "";
  if (hasTriedSubmit) {
    validateForm();
  }
});

registerForm.addEventListener("submit", function (event) {
  event.preventDefault();
  hasTriedSubmit = true;
  registrationMessage.textContent = "";

  if (!validateForm()) {
    return;
  }

  registrationMessage.textContent = "All fields are valid. No registration data was sent or saved.";
});

updatePasswordStrength();