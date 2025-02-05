// toggleComments.js

// This script sets up a toggle functionality for comments within a container.
// It hides and shows content, toggles the visibility icon, and prevents card click events.
// Include this script in your HTML to enable the toggleComments functionality.

function setupToggleComments() {
  // When the DOM content is fully loaded
  document.addEventListener("DOMContentLoaded", function () {
    // Get the parent container that holds all the comments
    const commentsContainer = document.getElementById("here");

    // Add click event to each toggleIcon within the container
    commentsContainer.addEventListener("click", function (event) {
      const toggleIcon = event.target.closest(".toggleCard");
      // Check if the clicked element is a toggleIcon
      if (toggleIcon) {
        const hiddenContent = toggleIcon
          .closest(".card")
          .querySelector(".hiddenContent");
        const shownContent = toggleIcon
          .closest(".card")
          .querySelector(".shownContent");
        event.stopPropagation(); // Prevent card click event from being triggered
        // Toggle the visibility by adding or removing the 'd-none' class
        hiddenContent.classList.toggle("d-none");
        shownContent.classList.toggle("d-none");
        // Toggle the icon between 'fa-eye-slash' and 'fa-eye'
        toggleIcon.classList.toggle("fa-eye-slash");
        toggleIcon.classList.toggle("fa-eye");

        // Check if "custom-background" class is present
        const isCustomBackground =
          toggleIcon.classList.contains("custom-background");

        // Toggle the "custom-background" class based on its presence
        toggleIcon.classList.toggle("custom-background", !isCustomBackground);
      }
    });
  });
}

// Call the function to set up the toggle functionality
setupToggleComments();
