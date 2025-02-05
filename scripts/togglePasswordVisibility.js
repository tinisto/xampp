// Toggle password visibility
// This script toggles the visibility of password fields when the associated icon is clicked.
function togglePasswordVisibility(passwordId) {
  var passwordInput = document.getElementById(passwordId);
  var icon = document.getElementById("show" + passwordId);

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
    passwordInput.type = "password";
    icon.innerHTML = '<i class="fas fa-eye"></i>';
  }
}

document
  .getElementById("showNewPassword")
  .addEventListener("click", function () {
    togglePasswordVisibility("newPassword");
  });

document
  .getElementById("showConfirmPassword")
  .addEventListener("click", function () {
    togglePasswordVisibility("confirmPassword");
  });
