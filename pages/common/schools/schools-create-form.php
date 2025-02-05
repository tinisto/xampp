<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

$userId = $_SESSION["user_id"];

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
  // Only apply this check if the user is not an admin
  if ($_SESSION['occupation'] !== 'Представитель школы') {
    // Redirect the user if their occupation does not match
    header("Location: /unauthorized"); // Or any other page you'd like to redirect them to
    exit();
  }
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";

// Initialize variables for form fields
$newSchoolName = "";
$newFullName = "";
$newShortName = "";
$newSite = "";
$newEmail = "";
$newEmail2 = "";
$newTel = "";
$newFax = "";
$newDirectorRole = "";
$newDirectorName = "";
$newDirectorInfo = "";
$newDirectorPhone = "";
$newDirectorEmail = "";
$newHistory = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Validate and sanitize form data
  $fields = [
    'school_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'full_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'short_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'site' => FILTER_SANITIZE_URL,
    'email' => FILTER_SANITIZE_EMAIL,
    'email2' => FILTER_SANITIZE_EMAIL,
    'tel' => FILTER_SANITIZE_NUMBER_INT,
    'fax' => FILTER_SANITIZE_NUMBER_INT,
    'director_role' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'director_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'director_info' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'director_phone' => FILTER_SANITIZE_NUMBER_INT,
    'director_email' => FILTER_SANITIZE_EMAIL,
    'history' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
  ];

  $sanitizedData = [];
  foreach ($fields as $field => $filter) {
    $sanitizedData[$field] = filter_input(INPUT_POST, $field, $filter);
  }

  // Extract sanitized data to individual variables
  $newSchoolName = $sanitizedData['school_name'] ?? null;
  $newFullName = $sanitizedData['full_name'] ?? null;
  $newShortName = $sanitizedData['short_name'] ?? null;
  $newSite = $sanitizedData['site'] ?? null;
  $newEmail = $sanitizedData['email'] ?? null;
  $newEmail2 = $sanitizedData['email2'] ?? null;
  $newTel = $sanitizedData['tel'] ?? null;
  $newFax = $sanitizedData['fax'] ?? null;
  $newDirectorRole = $sanitizedData['director_role'] ?? null;
  $newDirectorName = $sanitizedData['director_name'] ?? null;
  $newDirectorInfo = $sanitizedData['director_info'] ?? null;
  $newDirectorPhone = $sanitizedData['director_phone'] ?? null;
  $newDirectorEmail = $sanitizedData['director_email'] ?? null;
  $newHistory = $sanitizedData['history'] ?? null;
}

// Check if there is form data in the session
if (isset($_SESSION['form_data'])) {
  $formData = $_SESSION['form_data'];
  $newSchoolName = $formData['school_name'] ?? '';
  $newFullName = $formData['full_name'] ?? '';
  $newShortName = $formData['short_name'] ?? '';
  $newSite = $formData['site'] ?? '';
  $newEmail = $formData['email'] ?? '';
  $newEmail2 = $formData['email2'] ?? '';
  $newTel = $formData['tel'] ?? '';
  $newFax = $formData['fax'] ?? '';
  $newDirectorRole = $formData['director_role'] ?? '';
  $newDirectorName = $formData['director_name'] ?? '';
  $newDirectorInfo = $formData['director_info'] ?? '';
  $newDirectorPhone = $formData['director_phone'] ?? '';
  $newDirectorEmail = $formData['director_email'] ?? '';
  $newHistory = $formData['history'] ?? '';
  unset($_SESSION['form_data']); // Clear the session data after using it
}

// Check if there is address data in the session
$addressData = isset($_SESSION['address_data']) ? $_SESSION['address_data'] : [];
$selectedCountry = $addressData['country'] ?? '';
$selectedRegion = $addressData['region'] ?? '';
$selectedArea = $addressData['area'] ?? '';
$selectedTown = $addressData['town'] ?? '';
$selectedZipCode = $addressData['zip_code'] ?? '';
$selectedStreet = $addressData['street'] ?? '';
unset($_SESSION['address_data']); // Clear the session data after using it

?>
<!-- HTML Form for creating a new college -->
<h3 class="text-center text-white"><?php echo $pageTitle; ?></h3>
<p class='text-center text-danger text-bold'>Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<div class="container">
  <div class="row justify-content-center">
    <form action="/pages/common/schools/schools-create-process.php" method="post" enctype="multipart/form-data">

      <div class="card px-3 pt-2">
        <label class="mb-2">Выберите адрес<?php echo requiredAsterisk(); ?></label>

        <?php
        // Define variables for the included file
        $selected_country = isset($_SESSION['form_data']['country']) ? $_SESSION['form_data']['country'] : 1; // Default to 1 if not set
        $selected_region = isset($_SESSION['form_data']['region']) ? $_SESSION['form_data']['region'] : null;
        $selected_area = isset($_SESSION['form_data']['area']) ? $_SESSION['form_data']['area'] : null;
        $selected_town = isset($_SESSION['form_data']['town']) ? $_SESSION['form_data']['town'] : null;

        // Include the address selection file
        include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/address-selection.php";
        ?>
      </div>
      <div class="row  my-3">
        <!-- Zip Code (2 columns) -->
        <div class="col-md-1">
          <div class="form-floating">
            <input type="text" class="form-control" id="zip_code" name="zip_code"
              value="<?php echo $selectedZipCode; ?>" placeholder="Индекс"
              style="font-size: 14px;">
            <label for="zip_code">Индекс</label>
          </div>
        </div>

        <!-- Street (11 columns) -->
        <div class="col-md-11">

          <div class="form-floating">
            <input type="text" class="form-control" id="street" name="street"
              value="<?php echo $selectedStreet; ?>" placeholder="Улица, дом"
              style="font-size: 14px;">
            <label for="street">Улица, дом<?php echo requiredAsterisk(); ?></label>
          </div>
        </div>
      </div>

      <div class="row  my-3">
        <!-- Zip Code (2 columns) -->
        <div class="col-md-9">
          <div class="form-floating">
            <input type="text" class="form-control" id="school_name" name="school_name"
              value="<?php echo $newSchoolName; ?>" required placeholder="Название нового учебного заведения"
              style="font-size: 14px;">
            <label for="school_name">Название учебного заведения<?php echo requiredAsterisk(); ?></label>
          </div>
        </div>

        <!-- Street (11 columns) -->
        <div class="col-md-3">

          <div class="form-floating">
            <input type="text" class="form-control" id="short_name" name="short_name"
              value="<?php echo $newShortName; ?>" placeholder="Сокращенное название нового учебного заведения"
              style="font-size: 14px;">
            <label for="short_name">Сокращенное название</label>
          </div>

        </div>
      </div>

      <!-- Full Name -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $newFullName; ?>"
          placeholder="Полное название нового учебного заведения" style="font-size: 14px;">
        <label for="full_name">Полное название</label>
      </div>

      <div class="row  my-3">
        <!-- Zip Code (2 columns) -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="text" class="form-control" id="site" name="site"
              value="<?php echo $newSite; ?>" placeholder="Веб сайт нового учебного заведения"
              style="font-size: 14px;">
            <label for="site">Веб сайт<?php echo requiredAsterisk(); ?></label>
          </div>
        </div>

        <!-- Street (11 columns) -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="email" class="form-control" id="email" name="email"
              value="<?php echo $newEmail; ?>" placeholder="name@example.ru"
              style="font-size: 14px;">
            <label for="email">Email<?php echo requiredAsterisk(); ?></label>
          </div>

        </div>
      </div>

      <div class="row  my-3">
        <!-- Zip Code (2 columns) -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="text" class="form-control" id="tel" name="tel"
              value="<?php echo $newTel; ?>" placeholder="Телефоны нового учебного заведения"
              style="font-size: 14px;">
            <label for="tel">Телефоны</label>
          </div>
        </div>

        <!-- Street (11 columns) -->
        <div class="col-md-6">

          <div class="form-floating">
            <input type="text" class="form-control" id="fax" name="fax"
              value="<?php echo $newFax; ?>" placeholder="Факс нового учебного заведения"
              style="font-size: 14px;">
            <label for="fax">Факс</label>
          </div>

        </div>
      </div>

      <!-- Director Role -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="director_role" name="director_role"
          value="<?php echo $newDirectorRole; ?>" placeholder="Должность руководителя" style="font-size: 14px;">
        <label for="director_role">Должность руководителя</label>
      </div>

      <!-- Director Name -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="director_name" name="director_name"
          value="<?php echo $newDirectorName; ?>" placeholder="Фамилия руководителя" style="font-size: 14px;">
        <label for="director_name">ФИО руководителя</label>
      </div>

      <!-- Director Info -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="director_info" name="director_info"
          value="<?php echo $newDirectorInfo; ?>" placeholder="Научные звания и награды руководителя"
          style="font-size: 14px;">
        <label for="director_info">Научные звания и награды руководителя</label>
      </div>
      <div class="row  my-3">
        <!-- Zip Code (2 columns) -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="text" class="form-control" id="director_phone" name="director_phone"
              value="<?php echo $newDirectorPhone; ?>" placeholder="Телефон руководителя"
              style="font-size: 14px;">
            <label for="director_phone">Телефон руководителя</label>
          </div>
        </div>

        <!-- Street (11 columns) -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="email" class="form-control" id="director_email" name="director_email"
              value="<?php echo $newDirectorEmail; ?>" placeholder="name@example.ru"
              style="font-size: 14px;">
            <label for="director_email">Email руководителя</label>
          </div>

        </div>
      </div>

      <div class="form-floating mb-3">
        <textarea class="form-control" id="history" name="history" style="height: 250px;"
          placeholder="История учебного заведения"
          style="font-size: 14px;"><?php echo htmlspecialchars(
                                      $newHistory
                                    ); ?></textarea>
        <label for="history">История учебного заведения (не более 1000 символов)</label>
      </div>

      <!-- Hidden Fields for Meta Data -->
      <input type="hidden" id="meta_d_school" name="meta_d_school" value="default_meta_d_value">
      <input type="hidden" id="meta_k_school" name="meta_k_school" value="default_meta_k_value">

      <h6 class="text-center text-white">Разрешены только файлы в форматах JPG, PNG и GIF.</h6>


      <div class="row my-3">
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="col-md-4">
            <div class="form-floating mb-3">
              <input type="file" class="form-control" id="image_school_<?= $i ?>" name="image_school_<?= $i ?>" accept="image/*">
              <label for="image_school_<?= $i ?>">Загрузить изображение <?= $i ?> (необязательно)</label>
              <!-- Image Preview Container -->
              <div class="mt-3 position-relative" id="image-container-<?= $i ?>" style="display: <?= isset($_SESSION['temporary_images']["image_school_$i"]) ? 'block' : 'none'; ?>;">
                <img id="image-preview-<?= $i ?>" src="<?= isset($_SESSION['temporary_images']["image_school_$i"]) ? '/images/schools-images/' . htmlspecialchars($_SESSION['temporary_images']["image_school_$i"]) : '' ?>" alt="Предпросмотр изображения <?= $i ?>" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <!-- Close Button -->
                <button type="button" id="clear-image-<?= $i ?>" class="ms-2 btn btn-light btn-sm">Удалить</button>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>



      <input type="hidden" id="userId" name="userId" value="<?= htmlspecialchars($userId); ?>">

      <?php echo renderButtonBlock("Создать страницу", "Отмена"); ?>
    </form>
  </div>
</div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = [
      document.getElementById('image_school_1'),
      document.getElementById('image_school_2'),
      document.getElementById('image_school_3')
    ];

    const imagePreviews = [
      document.getElementById('image-preview-1'),
      document.getElementById('image-preview-2'),
      document.getElementById('image-preview-3')
    ];

    const clearButtons = [
      document.getElementById('clear-image-1'),
      document.getElementById('clear-image-2'),
      document.getElementById('clear-image-3')
    ];

    const imageContainers = [
      document.getElementById('image-container-1'),
      document.getElementById('image-container-2'),
      document.getElementById('image-container-3')
    ];

    fileInputs.forEach((fileInput, index) => {
      fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            imagePreviews[index].src = e.target.result;
            imageContainers[index].style.display = 'block'; // Show preview with close button
          };
          reader.readAsDataURL(file);
        }
      });
    });

    clearButtons.forEach((clearButton, index) => {
      clearButton.addEventListener('click', function() {
        fileInputs[index].value = ""; // Clear file input
        imagePreviews[index].src = ""; // Clear preview image
        imageContainers[index].style.display = 'none'; // Hide preview and button

        // Send AJAX request to delete the temporary image
        fetch('/delete-temporary-image.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            image: 'image_school_' + (index + 1)
          })
        }).then(response => response.json()).then(data => {
          if (data.success) {
            console.log('Temporary image deleted successfully.');
          } else {
            console.error('Error deleting temporary image:', data.error);
          }
        });
      });
    });
  });
</script>