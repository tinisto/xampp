<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

// Check if 'id_vpo' parameter is present in the URL and is a valid integer
if (isset($_GET['id_vpo'])) {
  // Retrieve and sanitize the id_vpo from the URL
  $id_vpo = filter_var($_GET['id_vpo'], FILTER_SANITIZE_NUMBER_INT);

  // Validate if the sanitized ID is a valid number
  if ($id_vpo && is_numeric($id_vpo) && $id_vpo > 0) {
    // Fetch data for the specified id_vpo from the database
    $resultUniversities = $connection->query("SELECT * FROM vpo WHERE id_vpo = $id_vpo");

    // Check if data is fetched successfully
    if ($resultUniversities->num_rows > 0) {
      $row = $resultUniversities->fetch_assoc();
    } else {
      echo "VPO not found.";
      exit();
    }
  } else {
    echo "Invalid university ID.";
    exit();
  }
} else {
  echo "No id_vpo specified in the URL.";
  exit();
}
?>

<h4 class="text-center mb-3">Редактирование - <span class='text-primary'>
    <?php echo $row['vpo_name']; ?>
  </span>
</h4>
<p class='text-center text-danger'>Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="card">
      <form action="vpo-edit-form-process.php" method="post">
        <input type="hidden" id="id_vpo" name="id_vpo" value="">
        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#mainCollapse"
            aria-expanded="false" aria-controls="mainCollapse">Главная</button>
          <div class="collapse" id="mainCollapse">
            <div class="card-body bg-secondary-subtle">
              <?php
              // Check if the user is an admin
              if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
              ?>
                <div class="row">
                  <div class="col-3">
                    <!-- URL -->
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="parent_vpo_id" name="parent_vpo_id"
                        value="<?php echo $row['parent_vpo_id']; ?>" placeholder="parent_vpo_id"
                        style="font-size: 14px;">
                      <label for="parent_vpo_id">parent_vpo_id</label>
                    </div>
                  </div>
                  <div class="col-9">
                    <!-- URL -->
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="filials_vpo" name="filials_vpo"
                        value="<?php echo $row['filials_vpo']; ?>" placeholder="filials_vpo (через запятую)"
                        style="font-size: 14px;">
                      <label for="filials_vpo">filials_vpo (через запятую)</label>
                    </div>
                  </div>
                </div>
              <?php
              }
              ?>
              <!-- 	vpo_name -->
              <div class="form-floating mb-3">
                <textarea class="form-control" id="vpo_name" name="vpo_name"
                  style="height: 85px; font-size: 14px;" required
                  placeholder="Название"><?php echo $row['vpo_name']; ?></textarea>
                <label for="vpo_name">Название <?php echo requiredAsterisk(); ?></label>
              </div>
              <!-- full_name -->
              <div class="form-floating mb-3">
                <textarea class="form-control" id="full_name" name="full_name" style="height: 85px; font-size: 14px;"
                  required placeholder="Полное название"><?php echo $row['full_name']; ?></textarea>
                <label for="full_name">Полное название <?php echo requiredAsterisk(); ?></label>
              </div>
              <div class="row">
                <div class="col"><!-- short_name -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="short_name" name="short_name"
                      value="<?php echo $row['short_name']; ?>" placeholder="Сокращенное название"
                      style="font-size: 14px;">
                    <label for="short_name">Сокращенное название <?php echo requiredAsterisk(); ?></label>
                  </div>
                </div>
                <div class="col"><!-- name_rod -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name_rod" name="name_rod"
                      value="<?php echo $row['name_rod']; ?>" placeholder="Сокращенное название в родительном падеже"
                      style="font-size: 14px;">
                    <label for="name_rod">Название название в родительном падеже</label>
                  </div>
                </div>
              </div>
              <!-- old_name -->
              <div class="form-floating mb-3">
                <textarea class="form-control" id="old_name" name="old_name" style="height: 85px; font-size: 14px;"
                  placeholder="Прежние названия"><?php echo nl2br($row['old_name']); ?></textarea>
                <label for="old_name">Прежние названия</label>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#contactsCollapse"
            aria-expanded="false" aria-controls="contactsCollapse">Контакты</button>
          <div class="collapse" id="contactsCollapse">
            <div class="card-body bg-secondary-subtle">

              <div class="row gap-3">
                <div class="col"><!-- site -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="site" name="site" value="<?php echo $row['site']; ?>"
                      placeholder="Веб сайт" style="font-size: 14px;">
                    <label for="site">Веб сайт</label>
                  </div>
                </div>
                <div class="col"><!-- email -->
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email"
                      value="<?php echo $row['email']; ?>" placeholder="name@example.ru" style="font-size: 14px;">
                    <label for="email">Email</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-8"><!-- tel -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="tel" name="tel" value="<?php echo $row['tel']; ?>"
                      placeholder="Телефоны" style="font-size: 14px;">
                    <label for="tel">Телефоны</label>
                  </div>
                </div>
                <div class="col"><!-- fax -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="fax" name="fax" value="<?php echo $row['fax']; ?>"
                      placeholder="Факс" style="font-size: 14px;">
                    <label for="fax">Факс</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#priemCollapse"
            aria-expanded="false" aria-controls="priemCollapse">Приемная комиссия</button>
          <div class="collapse" id="priemCollapse">
            <div class="card-body bg-secondary-subtle">
              <!-- site_pk -->
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="site_pk" name="site_pk"
                  value="<?php echo $row['site_pk']; ?>" placeholder="Веб сайт приемной комиссии"
                  style="font-size: 14px;">
                <label for="site_pk">Веб сайт приемной комиссии</label>
              </div>
              <!-- email_pk -->
              <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email_pk" name="email_pk"
                  value="<?php echo $row['email_pk']; ?>" placeholder="name@example.ru" style="font-size: 14px;">
                <label for="email_pk">Email приемной комиссии</label>
              </div>
              <!-- tel_pk -->
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="tel_pk" name="tel_pk" value="<?php echo $row['tel_pk']; ?>"
                  placeholder="Телефон приемной комиссии" style="font-size: 14px;">
                <label for="tel_pk">Телефоны приемной комиссии</label>
              </div>
              <!-- otvetcek -->
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="otvetcek" name="otvetcek"
                  value="<?php echo $row['otvetcek']; ?>" placeholder="Ответственный секретарь приемной комиссии"
                  style="font-size: 14px;">
                <label for="otvetcek">Ответственный секретарь приемной комиссии</label>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#accreditationCollapse"
            aria-expanded="false" aria-controls="accreditationCollapse">Регулирование</button>
          <div class="collapse" id="accreditationCollapse">
            <div class="card-body bg-secondary-subtle">
              <div class="row">
                <div class="col"><!-- accreditation -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="accreditation" name="accreditation"
                      value="<?php echo $row['accreditation']; ?>" placeholder="Аккредитация" style="font-size: 14px;">
                    <label for="accreditation">Аккредитация</label>
                  </div>
                </div>
                <div class="col"><!-- licence -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="licence" name="licence"
                      value="<?php echo $row['licence']; ?>" placeholder="Лицензия" style="font-size: 14px;">
                    <label for="licence">Лицензия</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#directorCollapse"
            aria-expanded="false" aria-controls="directorCollapse">Руководитель</button>
          <div class="collapse" id="directorCollapse">
            <div class="card-body bg-secondary-subtle">
              <div class="row">
                <div class="col-6"><!-- director_role -->
                  <div class="form-floating mb-3">
                    <input list="datalistOptions" type="text" class="form-control" id="director_role"
                      name="director_role" value="<?php echo $row['director_role']; ?>"
                      placeholder="Должность руководителя" style="font-size: 14px;">
                    <label for="director_role">Должность руководителя</label>
                    <datalist id="datalistOptions">
                      <option value="Директор">
                      <option value="и.о директора">
                    </datalist>
                  </div>
                </div>
                <div class="col-6"><!-- director -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="director_name" name="director_name"
                      value="<?php echo $row['director_name']; ?>" placeholder="Фамилия руководителя"
                      style="font-size: 14px;">
                    <label for="director_name">ФИО руководителя</label>
                  </div>
                </div>
              </div>
              <div class="form-floating mb-3">
                <textarea class="form-control" id="director_info" name="director_info"
                  style="height: 85px; font-size: 14px;"
                  placeholder="Научные звания и награды руководителя"><?php echo $row['director_info']; ?></textarea>
                <label for="director_info">Научные звания и награды руководителя</label>
              </div>
              <div class="row">
                <div class="col"><!-- director_phone -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="director_phone" name="director_phone"
                      value="<?php echo $row['director_phone']; ?>" placeholder="Телефон руководителя"
                      style="font-size: 14px;">
                    <label for="director_phone">Телефон приемной руководителя</label>
                  </div>
                </div>
                <div class="col"><!-- director_email -->
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="director_email" name="director_email"
                      value="<?php echo $row['director_email']; ?>" placeholder="name@example.ru"
                      style="font-size: 14px;">
                    <label for="director_email">Email руководителя</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#historyCollapse"
            aria-expanded="false" aria-controls="historyCollapse">История учебного заведения</button>
          <div class="collapse" id="historyCollapse">
            <div class="card-body bg-secondary-subtle">
              <div class="form-floating mb-3">
                <textarea class="form-control" id="history" name="history" style="height: 200px;"
                  placeholder="История учебного заведения"
                  style="font-size: 14px;"><?php echo htmlspecialchars_decode($row['history']); ?></textarea>
                <label for="history">История учебного заведения</label>
              </div>
            </div>
          </div>
        </div>
        <?php
        // Check if the user is an admin
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        ?>
          <div class="row">
            <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#urlCollapse"
              aria-expanded="false" aria-controls="urlCollapse">URL</button>
            <div class="collapse" id="urlCollapse">
              <div class="card-body bg-secondary-subtle">
                <!-- URL -->
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="vpo_url" name="vpo_url"
                    value="<?php echo $row['vpo_url']; ?>" placeholder="URL" style="font-size: 14px;" required>
                  <label for="vpo_url">URL <?php echo requiredAsterisk(); ?></label>
                </div>

              </div>
            </div>
          </div>
        <?php
        }
        ?>
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') { ?>
          <input type="hidden" name="vpo_url" value="<?php echo $row['vpo_url']; ?>">
        <?php } ?>
        <input type="hidden" name="id_vpo" value="<?php echo $row['id_vpo']; ?>">
        <input type="hidden" name="view" value="<?php echo $row['view']; ?>">
        <input type="hidden" name="zip_code" value="<?php echo $row['zip_code']; ?>">
        <input type="hidden" name="id_town" value="<?php echo $row['id_town']; ?>">
        <input type="hidden" name="id_area" value="<?php echo $row['id_area']; ?>">
        <input type="hidden" name="id_region" value="<?php echo $row['id_region']; ?>">
        <input type="hidden" name="id_country" value="<?php echo $row['id_country']; ?>">
        <input type="hidden" name="year" value="<?php echo $row['year']; ?>">
        <input type="hidden" name="street" value="<?php echo $row['street']; ?>">
        <div class="d-flex justify-content-center my-3 gap-3">
          <button id="openAllCollapsesBtn" type="submit" class="submit-button">Обновить
            данные</button>
          <!-- Add a Return button -->
          <button type="button" class="cancel-button" onclick="window.history.back();">Отмена</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  function openAllCollapses() {
    // Array of all collapse IDs
    var allCollapseIDs = ['mainCollapse', 'contactsCollapse', 'priemCollapse', 'accreditationCollapse', 'directorCollapse', 'historyCollapse', 'urlCollapse'];

    // Iterate through all collapses and open them
    allCollapseIDs.forEach(function(collapseID) {
      var collapseElement = document.getElementById(collapseID);
      var collapse = new bootstrap.Collapse(collapseElement, {
        toggle: true
      });
    });
  }

  // Attach the function to the button click event
  document.getElementById('openAllCollapsesBtn').addEventListener('click', openAllCollapses);
</script>