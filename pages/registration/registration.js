// registration.js

// This script dynamically enables or disables the registration button
// based on whether all form fields have non-empty values.
// If any field is empty, the button is disabled; otherwise, it's enabled.

document.addEventListener("DOMContentLoaded", function () {
  var formFields = document.querySelectorAll(
    "#firstname, #lastname, #occupation, #newPassword, #confirmPassword, #email"
  );

  formFields.forEach(function (field) {
    field.addEventListener("input", function () {
      var registrationButton = document.getElementById("registrationButton");
      var isFormValid = Array.from(formFields).every(function (field) {
        return field.value.trim() !== "";
      });
      registrationButton.disabled = !isFormValid;
    });
  });
});
