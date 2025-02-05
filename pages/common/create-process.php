<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateSlug.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/makeUrlFriendly.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";

$occupation = isset($_SESSION['occupation']) ? trim($_SESSION['occupation']) : '';

if (
    $_SESSION['role'] !== 'admin' &&
    $_SESSION['occupation'] !== 'Представитель ВУЗа' &&
    $_SESSION['occupation'] !== 'Представитель ССУЗа'
) {
    header("Location: /unauthorized");
    exit();
}

// Check if user has a valid role (admin or user)
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get current date for user/admin-specific date field
$currentDate = date("Y-m-d");
$formType = filter_input(INPUT_POST, 'formType', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


// Determine the type of form (SPO or VPO)
$isSPO = $formType === 'spo';
$tableName = $isSPO ? 'spo' : 'vpo';
$urlColumn = $isSPO ? 'spo_url' : 'vpo_url';
$parentColumn = $isSPO ? 'parent_spo_id' : 'parent_vpo_id';
$filialsColumn = $isSPO ? 'filials_spo' : 'filials_vpo';
$name = $isSPO ?  filter_input(INPUT_POST, 'spo_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) :  filter_input(INPUT_POST, 'vpo_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Sanitize and retrieve POST data
$parentID = filter_input(INPUT_POST, $parentColumn, FILTER_VALIDATE_INT);
$filial = filter_input(INPUT_POST, $filialsColumn, FILTER_SANITIZE_FULL_SPECIAL_CHARS);






$full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$short_name = filter_input(INPUT_POST, 'short_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$name_rod = filter_input(INPUT_POST, 'name_rod', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$old_name = filter_input(INPUT_POST, 'old_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$licence = filter_input(INPUT_POST, 'licence', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$accreditation = filter_input(INPUT_POST, 'accreditation', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$site = filter_input(INPUT_POST, 'site', FILTER_SANITIZE_URL);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_NUMBER_INT);
$fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_NUMBER_INT);

$site_pk = filter_input(INPUT_POST, 'site_pk', FILTER_SANITIZE_URL);
$email_pk = filter_input(INPUT_POST, 'email_pk', FILTER_SANITIZE_EMAIL);
$tel_pk = filter_input(INPUT_POST, 'tel_pk', FILTER_SANITIZE_NUMBER_INT);
$otvetcek = filter_input(INPUT_POST, 'otvetcek', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$address_pk = filter_input(INPUT_POST, 'address_pk', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$director_role = filter_input(INPUT_POST, 'director_role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$director_name = filter_input(INPUT_POST, 'director_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$director_info = filter_input(INPUT_POST, 'director_info', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$director_phone = filter_input(INPUT_POST, 'director_phone', FILTER_SANITIZE_NUMBER_INT);

$director_email = filter_input(INPUT_POST, 'director_email', FILTER_SANITIZE_EMAIL);
$history = filter_input(INPUT_POST, 'history', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0; // Default to 0 if not provided

// Retrieve address-related data
$id_country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_NUMBER_INT);
$id_region = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_NUMBER_INT);
$id_area = filter_input(INPUT_POST, 'area', FILTER_SANITIZE_NUMBER_INT);
$id_town = filter_input(INPUT_POST, 'town', FILTER_SANITIZE_NUMBER_INT);
$zip_code = filter_input(INPUT_POST, 'zip_code', FILTER_SANITIZE_NUMBER_INT);
$street = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$vkontakte = filter_input(INPUT_POST, 'vkontakte', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$meta_d = filter_input(INPUT_POST, $isSPO ? 'meta_d_spo' : 'meta_d_vpo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$meta_k = filter_input(INPUT_POST, $isSPO ? 'meta_k_spo' : 'meta_k_vpo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$url = makeUrlFriendly($name);

$_SESSION['form_data']['country'] = $id_country;
$_SESSION['form_data']['region'] = $id_region;
$_SESSION['form_data']['area'] = $id_area;
$_SESSION['form_data']['town'] = $id_town;

$image1 = $_SESSION['temporary_images']["image_" . $formType . "_1"] ?? null;
$image2 = $_SESSION['temporary_images']["image_" . $formType . "_2"] ?? null;
$image3 = $_SESSION['temporary_images']["image_" . $formType . "_3"] ?? null;

// Handle image uploads
$uploadDirectory = $_SERVER["DOCUMENT_ROOT"] . "/images/" . $formType . "-images/";
$temporaryImages = [];

for ($i = 1; $i <= 3; $i++) {
    if (isset($_FILES["image_" . $formType . "_" . $i]) && $_FILES["image_" . $formType . "_" . $i]["error"] === UPLOAD_ERR_OK) {
        $imageResult = handleImageUpload($_FILES["image_" . $formType . "_" . $i], $uploadDirectory);

        if (isset($imageResult["error"])) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error-message'] = $imageResult["error"];
            header("Location: /pages/common/create.php?type=" . $formType);
            exit();
        }

        ${"image" . $i} = $imageResult["success"];
        $temporaryImages["image_" . $formType . "_" . $i] = $imageResult["success"];
    } else {
        // Retain the existing image if no new image is uploaded
        if (isset($_SESSION['temporary_images']["image_" . $formType . "_" . $i])) {
            ${"image" . $i} = $_SESSION['temporary_images']["image_" . $formType . "_" . $i];
        }
    }
}

// Store temporary image paths in session
$_SESSION['temporary_images'] = $temporaryImages;

// Handle image upload function
function handleImageUpload($file, $uploadDirectory)
{
    // Ensure the upload directory exists
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);  // Create the directory if it doesn't exist
    }

    // Validate the uploaded file
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false || !in_array($fileExtension, $allowedExtensions)) {
        return ["error" => "Неверный тип файла изображения. Разрешены только файлы в форматах JPG, PNG и GIF."];
    }

    // Generate a new file name to avoid conflicts
    $newFileName = time() . "_" . basename($file["name"]);
    $imageFilePath = $uploadDirectory . $newFileName;

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($file["tmp_name"], $imageFilePath)) {
        return ["success" => $newFileName];  // Return the new file name on success
    } else {
        return ["error" => "Error uploading the image."];  // Return error if upload fails
    }
}

// Check for duplicate URL
$checkQuery = $connection->prepare("SELECT " . $urlColumn . " FROM " . $tableName . " WHERE " . $urlColumn . " = ?");
$checkQuery->bind_param('s', $url);
$checkQuery->execute();
$checkQuery->store_result();


if ($checkQuery->num_rows > 0) {
    $_SESSION['form_data'] = $_POST;
    $_SESSION['error-message'] = "URL должен быть уникальным. Пожалуйста, выберите другое название.";
    header("Location: /pages/common/create.php?type=" . $formType);
    exit();
}

// Prepare the SQL query to insert data
$query = $connection->prepare("
    INSERT INTO " . $tableName . " (
        approved, " . $parentColumn . ", " . $filialsColumn . ", " . $formType . "_name, full_name, short_name, name_rod, old_name, accreditation, licence, site, email, tel, fax, director_role, director_name,
        director_info, director_phone, director_email, history, user_id, id_country, id_region,
        id_area, id_town, zip_code, street, image_" . $formType . "_1, image_" . $formType . "_2, image_" . $formType . "_3, site_pk, email_pk, tel_pk, otvetcek, address_pk, vkontakte, " . $urlColumn . ", meta_d_" . $formType . ", meta_k_" . $formType . "
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Check for query preparation errors
if ($query === false) {
    $_SESSION['error-message'] = "Error preparing the SQL statement: " . $connection->error;
    header("Location: /pages/common/create.php?type=" . $formType);
    exit();
}

// Declare all variables before binding
$approved = $isAdmin ? 1 : 0; // Set approved status based on user type (admin or user)

// Bind the parameters to the query (all variables should be passed by reference)
$query->bind_param(
    'iissssssssssssssssssiiiiiisssssssssssss',
    $approved,
    $parentID,
    $filial,
    $name,
    $full_name,
    $short_name,
    $name_rod,
    $old_name,
    $accreditation,
    $licence,
    $site,
    $email,
    $tel,
    $fax,
    $director_role,
    $director_name,
    $director_info,
    $director_phone,
    $director_email,
    $history,
    $userId,
    $id_country,
    $id_region,
    $id_area,
    $id_town,
    $zip_code,
    $street,
    $image1,
    $image2,
    $image3,
    $site_pk,
    $email_pk,
    $tel_pk,
    $otvetcek,
    $address_pk,
    $vkontakte,
    $url,
    $meta_d,
    $meta_k
);

// Execute the query and handle the result
if ($query->execute()) {
    // Redirect based on the approved status
    if ($approved == 1) {
        header("Location: /" . $formType . "/" . $url);
        exit();
    } else {
        $_SESSION['message'] = "Спасибо за отправку новой информации! Мы скоро с вами свяжемся.";
        header("Location: /thank-you");
        exit();
    }
} else {
    // Handle query execution error
    $_SESSION['error-message'] = "Error executing the query: " . $query->error;
    header("Location: /pages/common/create.php?type=" . $formType);
    exit();
}

// Close the prepared statement
$query->close();
