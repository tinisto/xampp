<div class="container mt-4">
  <form id="searchForm" action="/search-process" method="get" class="mb-3">
    <div class="input-group">
      <input type="text" name="query" class="form-control" placeholder="Искать на 11klassniki.ru...">
      <span class="input-group-text" onclick="redirectToMainPage()">
        <i class="fas fa-times" style="cursor: pointer;"></i>
      </span>
    </div>
  </form>

  <div id="spinner-container" style="display: none;">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
</div>

<script>
  function submitForm() {
    // Show the spinner
    document.getElementById('spinner-container').style.display = 'block';

    // Get form data
    var formData = new FormData(document.getElementById('searchForm'));

    // Perform the AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/search-process?' + new URLSearchParams(formData).toString(), true);

    xhr.onload = function () {
      // Handle the response from the server

      // Hide the spinner after processing the response
      document.getElementById('spinner-container').style.display = 'none';
    };

    xhr.onerror = function () {
      // Handle errors if the AJAX request fails

      // Hide the spinner in case of an error
      document.getElementById('spinner-container').style.display = 'none';
    };

    xhr.send();
  }
</script>

<script>
  function redirectToMainPage() {
    window.location.href = "/";
  }
</script>