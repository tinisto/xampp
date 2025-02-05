<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";

// Check if 'id_school' parameter is present in the URL and is a valid integer
if (isset($_GET['id_school'])) {
  // Retrieve and sanitize the schoolId from the URL
  $schoolId = filter_var($_GET['id_school'], FILTER_SANITIZE_NUMBER_INT);

  // Validate if the sanitized ID is a valid number
  if ($schoolId && is_numeric($schoolId) && $schoolId > 0) {
    // Fetch data for the specified schoolId from the database
    $resultSchools = $connection->query("SELECT * FROM schools WHERE id_school = $schoolId");

    // Check if data is fetched successfully
    if ($resultSchools->num_rows > 0) {
      $row = $resultSchools->fetch_assoc();
    } else {
      echo "School not found.";
      exit();
    }
  } else {
    echo "Invalid school ID.";
    exit();
  }
} else {
  echo "No schoolId specified in the URL.";
  exit();
}

// Check if 'id_school' parameter is present in the URL
if (isset($_GET['id_school'])) {
  // Retrieve and sanitize the schoolId from the URL
  $schoolId = filter_var($_GET['id_school'], FILTER_SANITIZE_NUMBER_INT);

  // Fetch data for the specified schoolId from the database
  // Replace this with your actual database query
  $resultSchools = $connection->query("SELECT * FROM schools WHERE id_school = $schoolId");

  // Check if data is fetched successfully
  if ($resultSchools->num_rows > 0) {
    $row = $resultSchools->fetch_assoc();
  } else {
    echo "School not found.";
  }
} else {
  echo "No schoolId specified in the URL.";
}
?>

<h4 class="text-center mb-3">Редактирование - <span class='text-primary'>
    <?php echo $row['school_name']; ?>
  </span>
</h4>
<p class='text-center text-danger'>Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="card">
      <form action="school-edit-form-process.php" method="post">
        <input type="hidden" id="schoolId" name="schoolId" value="">

        <div class="row">
          <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#mainCollapse"
            aria-expanded="false" aria-controls="mainCollapse">Главная</button>
          <div class="collapse" id="mainCollapse">
            <div class="card-body bg-secondary-subtle">
              <!-- 	school_name -->
              <div class="form-floating mb-3">
                <textarea class="form-control" id="school_name" name="school_name"
                  style="height: 85px; font-size: 14px;" required
                  placeholder="Название"><?php echo $row['school_name']; ?></textarea>
                <label for="school_name">Название <?php echo requiredAsterisk(); ?></label>
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
                <div class="col-6"><!-- director_name -->
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

        <input type="hidden" name="id_school" value="<?php echo $row['id_school']; ?>">
        <input type="hidden" name="view" value="<?php echo $row['view']; ?>">
        <input type="hidden" name="zip_code" value="<?php echo $row['zip_code']; ?>">
        <input type="hidden" name="id_town" value="<?php echo $row['id_town']; ?>">
        <input type="hidden" name="id_area" value="<?php echo $row['id_area']; ?>">
        <input type="hidden" name="id_region" value="<?php echo $row['id_region']; ?>">
        <input type="hidden" name="id_country" value="<?php echo $row['id_country']; ?>">
        <input type="hidden" name="year" value="<?php echo $row['year']; ?>">
        <input type="hidden" name="street" value="<?php echo $row['street']; ?>">

        <div class="d-flex justify-content-center my-3 gap-3">
          <button type="submit" class="submit-button">Обновить данные</button>
          <!-- Add a Return button -->
          <button type="button" class="cancel-button" onclick="window.history.back();">Отмена</button>
        </div>
      </form>
    </div>
  </div>
</div>