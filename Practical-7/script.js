document.getElementById("loginForm").onsubmit = function (event) {
    event.preventDefault();
    document.getElementById("message").textContent = "Login credentials are valid.";

};
