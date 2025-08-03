<?php
function getEntityIdFromPostURL($connection)
{
    $entity_type = 'post';
    $id_table = 'post';

    // Get the requested URL path
    $requestPath = $_SERVER['REQUEST_URI'];

    // Split the path into segments
    $pathSegments = explode('/', trim($requestPath, '/'));

    // Initialize variables
    $id_entity = null;

    // Check if the URL matches the expected structure for 'post' pages
    if (count($pathSegments) >= 2 && $pathSegments[0] === 'post') {
        // Dynamically select the column name and table based on the entity type
        $column_name = 'url_post';

        // Query the database to find the ID based on the selected column name and table
        $query = "SELECT id_$id_table FROM posts WHERE LOWER(TRIM($column_name)) = LOWER(?)";
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
            mysqli_stmt_bind_result($stmt, $id_entity);
            mysqli_stmt_fetch($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle the case where the URL structure doesn't match for entities
        $id_entity = null;
        $entity_type = null;
    }

    return [
        'id_entity' => $id_entity,
        'entity_type' => $entity_type,
    ];
}

function getEntityIdFromHighEduURL($connection)
{
    $entity_type = 'vpo';
    $id_table = 'universities';

    // Get the requested URL path
    $requestPath = $_SERVER['REQUEST_URI'];

    // Split the path into segments
    $pathSegments = explode('/', trim($requestPath, '/'));

    // Initialize variables
    $id_entity = null;

    // Check if the URL matches the expected structure for 'vpo' pages
    if (count($pathSegments) >= 2 && $pathSegments[0] === 'vpo') {
        // Dynamically select the column name and table based on the entity type
        $column_name = 'url_slug';

        // Query the database to find the ID based on the selected column name and table
        $query = "SELECT id FROM universities WHERE LOWER(TRIM($column_name)) = LOWER(?)";
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
            mysqli_stmt_bind_result($stmt, $id_entity);
            mysqli_stmt_fetch($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle the case where the URL structure doesn't match for entities
        $id_entity = null;
        $entity_type = null;
    }

    return [
        'id_entity' => $id_entity,
        'entity_type' => $entity_type,
    ];
}

function getEntityIdFromMiddleEduURL($connection)
{
    $entity_type = 'spo';
    $id_table = 'colleges';

    // Get the requested URL path
    $requestPath = $_SERVER['REQUEST_URI'];

    // Split the path into segments
    $pathSegments = explode('/', trim($requestPath, '/'));

    // Initialize variables
    $id_entity = null;

    // Check if the URL matches the expected structure for 'spo' pages
    if (count($pathSegments) >= 2 && $pathSegments[0] === 'spo') {
        // Dynamically select the column name and table based on the entity type
        $column_name = 'url_slug';

        // Query the database to find the ID based on the selected column name and table
        $query = "SELECT id FROM colleges WHERE LOWER(TRIM($column_name)) = LOWER(?)";
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
            mysqli_stmt_bind_result($stmt, $id_entity);
            mysqli_stmt_fetch($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle the case where the URL structure doesn't match for entities
        $entity_type = null;
    }

    return [
        'id_entity' => $id_entity,
        'entity_type' => $entity_type,
    ];
}

function extractSchoolIdEntityFromURL()
{
    $entity_type = 'school';

    // Get the requested URL path
    $requestPath = $_SERVER['REQUEST_URI'];

    // Split the path into segments
    $pathSegments = explode('/', trim($requestPath, '/'));

    // Initialize variables
    $id_entity = null;

    // Check if the URL matches the expected structure for 'school' pages
    if (count($pathSegments) >= 2 && $pathSegments[0] === 'school') {
        // The school ID is the second segment
        $id_entity = $pathSegments[1];
    } else {
        // Handle the case where the URL structure doesn't match
        $id_entity = null;
        $entity_type = null;
    }

    return [
        'id_entity' => $id_entity,
        'entity_type' => $entity_type,
    ];
}

function getEntityNameById($connection, $entityType, $id_entity)
{
    // Define default column name and URL prefix
    $column_name = '';
    $urlPrefix = '';

    // Determine the column name and URL prefix based on the entity type
    switch ($entityType) {
        case 'post':
            $column_name = 'url_post';
            $urlPrefix = '/post/';
            $table = 'posts';
            $id_field = 'id_post';
            $status_check = '';
            break;
        case 'spo':
            $column_name = 'url_slug';
            $urlPrefix = '/spo/';
            $table = 'colleges';
            $id_field = 'id';
            $status_check = '';
            break;
        case 'vpo':
            $column_name = 'url_slug';
            $urlPrefix = '/vpo/';
            $table = 'universities';
            $id_field = 'id';
            $status_check = '';
            break;
        default:
            // Handle unknown entity types or set a default behavior
            header("Location: /error");
            exit();
    }

    // Check if a valid column name is determined
    if ($column_name) {
        // Query the database to find the entity name based on the selected column name and table
        $query = "SELECT $column_name FROM $table WHERE $id_field = ? $status_check";
        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            header("Location: /error");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $id_entity);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Declare $entity_name in this scope
        $entity_name = null;

        // Check if a record is found
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $entity_name);
            mysqli_stmt_fetch($stmt);

            // Close the statement
            mysqli_stmt_close($stmt);

            // Return the URL prefix and entity name
            return $urlPrefix . $entity_name;
        }
    }

    // Handle the case where the column name is not determined or record not found
    return null;
}
?>
