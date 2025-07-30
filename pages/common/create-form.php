<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

$userId = $_SESSION["user_id"];


// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
  // Check if the user's occupation is neither 'Представитель ССУЗа' nor 'Представитель ВУЗа'
  if (!in_array($_SESSION['occupation'], ['Представитель ССУЗа', 'Представитель ВУЗа'])) {
    // Redirect to the unauthorized page if the conditions are met
    header("Location: /unauthorized");
    exit();
  } else {
    // Set form type based on occupation for non-admin users
    $formType = $_SESSION['occupation'] === 'Представитель ССУЗа' ? 'spo' : 'vpo';
    $isSPO = $formType === 'spo'; // Set the isSPO flag
  }
} else {
  // For admin users, the form type comes from the query parameter 'type'
  $formType = isset($_GET['type']) ? $_GET['type'] : 'vpo'; // Default to 'vpo' if not set
  $isSPO = $formType === 'spo'; // Set the isSPO flag based on formType
}


include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";

$form_data = $_SESSION['form_data'] ?? [];

// Fields to be initialized
$fields = [
  $isSPO ? 'spo_name' : 'vpo_name',
  'zip_code',
  'street',
  'full_name',
  'short_name',
  'site',
  'email',
  'tel',
  'fax',
  'director_role',
  'director_name',
  'director_info',
  'director_phone',
  'director_email',
  'history',
  $isSPO ? 'spo_url' : 'vpo_url',
  'name_rod',
  'old_name',
  'site_pk',
  'email_pk',
  'tel_pk',
  'otvetcek',
  'accreditation',
  'licence',
  $isSPO ? 'filials_spo' : 'filials_vpo',
  'vkontakte',
  'address_pk',
  $isSPO ? 'parent_spo_id' : 'parent_vpo_id',
  $isSPO ? 'meta_d_spo' : 'meta_d_vpo',
  $isSPO ? 'meta_k_spo' : 'meta_k_vpo'
];

// Initialize form values
$form_values = [];
foreach ($fields as $field) {
  $form_values[$field] = $form_data[$field] ?? "";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Sanitize and validate input fields
  $sanitizedData = [];
  foreach ($fields as $field) {
    $sanitizedData[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

  // Merge sanitized data with form values
  $form_values = array_merge($form_values, $sanitizedData);

  // Store form data in session for repopulation
  $_SESSION['form_data'] = $form_values;

  // Further validation can be added here, such as checking the format of emails, etc.
}

// Reusable input generation function
function generateInput($id, $name, $value, $label, $required = false)
{
  echo "<div class='form-floating my-3'>";
  echo "<input type='text' class='form-control' id='$id' name='$name' value='" . htmlspecialchars($value ?? '') . "' placeholder='$label' style='font-size: 14px'" . ($required ? " required" : "") . ">";
  echo "<label for='$id'>$label" . ($required ? requiredAsterisk() : "") . "</label>";
  echo "</div>";
}

function generateInputRow($id1, $name1, $value1, $label1, $id2, $name2, $value2, $label2, $required1 = false, $required2 = false)
{
  echo '<div class="row my-3">';
  echo '<div class="col-md-6">';
  echo '<div class="form-floating">';
  echo '<input type="text" class="form-control" id="' . $id1 . '" name="' . $name1 . '" value="' . htmlspecialchars($value1 ?? '') . '" placeholder="' . $label1 . '" style="font-size: 14px;"' . ($required1 ? ' required' : '') . '>';
  echo '<label for="' . $id1 . '">' . $label1 . ($required1 ? requiredAsterisk() : '') . '</label>';
  echo '</div>';
  echo '</div>';
  echo '<div class="col-md-6">';
  echo '<div class="form-floating">';
  echo '<input type="text" class="form-control" id="' . $id2 . '" name="' . $name2 . '" value="' . htmlspecialchars($value2 ?? '') . '" placeholder="' . $label2 . '" style="font-size: 14px;"' . ($required2 ? ' required' : '') . '>';
  echo '<label for="' . $id2 . '">' . $label2 . ($required2 ? requiredAsterisk() : '') . '</label>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
}

function generateInputRowThree($id1, $name1, $value1, $label1, $id2, $name2, $value2, $label2, $id3, $name3, $value3, $label3, $required1 = false, $required2 = false, $required3 = false)
{
  echo '<div class="row my-3">';

  echo '<div class="col-md-4">';
  echo '<div class="form-floating">';
  echo '<input type="text" class="form-control" id="' . $id1 . '" name="' . $name1 . '" value="' . htmlspecialchars($value1 ?? '') . '" placeholder="' . $label1 . '" style="font-size: 14px;"' . ($required1 ? ' required' : '') . '>';
  echo '<label for="' . $id1 . '">' . $label1 . ($required1 ? requiredAsterisk() : '') . '</label>';
  echo '</div>';
  echo '</div>';

  echo '<div class="col-md-4">';
  echo '<div class="form-floating">';
  echo '<input type="text" class="form-control" id="' . $id2 . '" name="' . $name2 . '" value="' . htmlspecialchars($value2 ?? '') . '" placeholder="' . $label2 . '" style="font-size: 14px;"' . ($required2 ? ' required' : '') . '>';
  echo '<label for="' . $id2 . '">' . $label2 . ($required2 ? requiredAsterisk() : '') . '</label>';
  echo '</div>';
  echo '</div>';

  echo '<div class="col-md-4">';
  echo '<div class="form-floating">';
  echo '<input type="text" class="form-control" id="' . $id3 . '" name="' . $name3 . '" value="' . htmlspecialchars($value3 ?? '') . '" placeholder="' . $label3 . '" style="font-size: 14px;"' . ($required3 ? ' required' : '') . '>';
  echo '<label for="' . $id3 . '">' . $label3 . ($required3 ? requiredAsterisk() : '') . '</label>';
  echo '</div>';
  echo '</div>';

  echo '</div>';
}
?>

<!-- HTML Form for creating a new educational institution -->
<h3 class="text-center text-white"><?php echo $pageTitle; ?></h3>
<p class="text-center text-danger text-bold">Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<?php if (isset($_SESSION['error-message'])): ?>
  <div class="alert alert-danger text-center">
    <?php echo $_SESSION['error-message'];
    unset($_SESSION['error-message']); ?>
  </div>
<?php endif; ?>

<div class="container">
  <div class="row justify-content-center">
    <form action="/pages/common/create-process.php" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
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

      <?php
      if ($_SESSION['role'] === 'admin') {
        generateInputRow($isSPO ? 'meta_d_spo' : 'meta_d_vpo', $isSPO ? 'meta_d_spo' : 'meta_d_vpo', $form_values[$isSPO ? 'meta_d_spo' : 'meta_d_vpo'], 'meta_d', $isSPO ? 'meta_k_spo' : 'meta_k_vpo', $isSPO ? 'meta_k_spo' : 'meta_k_vpo', $form_values[$isSPO ? 'meta_k_spo' : 'meta_k_vpo'], 'meta_k', false, false);
      } else {
        echo '<input type="hidden" name="' . ($isSPO ? 'meta_d_spo' : 'meta_d_vpo') . '" value="' . htmlspecialchars($form_values[$isSPO ? 'meta_d_spo' : 'meta_d_vpo']) . '">';
        echo '<input type="hidden" name="' . ($isSPO ? 'meta_k_spo' : 'meta_k_vpo') . '" value="' . htmlspecialchars($form_values[$isSPO ? 'meta_k_spo' : 'meta_k_vpo']) . '">';
      }

      if ($_SESSION['role'] === 'admin') {
        generateInputRow($isSPO ? 'parent_spo_id' : 'parent_vpo_id', $isSPO ? 'parent_spo_id' : 'parent_vpo_id', $form_values[$isSPO ? 'parent_spo_id' : 'parent_vpo_id'], 'Parent ID', $isSPO ? 'filials_spo' : 'filials_vpo', $isSPO ? 'filials_spo' : 'filials_vpo', $form_values[$isSPO ? 'filials_spo' : 'filials_vpo'], 'Филиалы (через запятую)');
      } else {
        echo '<input type="hidden" name="' . ($isSPO ? 'parent_spo_id' : 'parent_vpo_id') . '" value="' . htmlspecialchars($form_values[$isSPO ? 'parent_spo_id' : 'parent_vpo_id']) . '">';
        echo '<input type="hidden" name="' . ($isSPO ? 'filials_spo' : 'filials_vpo') . '" value="' . htmlspecialchars($form_values[$isSPO ? 'filials_spo' : 'filials_vpo']) . '">';
      }

      generateInputRow('zip_code', 'zip_code', $form_values['zip_code'], 'Индекс', 'street', 'street', $form_values['street'], 'Улица, дом', false, false);
      generateInput($isSPO ? 'spo_name' : 'vpo_name', $isSPO ? 'spo_name' : 'vpo_name', $form_values[$isSPO ? 'spo_name' : 'vpo_name'], 'Название учебного заведения', true);
      generateInput('full_name', 'full_name', $form_values['full_name'], 'Полное название', false);
      generateInputRow('short_name', 'short_name', $form_values['short_name'], 'Сокращенное название', 'name_rod', 'name_rod', $form_values['name_rod'], 'Название в родительном падеже');
      generateInput('old_name', 'old_name', $form_values['old_name'], 'Прежние названия учебного заведения');
      generateInputRowThree('site', 'site', $form_values['site'], 'Веб сайт учебного заведения', 'email', 'email', $form_values['email'], 'Email', 'tel', 'tel', $form_values['tel'], 'Телефон', false, false, false);
      generateInputRow('accreditation', 'accreditation', $form_values['accreditation'], 'Аккредитация', 'licence', 'licence', $form_values['licence'], 'Лицензия');

      generateInputRow('director_role', 'director_role', $form_values['director_role'], 'Должность руководителя', 'director_name', 'director_name', $form_values['director_name'], 'ФИО руководителя');
      generateInput('director_info', 'director_info', $form_values['director_info'], 'Научные звания и награды руководителя');
      generateInputRow('director_phone', 'director_phone', $form_values['director_phone'], 'Телефон руководителя', 'director_email', 'director_email', $form_values['director_email'], 'Email руководителя');
      generateInput('vkontakte', 'vkontakte', $form_values['vkontakte'], 'ВКонтакте');

      generateInputRow('otvetcek', 'otvetcek', $form_values['otvetcek'], 'Ответственный секретарь', 'address_pk', 'address_pk', $form_values['address_pk'], 'Адрес PK');

      generateInputRowThree('site_pk', 'site_pk', $form_values['site_pk'], 'Веб сайт приемной комиссии', 'email_pk', 'email_pk', $form_values['email_pk'], 'Email PK', 'tel_pk', 'tel_pk', $form_values['tel_pk'], 'Телефон PK');

      ?>

      <!-- History Textarea -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="history" name="history" style="height: 250px;" placeholder="История учебного заведения" style="font-size: 14px;"><?php echo htmlspecialchars($form_values['history'] ?? ''); ?></textarea>
        <label for="history">История учебного заведения (не более 1000 символов)</label>
      </div>

      <h6 class="text-center text-white">Разрешены только файлы в форматах JPG, PNG и GIF.</h6>

      <div class="row my-3">
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="col-md-4">
            <div class="form-floating mb-3">
              <input type="file" class="form-control" id="image_<?= $formType ?>_<?= $i ?>" name="image_<?= $formType ?>_<?= $i ?>" accept="image/*">
              <label for="image_<?= $formType ?>_<?= $i ?>">Загрузить изображение <?= $i ?> (необязательно)</label>
              <!-- Image Preview Container -->
              <div class="mt-3 position-relative" id="image-container-<?= $i ?>" style="display: <?= isset($_SESSION['temporary_images']['image_' . $formType . '_' . $i]) ? 'block' : 'none'; ?>;">
                <img id="image-preview-<?= $i ?>" src="<?= isset($_SESSION['temporary_images']['image_' . $formType . '_' . $i]) ? '/images/' . $formType . '-images/' . htmlspecialchars($_SESSION['temporary_images']['image_' . $formType . '_' . $i]) : '' ?>" alt="Предпросмотр изображения <?= $i ?>" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <!-- Close Button -->
                <button type="button" id="clear-image-<?= $i ?>" class="ms-2 btn btn-light btn-sm">Удалить</button>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>

      <input type="hidden" id="userId" name="userId" value="<?= htmlspecialchars($userId); ?>">
      <input type="hidden" name="fax" value="<?php echo isset($fax) ? htmlspecialchars($fax) : ''; ?>">
      <input type="hidden" name="formType" value="<?= $formType ?>">


      <?php echo renderButtonBlock("Создать страницу", "Отмена"); ?>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = [
      document.getElementById('image_<?= $formType ?>_1'),
      document.getElementById('image_<?= $formType ?>_2'),
      document.getElementById('image_<?= $formType ?>_3')
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
            image: 'image_<?= $formType ?>_' + (index + 1),
            type: '<?= $formType ?>'
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