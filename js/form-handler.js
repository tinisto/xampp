// form-handler.js

alert("fdsfsd");

// This script is designed to work with the common-form.php file located at:
// includes/common-form.php

document.addEventListener("DOMContentLoaded", function () {
  // Get all forms with the class 'dynamicForm'
  var forms = document.querySelectorAll(".dynamicForm");

  forms.forEach(function (form) {
    var submitButton = form.querySelector('button[type="submit"]');

    // Add event listeners to form inputs
    form.addEventListener("input", function () {
      // Check if all required fields are filled
      var areFieldsFilled = Array.from(form.elements).every(function (element) {
        return (
          !element.required ||
          (element.type !== "checkbox" && element.value.trim() !== "")
        );
      });

      // Enable or disable the submit button based on field status
      submitButton.disabled = !areFieldsFilled;
    });
  });
});
