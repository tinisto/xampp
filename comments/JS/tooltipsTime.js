// Function to initialize Bootstrap tooltips for the entire document
function initializeTooltips() {
  // Get all elements with the 'data-toggle="tooltip"' attribute
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-toggle="tooltip"]')
  );

  // Map each tooltip trigger element to a Tooltip instance
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    // Configure tooltip options
    var tooltipOptions = {
      delay: {
        show: 0,
        hide: 0,
      },
    };

    // Create a new Tooltip instance for each tooltip trigger element
    var tooltip = new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions);

    // Customize tooltip text font size
    var timeSpan = tooltipTriggerEl.querySelector(".time-tooltip");
    if (timeSpan) {
      // Add !important to force precedence for custom styles
      timeSpan.style.fontSize = "12px !important";
    }

    // Return the Tooltip instance
    return tooltip;
  });
}

// Call the function when the DOM is fully loaded

document.addEventListener("DOMContentLoaded", initializeTooltips);
