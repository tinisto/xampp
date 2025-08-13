<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

if (
  $_SESSION['role'] !== 'admin' &&
  $_SESSION['occupation'] !== 'Представитель ВУЗа' &&
  $_SESSION['occupation'] !== 'Представитель ССУЗа' &&
  $_SESSION['occupation'] !== 'Представитель школы'
) {
  header("Location: /unauthorized");
  exit();
}

$occupation = $_SESSION['occupation'];
$metaDescription = "";
$metaKeywords = [];
$additionalData = [];
$mainContent = "";
$pageTitle = "";

if ($_SESSION['role'] === 'admin') {
  $pageTitle = "Админ панель - Создать страницу школы";
  $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/schools/schools-create-form.php";

  // Check if 'type' parameter exists in the URL
  if (isset($_GET['type'])) {
    switch ($_GET['type']) {
      case 'vpo':
        $pageTitle = "Админ панель - Создать страницу VPO";
        $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/create-form.php";
        break;
      case 'spo':
        $pageTitle = "Админ панель - Создать страницу SPO";
        $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/create-form.php";
        break;
      case 'school':
        $pageTitle = "Админ панель - Создать страницу школы";
        $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/schools/schools-create-form.php";
        break;
      default:
        // Redirect to error page if 'type' is not recognized
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
  }
} else {
  // Non-admin role, set title based on occupation
  switch ($occupation) {
    case "Представитель ВУЗа":
      $pageTitle = "Создать страницу ВПО";
      $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/create-form.php";
      break;
    case "Представитель ССУЗа":
      $pageTitle = "Создать страницу СПО";
      $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/create-form.php";
      break;
    case "Представитель школы":
      $pageTitle = "Создать страницу школы";
      $mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/schools/schools-create-form.php";
      break;
    default:
      header("Location: /error");
      exit();
  }
}

include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template.php";
renderTemplate($pageTitle, $mainContent, $additionalData, $metaDescription, $metaKeywords);
