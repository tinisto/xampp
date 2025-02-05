<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateSlug.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/makeUrlFriendly.php";

$occupation = isset($_SESSION['occupation']) ? trim($_SESSION['occupation']) : '';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    // Only apply this check if the user is not an admin
    if ($_SESSION['occupation'] !== 'Представитель школы') {
        // Redirect the user if their occupation does not match
        header("Location: /unauthorized"); // Or any other page you'd like to redirect them to
        exit();
    }
}

// Check if user has a valid role (admin or user)
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get current date for user/admin-specific date field
$currentDate = date("Y-m-d");

// Sanitize and retrieve POST data
$school_name = filter_input(INPUT_POST, 'school_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$short_name = filter_input(INPUT_POST, 'short_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$site = filter_input(INPUT_POST, 'site', FILTER_SANITIZE_URL);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_NUMBER_INT);
$fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_NUMBER_INT);

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
$street = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Fixed overwriting issue

$_SESSION['form_data']['country'] = $id_country;
$_SESSION['form_data']['region'] = $id_region;
$_SESSION['form_data']['area'] = $id_area;
$_SESSION['form_data']['town'] = $id_town;

// Handle image uploads
$uploadDirectory = $_SERVER["DOCUMENT_ROOT"] . "/images/schools-images/";
$temporaryImages = [];

for ($i = 1; $i <= 3; $i++) {
    if (isset($_FILES["image_school_$i"]) && $_FILES["image_school_$i"]["error"] === UPLOAD_ERR_OK) {
        $imageResult = handleImageUpload($_FILES["image_school_$i"], $uploadDirectory);

        if (isset($imageResult["error"])) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error-message'] = $imageResult["error"];
            header("Location: /pages/common/create.php?type=school");
            exit();
        }

        ${"imageSchool$i"} = $imageResult["success"];
        $temporaryImages["image_school_$i"] = $imageResult["success"];
    } else {
        // Retain the existing image if no new image is uploaded
        if (isset($_SESSION['temporary_images']["image_school_$i"])) {
            ${"imageSchool$i"} = $_SESSION['temporary_images']["image_school_$i"];
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

// Set default values for meta fields
$meta_d_school = filter_input(INPUT_POST, 'meta_d_school', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$meta_k_school = filter_input(INPUT_POST, 'meta_k_school', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

// Assign session variables to local variables
$imageSchool1 = $_SESSION['temporary_images']['image_school_1'] ?? null;
$imageSchool2 = $_SESSION['temporary_images']['image_school_2'] ?? null;
$imageSchool3 = $_SESSION['temporary_images']['image_school_3'] ?? null;

// Prepare the SQL query to insert school data
$query = $connection->prepare("
    INSERT INTO schools (
        approved, school_name, full_name, short_name, site, email, tel, fax, director_role, director_name,
        director_info, director_phone, director_email, history, user_id, id_country, id_region,
        id_area, id_town, zip_code, street, image_school_1, image_school_2, image_school_3, meta_d_school, meta_k_school
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Check for query preparation errors
if ($query === false) {
    $_SESSION['error-message'] = "Error preparing the SQL statement: " . $connection->error;
    $_SESSION['form_data'] = $_POST; // Store form data in session
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Declare all variables before binding
$approved = $isAdmin ? 1 : 0; // Set approved status based on user type (admin or user)

// Bind the parameters to the query (all variables should be passed by reference)
$query->bind_param(
    'isssssssssssssiiiiiissssss',  // Adjusted types for 24 placeholders
    $approved,
    $school_name,
    $full_name,
    $short_name,
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
    $imageSchool1,
    $imageSchool2,
    $imageSchool3,
    $meta_d_school,
    $meta_k_school
);

// Execute the query and handle the result
if ($query->execute()) {
    $id_school = $connection->insert_id; // Get the ID of the newly inserted school

    // Rename the images using the new school ID
    for ($i = 1; $i <= 3; $i++) {
        if (isset($temporaryImages["image_school_$i"])) {
            $oldFileName = $uploadDirectory . $temporaryImages["image_school_$i"];
            $newFileName = $uploadDirectory . $id_school . "_" . $i . "." . pathinfo($oldFileName, PATHINFO_EXTENSION);
            if (rename($oldFileName, $newFileName)) {
                ${"imageSchool$i"} = basename($newFileName);
            }
        }
    }

    // Update the database with the new image names
    $updateQuery = $connection->prepare("
        UPDATE schools
        SET image_school_1 = ?, image_school_2 = ?, image_school_3 = ?
        WHERE id_school = ?
    ");

    if ($updateQuery === false) {
        $_SESSION['error-message'] = "Error preparing the update SQL statement: " . $connection->error;
        $_SESSION['form_data'] = $_POST; // Store form data in session
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $updateQuery->bind_param('sssi', $imageSchool1, $imageSchool2, $imageSchool3, $id_school);

    if ($updateQuery->execute()) {
        // Redirect based on the approved status
        if ($approved == 1) {
            header("Location: /school/$id_school");
            exit();
        } else {
            $_SESSION['message'] = "Спасибо за отправку новой информации! Мы скоро с вами свяжемся.";
            header("Location: /thank-you");
            exit();
        }
    } else {
        // Handle query execution error
        $_SESSION['error-message'] = "Error executing the update query: " . $updateQuery->error;
        $_SESSION['form_data'] = $_POST; // Store form data in session
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Close the prepared statement
    $updateQuery->close();
} else {
    // Handle query execution error
    $_SESSION['error-message'] = "Error executing the query: " . $query->error;
    $_SESSION['form_data'] = $_POST; // Store form data in session
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Close the prepared statement
$query->close();
