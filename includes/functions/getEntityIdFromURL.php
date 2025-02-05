<?php
function getEntityIdFromURL($connection, $entityType)
{
    // Define default column name and table based on the entity type
    $columnName = '';
    $table = '';
    $idTable = '';

    switch ($entityType) {
        case 'post':
            $columnName = 'url_post';
            $table = 'posts';
            $idTable = 'post';
            break;
        case 'spo':
            $columnName = 'spo_url';
            $table = 'spo';
            $idTable = 'spo';
            break;
        case 'vpo':
            $columnName = 'vpo_url';
            $table = 'vpo';
            $idTable = 'vpo';
            break;
        case 'school':
            // For school, we directly extract the ID from the URL
            return extractSchoolIdEntityFromURL();
        default:
            // Handle unknown entity types or set a default behavior
            header("Location: /error");
            exit();
    }

    // Get the requested URL path
    $requestPath = $_SERVER['REQUEST_URI'];

    // Split the path into segments
    $pathSegments = explode('/', trim($requestPath, '/'));

    // Initialize variables
    $idEntity = null;

    // Check if the URL matches the expected structure for the entity type
    if (count($pathSegments) >= 2 && $pathSegments[0] === $entityType) {
        // Query the database to find the ID based on the selected column name and table
        $query = "SELECT id_$idTable FROM $table WHERE LOWER(TRIM($columnName)) = LOWER(?)";
        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            header("Location: /error");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $pathSegments[1]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Check if a record is found
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $idEntity);
            mysqli_stmt_fetch($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle the case where the URL structure doesn't match for entities
        $idEntity = null;
        $entityType = null;
    }

    return [
        'id_entity' => $idEntity,
        'entity_type' => $entityType,
    ];
}

function extractSchoolIdEntityFromURL()
{
    $entityType = 'school';

    // Get the requested URL path
    $requestPath = $_SERVER['REQUEST_URI'];

    // Split the path into segments
    $pathSegments = explode('/', trim($requestPath, '/'));

    // Initialize variables
    $idEntity = null;

    // Check if the URL matches the expected structure for 'school' pages
    if (count($pathSegments) >= 2 && $pathSegments[0] === 'school') {
        // The school ID is the second segment
        $idEntity = $pathSegments[1];
    } else {
        // Handle the case where the URL structure doesn't match
        $idEntity = null;
        $entityType = null;
    }

    return [
        'id_entity' => $idEntity,
        'entity_type' => $entityType,
    ];
}

function getEntityNameById($connection, $entityType, $idEntity)
{
    // Define default column name and URL prefix
    $columnName = '';
    $urlPrefix = '';
    $table = '';
    $idTable = '';

    // Determine the column name and URL prefix based on the entity type
    switch ($entityType) {
        case 'post':
            $columnName = 'url_post';
            $urlPrefix = '/post/';
            $table = 'posts';
            $idTable = 'post';
            break;
        case 'spo':
            $columnName = 'spo_url';
            $urlPrefix = '/spo/';
            $table = 'spo';
            $idTable = 'spo';
            break;
        case 'vpo':
            $columnName = 'vpo_url';
            $urlPrefix = '/vpo/';
            $table = 'vpo';
            $idTable = 'vpo';
            break;
        default:
            // Handle unknown entity types or set a default behavior
            header("Location: /error");
            exit();
    }

    // Check if a valid column name is determined
    if ($columnName) {
        // Query the database to find the entity name based on the selected column name and table
        $query = "SELECT $columnName FROM $table WHERE id_$idTable = ?";
        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            header("Location: /error");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $idEntity);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Declare $entityName in this scope
        $entityName = null;

        // Check if a record is found
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $entityName);
            mysqli_stmt_fetch($stmt);

            // Close the statement
            mysqli_stmt_close($stmt);

            // Return the URL prefix and entity name
            return $urlPrefix . $entityName;
        }
    }

    // Handle the case where the column name is not determined or record not found
    return null;
}
?>
