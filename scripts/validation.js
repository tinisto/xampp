// validation.js
// This script provides client-side validation for the registration form.
function validateForm() {
  var newPassword = document.getElementById("newPassword").value;
  var confirmPassword = document.getElementById("confirmPassword").value;

  // Password strength criteria
  var passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;

  if (!passwordRegex.test(newPassword)) {
    alert(
      "Пароль должен содержать минимум 8 символов и включать буквы и цифры."
    );
    return false;
  }

  if (newPassword !== confirmPassword) {
    alert("Пароли не совпадают. Пожалуйста, попробуйте еще раз.");
    return false;
  }

  var uploadAvatarCheckbox = document.getElementById("uploadAvatar");
  var avatarInput = document.getElementById("avatar");

  if (uploadAvatarCheckbox.checked && avatarInput.value === "") {
    alert("Выберите файл для загрузки аватара.");
    return false;
  }

  return true;
}
