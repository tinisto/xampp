<style>
  .search-container {
    max-width: 800px;
    margin: 40px auto 0;
    padding: 0 20px;
  }
  .modern-search-form {
    display: flex;
    background: white;
    border-radius: 50px;
    padding: 5px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
  }
  .modern-search-form:focus-within {
    border-color: #28a745;
    box-shadow: 0 6px 25px rgba(40, 167, 69, 0.2);
  }
  .modern-search-input {
    flex: 1;
    border: none;
    padding: 15px 25px;
    font-size: 16px;
    outline: none;
    border-radius: 50px;
    background: transparent;
  }
  .modern-search-input::placeholder {
    color: #6c757d;
  }
  .modern-search-close {
    background: #f8f9fa;
    color: #6c757d;
    border: none;
    padding: 15px 20px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 5px;
  }
  .modern-search-close:hover {
    background: #e9ecef;
    color: #495057;
  }
  @media (max-width: 768px) {
    .search-container {
      margin: 20px auto 0;
      padding: 0 15px;
    }
    .modern-search-form {
      border-radius: 25px;
      padding: 3px;
    }
    .modern-search-input {
      padding: 12px 20px;
      font-size: 16px;
    }
    .modern-search-close {
      padding: 12px 15px;
      border-radius: 25px;
    }
  }
  @media (max-width: 480px) {
    .modern-search-input {
      padding: 12px 15px;
      font-size: 16px;
    }
    .modern-search-close {
      padding: 12px;
    }
  }
</style>

<div class="search-container">
  <form id="searchForm" action="/search-process" method="get" class="mb-4">
    <div class="modern-search-form">
      <input type="text" name="query" class="modern-search-input" placeholder="Поиск школ, ВУЗов, статей..." autocomplete="off">
      <button type="button" class="modern-search-close" onclick="redirectToMainPage()" title="Закрыть">
        <i class="fas fa-times"></i>
      </button>
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