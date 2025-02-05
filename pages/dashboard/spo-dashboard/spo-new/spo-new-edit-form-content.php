<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";


// Check if 'id_spo' parameter is present in the URL
if (isset($_GET["id_spo"])) {
  // Retrieve and sanitize the collegeId from the URL
  $collegeId = filter_var($_GET["id_spo"], FILTER_SANITIZE_NUMBER_INT);
  $userId = filter_var($_GET["user_id"], FILTER_SANITIZE_NUMBER_INT);

  // Fetch data for the specified collegeId from the database
  // Replace this with your actual database query
  $resultspo = $connection->query(
    "SELECT * FROM spo WHERE id_spo = $collegeId"
  );

  // Check if data is fetched successfully
  if ($resultspo->num_rows > 0) {
    $row = $resultspo->fetch_assoc();
  } else {
    echo "SPO not found.";
  }
} else {
  echo "No collegeId specified in the URL.";
}
?>

<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?><br><span class='text-primary'>
    <?php echo $row["spo_name"]; ?>
  </span>
</h3>
<p class='text-center text-danger'>Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<div class="container mt-4">
  <div class="row justify-content-center">
    <form action="admin-approve-spo-edit-form-process.php" method="post">

      <!-- 	spo_name -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="spo_name" name="spo_name" style="height: 85px; font-size: 14px;"
          required placeholder="Название"><?php echo $row["spo_name"]; ?></textarea>
        <label for="spo_name">Название <?php echo requiredAsterisk(); ?></label>
      </div>

      <!-- full_name -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="full_name" name="full_name" style="height: 85px; font-size: 14px;"
          required placeholder="Полное название"><?php echo $row["full_name"]; ?></textarea>
        <label for="full_name">Полное название <?php echo requiredAsterisk(); ?></label>
      </div>

      <div class="row">
        <div class="col"><!-- short_name -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="short_name" name="short_name"
              value="<?php echo $row["short_name"]; ?>" required placeholder="Сокращенное название"
              style="font-size: 14px;">
            <label for="short_name">Сокращенное название <?php echo requiredAsterisk(); ?></label>
          </div>
        </div>

        <div class="col"><!-- name_rod -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="name_rod" name="name_rod"
              value="<?php echo $row["name_rod"]; ?>" placeholder="Сокращенное название в родительном падеже"
              style="font-size: 14px;">
            <label for="name_rod">Название название в родительном падеже</label>
          </div>
        </div>
      </div>

      <!-- old_name -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="old_name" name="old_name" style="height: 85px; font-size: 14px;"
          placeholder="Прежние названия"><?php echo nl2br(
                                            $row["old_name"]
                                          ); ?></textarea>
        <label for="old_name">Прежние названия</label>
      </div>


      <div class="row">
        <div class="col"><!-- site -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="site" name="site" value="<?php echo $row["site"]; ?>"
              placeholder="Веб сайт" style="font-size: 14px;">
            <label for="site">Веб сайт</label>
          </div>
        </div>
        <div class="col"><!-- email -->
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $row["email"]; ?>"
              placeholder="name@example.ru" style="font-size: 14px;">
            <label for="email">Email</label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-8"><!-- tel -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="tel" name="tel" value="<?php echo $row["tel"]; ?>"
              placeholder="Телефоны" style="font-size: 14px;">
            <label for="tel">Телефоны</label>
          </div>
        </div>
        <div class="col"><!-- fax -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="fax" name="fax" value="<?php echo $row["fax"]; ?>"
              placeholder="Факс" style="font-size: 14px;">
            <label for="fax">Факс</label>
          </div>
        </div>
      </div>

      <!-- site_pk -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="site_pk" name="site_pk" value="<?php echo $row["site_pk"]; ?>"
          placeholder="Веб сайт приемной комиссии" style="font-size: 14px;">
        <label for="site_pk">Веб сайт приемной комиссии</label>
      </div>
      <!-- email_pk -->
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email_pk" name="email_pk"
          value="<?php echo $row["email_pk"]; ?>" placeholder="name@example.ru" style="font-size: 14px;">
        <label for="email_pk">Email приемной комиссии</label>
      </div>
      <!-- tel_pk -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="tel_pk" name="tel_pk" value="<?php echo $row["tel_pk"]; ?>"
          placeholder="Телефон приемной комиссии" style="font-size: 14px;">
        <label for="tel_pk">Телефоны приемной комиссии</label>
      </div>
      <!-- otvetcek -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="otvetcek" name="otvetcek"
          value="<?php echo $row["otvetcek"]; ?>" placeholder="Ответственный секретарь приемной комиссии"
          style="font-size: 14px;">
        <label for="otvetcek">Ответственный секретарь приемной комиссии</label>
      </div>

      <div class="row">
        <div class="col"><!-- accreditation -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="accreditation" name="accreditation"
              value="<?php echo $row["accreditation"]; ?>" placeholder="Аккредитация" style="font-size: 14px;">
            <label for="accreditation">Аккредитация</label>
          </div>
        </div>
        <div class="col"><!-- licence -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="licence" name="licence"
              value="<?php echo $row["licence"]; ?>" placeholder="Лицензия" style="font-size: 14px;">
            <label for="licence">Лицензия</label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-6"><!-- director_role -->
          <div class="form-floating mb-3">
            <input list="datalistOptions" type="text" class="form-control" id="director_role" name="director_role"
              value="<?php echo $row["director_role"]; ?>" placeholder="Должность руководителя"
              style="font-size: 14px;">
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
              value="<?php echo $row["director_name"]; ?>" placeholder="Фамилия руководителя"
              style="font-size: 14px;">
            <label for="director_name">ФИО руководителя</label>
          </div>
        </div>
      </div>
      <div class="form-floating mb-3">
        <textarea class="form-control" id="director_info" name="director_info"
          style="height: 85px; font-size: 14px;"
          placeholder="Научные звания и награды руководителя"><?php echo nl2br(
                                                                $row["director_info"]
                                                              ); ?></textarea>
        <label for="director_info">Научные звания и награды руководителя</label>
      </div>
      <div class="row">
        <div class="col"><!-- director_phone -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="director_phone" name="director_phone"
              value="<?php echo $row["director_phone"]; ?>" placeholder="Телефон руководителя"
              style="font-size: 14px;">
            <label for="director_phone">Телефон приемной руководителя</label>
          </div>
        </div>
        <div class="col"><!-- director_email -->
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="director_email" name="director_email"
              value="<?php echo $row["director_email"]; ?>" placeholder="name@example.ru" style="font-size: 14px;">
            <label for="director_email">Email руководителя</label>
          </div>
        </div>
      </div>

      <div class="form-floating mb-3">
        <textarea class="form-control" id="history" name="history" style="height: 200px;"
          placeholder="История учебного заведения"
          style="font-size: 14px;"><?php echo htmlspecialchars_decode(
                                      $row["history"]
                                    ); ?></textarea>
        <label for="history">История учебного заведения</label>
      </div>

      <!-- URL -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="spo_url" name="spo_url"
          value="<?php echo $row["spo_url"]; ?>" placeholder="URL" style="font-size: 14px;" required>
        <label for="spo_url">URL <?php echo requiredAsterisk(); ?></label>
      </div>

      <div class="d-flex justify-content-center align-items-center mt-3 gap-3">
        <div class="form-check">
          <input
            class="form-check-input"
            type="checkbox"
            name="approveSPO"
            id="approveSPO"
            <?= $row['approved'] == 1 ? 'checked' : ''; ?>>
          <label class="form-check-label text-white" for="approveSPO">Approve</label>
        </div>

        <input type="hidden" name="id_spo" value="<?= $row['id_spo']; ?>">
        <input type="hidden" id="approved" name="approved" value="<?= $row['approved']; ?>">
        <input type="hidden" id="collegeId" name="collegeId" value="">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId); ?>">

        <?= renderButtonBlock("Сохранить изменения", "Отмена", "/dashboard"); ?>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById("approveSPO").addEventListener("change", function() {
    // Update the hidden input value based on checkbox state
    document.getElementById("approved").value = this.checked ? 1 : 0;
  });
</script>