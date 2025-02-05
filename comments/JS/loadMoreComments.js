document.addEventListener('DOMContentLoaded', function () {
  // Get the loadMoreButton element
  var loadMoreButton = document.getElementById('loadMoreButton');

  // Check if the button is found
  if (loadMoreButton) {
    // Get data attributes for id_entity and entity_type
    var id_entity = loadMoreButton.dataset.idEntity;
    var entity_type = loadMoreButton.dataset.entityType;

    // Initialize page number
    var page = 1;

    // Add click event listener to the button
    loadMoreButton.addEventListener('click', function () {
      // Increment the page number for the next request
      page++;

      // Create XMLHttpRequest
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            // Create a container for new comments
            var commentsContainer = document.createElement('div');
            commentsContainer.innerHTML = xhr.responseText;

            // Check the number of new comments loaded
            var newCommentsCount =
              commentsContainer.querySelectorAll('.vasya').length;

            if (newCommentsCount < 5) {
              // If less than 5 new comments loaded, hide the button
              loadMoreButton.style.display = 'none';
            }

            // Get the existing container
            var existingContainer = document.getElementById('here');

            // Check if the existing container is found
            if (existingContainer) {
              // Append new comments directly to the existing container
              var newComments =
                commentsContainer.querySelectorAll('.card.vasya');
              newComments.forEach(function (newComment) {
                existingContainer.appendChild(newComment.cloneNode(true));
              });

              // Call initializeTooltips after new content is loaded
              initializeTooltips();
            }
          } else {
            console.error('Error loading comments. Status:', xhr.status);
          }
        }
      };

      // Send a GET request to load_comments.php with the updated page and id_entity
      xhr.open(
        'GET',
        '/comments/load_comments.php?page=' +
          page +
          '&id_entity=' +
          id_entity +
          '&entity_type=' +
          entity_type,
        true
      );
      xhr.send();
    });

    // Function to initialize Bootstrap tooltips for the entire document
    function initializeTooltips() {
      var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-toggle="tooltip"]')
      );
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
      });
    }

    // Call the function when the DOM is fully loaded
    initializeTooltips();
  }
});
