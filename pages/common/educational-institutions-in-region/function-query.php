<?php
function getInstitutions($connection, $region_id, $type, $offset, $limit)
{
    // Determine the sorting column based on the type of institution
    $sortColumn = '';
    switch ($type) {
        case 'schools':
            $sortColumn = 'id_school';
            break;
        case 'spo':
            $sortColumn = 'spo_name';
            break;
        case 'vpo':
            $sortColumn = 'vpo_name';
            break;
        default:
            header("Location: /error");
            exit();
    }

    // Prepare the SQL statement with placeholders
    $query = "SELECT * FROM $type WHERE id_region = ? ORDER BY $sortColumn ASC LIMIT ?, ?";

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
