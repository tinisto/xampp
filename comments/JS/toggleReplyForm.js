// toggleReplyForm.js

// Script to toggle the visibility of reply forms or redirect to login
function toggleReplyForm(commentId) {
  // Read the login status from the data attribute
  var isUserLoggedIn =
    document
      .getElementById("loginStatus")
      .getAttribute("data-is-user-logged-in") === "true";

  if (isUserLoggedIn) {
    // If the user is logged in, toggle the visibility of the reply form
    var replyForm = document.getElementById("replyForm_" + commentId);
    var displayStyle = window
      .getComputedStyle(replyForm)
      .getPropertyValue("display");

    if (displayStyle === "none" || displayStyle === "") {
      replyForm.style.display = "block";
    } else {
      replyForm.style.display = "none";
    }
  } else {
    // If the user is not logged in, redirect to the login page
    redirectToLogin();
  }
}

// Function to redirect to the login page
function redirectToLogin() {
  window.location.href = "/login"; // Update the URL to your login page
}
