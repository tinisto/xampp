// toggleAvatarInput.js
// This script toggles the avatar input field based on the state of the uploadAvatarCheckbox.
// If the checkbox is checked, it enables the avatar input; otherwise, it disables and hides it.
function toggleAvatarInput() {
  var avatarInput = document.getElementById("avatar");
  var uploadAvatarCheckbox = document.getElementById("uploadAvatar");
  var avatarSection = document.getElementById("avatarSection");

  avatarInput.disabled = !uploadAvatarCheckbox.checked;

  if (uploadAvatarCheckbox.checked) {
    avatarSection.style.display = "block";
  } else {
    avatarSection.style.display = "none";
  }
}
