<?php
$linkBootstrapHover = "text-white link-offset-2 link-offset-3-hover link-underline-light link-underline-opacity-0 link-underline-opacity-75-hover";
?>

<footer class="p-3 mt-auto bg-dark border-top border-white">
  <div class="row d-flex justify-content-evenly align-items-center">
    <div class="col-md-3 col-12 text-center py-1 text-white">
      <small>&copy; 2009-<span id="currentYear"></span></small>
    </div>

    <div class=" col-md-4 col-12 text-center py-1">
      <small><a href="/about" class="<?php echo $linkBootstrapHover; ?>">О сайте</a></small>
    </div>

    <!-- <div class="col-md-4 col-12 text-center py-1">
      <small><a href="/write" class="<?php echo $linkBootstrapHover; ?>">Обратная связь</a></small>
    </div> -->

    <div class="col-md-4 col-12 text-center py-1">
      <small>
        <a href="javascript:location='mailto:'+['contact','11klassniki.ru'].join('@');" class="<?php echo $linkBootstrapHover; ?>">contact@11klassniki.ru</a>
      </small>
    </div>




    <div class="col-md-1 d-flex justify-content-center mt-1 mt-md-0 py-1">
      <!-- Scroll to Top Button -->
      <button onclick="scrollToTop()" class="btn btn-outline-light btn-sm scroll-to-top" style="display: none;"><i
          class="fas fa-arrow-up"></i></button>
    </div>
  </div>
</footer>
<!-- End Footer -->

<script>
  // Function to check scroll position and toggle the button visibility
  function toggleScrollTopButton() {
    var button = document.querySelector('.scroll-to-top');
    button.style.display = (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) ? 'block' : 'none';
  }

  // Function to scroll to the top
  function scrollToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }

  // Attach the toggleScrollTopButton function to the scroll event
  window.onscroll = function() {
    toggleScrollTopButton();
  };
</script>

<script>
  // Update the current year dynamically
  document.getElementById("currentYear").textContent = new Date().getFullYear();
</script>