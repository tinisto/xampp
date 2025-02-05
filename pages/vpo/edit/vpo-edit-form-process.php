<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// echo "<pre>";
// print_r($_POST);  // This will print the regions array
// echo "</pre>";
// exit();


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get data from the form
  $id_vpo = $_POST['id_vpo'];
  $parent_vpo_id = $_POST['parent_vpo_id'] ?? 0;
  $filials_vpo = $_POST['filials_vpo'] ?? '';
  $directorName = $_POST['director_name'];
  $directorRole = $_POST['director_role'];
  $directorInfo = $_POST['director_info'];
  $vpo_name = $_POST['vpo_name'];
  $nameRod = $_POST['name_rod'];
  $fullName = $_POST['full_name'];
  $shortName = $_POST['short_name'];
  $oldName = $_POST['old_name'];
  $tel = $_POST['tel'];
  $fax = $_POST['fax'];
  $accreditation = $_POST['accreditation'];
  $licence = $_POST['licence'];
  $site = $_POST['site'];
  $email = $_POST['email'];
  $email_pk = $_POST['email_pk'];
  $tel_pk = $_POST['tel_pk'];
  $otvetcek = $_POST['otvetcek'];
  $site_pk = $_POST['site_pk'];
  $directorEmail = $_POST['director_email'];
  $directorPhone = $_POST['director_phone'];
  $history = $_POST['history'];
  $vpo_url = $_POST['vpo_url'];
  // Get the user email from the session
  $user_id = $_SESSION['email'];
  $view = $_POST['view'];
  $zip_code = $_POST['zip_code'];
  $id_town = $_POST['id_town'];
  $id_area = $_POST['id_area'];
  $id_region = $_POST['id_region'];
  $id_country = $_POST['id_country'];
  $year = $_POST['year'];
  $street = $_POST['street'];

  // Validation and sanitation of data if needed

  // Prepare the query to insert into vpo_verification table
  $verificationQuery = $connection->prepare("INSERT INTO vpo_verification (
                                      id_vpo,
                                      parent_vpo_id,
                                      filials_vpo,
                                      director_name,
                                      director_role,
                                      director_info,
                                      director_email,
                                      director_phone,
                                      vpo_name,
                                      name_rod,
                                      full_name,
                                      short_name,
                                      old_name,
                                      tel,
                                      fax,
                                      accreditation,
                                      licence,
                                      site,
                                      email,
                                      email_pk,
                                      tel_pk,
                                      otvetcek,
                                      site_pk,
                                      history,
                                      vpo_url,
                                      user_id,
                                      view,
                                      zip_code,
                                      id_town,
                                      id_area,
                                      id_region,
                                      id_country,
                                      year,
                                      street
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if (!$verificationQuery) {
    header("Location: /error");
    exit();
  }

  // Bind parameters
  $verificationQuery->bind_param(
    "iissssssssssssssssssssiiiiiiss",
    $id_vpo,
    $parent_vpo_id,
    $filials_vpo,
    $directorName,
    $directorRole,
    $directorInfo,
    $directorEmail,
    $directorPhone,
    $vpo_name,
    $nameRod,
    $fullName,
    $shortName,
    $oldName,
    $tel,
    $fax,
    $accreditation,
    $licence,
    $site,
    $email,
    $email_pk,
    $tel_pk,
    $otvetcek,
    $site_pk,
    $history,
    $vpo_url,
    $user_id,
    $view,
    $zip_code,
    $id_town,
    $id_area,
    $id_region,
    $id_country,
    $year,
    $street
  );

  // Execute the query to insert into vpo_verification table
  $verificationResult = $verificationQuery->execute();

  // Check if the insertion was successful
  if ($verificationResult) {
    $subject = $vpo_name . "  - VPO updated";
    $body = "VPO updated<br><br>
    <strong>user: </strong>$user_id<br><br>
    <strong>vpo_name:</strong> $vpo_name<br>
    <strong>name_rod:</strong> $nameRod<br>
    <strong>full_name:</strong> $fullName<br>
    <strong>short_name:</strong> $shortName<br>
    <strong>old_name:</strong> $oldName<br>
    <strong>vpo_url:</strong> $vpo_url<br>
    <strong>site:</strong> $site<br>
    <strong>email:</strong> $email<br>
    <strong>tel:</strong> $tel<br>
    <strong>fax:</strong> $fax<br>
    <strong>site_pk:</strong> $site_pk<br>
    <strong>email_pk:</strong> $email_pk<br>
    <strong>tel_pk:</strong> $tel_pk<br>
    <strong>otvetcek:</strong> $otvetcek<br>
    <strong>accreditation:</strong> $accreditation<br>
    <strong>licence:</strong> $licence<br>
    <strong>director_role:</strong> $directorRole<br>
    <strong>director_name:</strong> $directorName<br>
    <strong>director_info:</strong> $directorInfo<br>
    <strong>director_phone:</strong> $directorPhone<br>
    <strong>director_email:</strong> $directorEmail<br>
    <strong>history:</strong> $history<br>";

    sendToAdmin($subject, $body);

    // Dynamically redirect before echoing any content
    header("Location: thank-you?message=Ваш запрос успешно отправлен и находится в процессе проверки.");
    exit(); // It's crucial to exit the script after redirection
  } else {
    // Handle error if the query execution fails
    error_log("Error updating universities: " . $verificationQuery->error);  // Log the error to a file
    header("Location: /error");
    exit();
  }

  // Close the query for the vpo_verification table
  $verificationQuery->close();
} else {
  // If the form is not submitted, handle accordingly (you can add a message or log entry)
  header("Location: /error");
  exit();
}
