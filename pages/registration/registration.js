// registration.js

document.addEventListener("DOMContentLoaded", function () {
  // Set timezone
  document.getElementById('timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;

  // Toggle password visibility
  function togglePasswordVisibility(passwordId, toggleId) {
    var passwordInput = document.getElementById(passwordId);
    var icon = document.querySelector('#' + toggleId + ' i');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.className = 'fas fa-eye-slash';
    } else {
      passwordInput.type = 'password';
      icon.className = 'fas fa-eye';
    }
  }

  // Password toggle event listeners
  document.getElementById('showPassword').addEventListener('click', function () {
    togglePasswordVisibility('password', 'showPassword');
  });

  document.getElementById('showConfirmPassword').addEventListener('click', function () {
    togglePasswordVisibility('confirmPassword', 'showConfirmPassword');
  });

  // Form validation
  var formFields = document.querySelectorAll("#email, #password, #confirmPassword, #occupation");
  var termsCheck = document.getElementById('termsCheck');
  var submitBtn = document.getElementById('submitBtn');

  function validateForm() {
    var isFormValid = Array.from(formFields).every(function (field) {
      return field.value.trim() !== "";
    });
    var isTermsAccepted = termsCheck.checked;
    var passwordsMatch = document.getElementById('password').value === document.getElementById('confirmPassword').value;
    
    submitBtn.disabled = !isFormValid || !isTermsAccepted || !passwordsMatch;
  }

  // Add event listeners
  formFields.forEach(function (field) {
    field.addEventListener("input", validateForm);
  });

  termsCheck.addEventListener("change", validateForm);

  // Password match validation
  document.getElementById('confirmPassword').addEventListener('input', function() {
    var password = document.getElementById('password').value;
    var confirmPassword = this.value;
    
    if (confirmPassword !== '' && password !== confirmPassword) {
      this.style.borderColor = '#dc3545';
      this.nextElementSibling.querySelector('i').style.color = '#dc3545';
    } else {
      this.style.borderColor = '';
      this.nextElementSibling.querySelector('i').style.color = '';
    }
    validateForm();
  });

  // Initial validation
  validateForm();
});
