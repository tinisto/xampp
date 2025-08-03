<?php
function getInstitutions($connection, $region_id, $type, $offset, $limit)
{
    // Map type to actual table names and columns
    switch ($type) {
        case 'schools':
            $tableName = 'schools';
            $regionColumn = 'id_region';
            $sortColumn = 'id_school';
            break;
        case 'spo':
            $tableName = 'spo';  // Use old table since new is empty
            $regionColumn = 'id_region';
            $sortColumn = 'spo_name';
            break;
        case 'vpo':
            $tableName = 'vpo';  // Use old table since new is empty
            $regionColumn = 'id_region';
            $sortColumn = 'vpo_name';
            break;
        default:
            header("Location: /error");
            exit();
    }

    // Prepare the SQL statement with placeholders
    $query = "SELECT * FROM $tableName WHERE $regionColumn = ? ORDER BY $sortColumn ASC LIMIT ?, ?";

    // Prepare the statement
    $stmt = $connection->prepare($query);

    // Check if the prepare() method was successful
    if ($stmt === false) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters
    $stmt->bind_param("iii", $region_id, $offset, $limit);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if (!$result) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    return $result;
}
?>
