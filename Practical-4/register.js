document.getElementById("registerForm").onsubmit = function (event) {
  event.preventDefault();

  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const message = document.getElementById("message");

  if (password !== confirmPassword) {
    message.textContent = "Passwords do not match.";
    message.style.color = "red";
  } else {
    message.textContent = "Registration successful.";
    message.style.color = "green";
  }
};