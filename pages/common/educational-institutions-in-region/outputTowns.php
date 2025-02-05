<?php
function outputTowns($towns_result, $type, $region_name_en)
{
    global $connection; // Use the global connection variable

    // Add Bootstrap's list-unstyled class to remove bullet points
    echo '<ul class="list-unstyled">';

    while ($town_row = $towns_result->fetch_assoc()) {
        echo '<li class="d-flex align-items-center">';

        // Display the town name with the link using the url_slug_town field, region_name_en, and type
        echo '<a href="/' . $type . '/' . $region_name_en . '/' . $town_row['url_slug_town'] . '" class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">' . $town_row['name'] . '</a>';

        // Determine the column name for counting institutions based on the type
        $countColumn = '';
        switch ($type) {
            case 'schools':
                $countColumn = 'id_school';
                break;
            case 'spo':
                $countColumn = 'id_spo';
                break;
            case 'vpo':
                $countColumn = 'id_vpo';
                break;
        }

        // Fetch the quantity of institutions in the town
        $query_institution_count = "SELECT COUNT(*) AS institution_count FROM $type WHERE id_town = ?";
        $stmt_institution_count = mysqli_prepare($connection, $query_institution_count);
        mysqli_stmt_bind_param($stmt_institution_count, "i", $town_row['id_town']);
        mysqli_stmt_execute($stmt_institution_count);
        $result_institution_count = mysqli_stmt_get_result($stmt_institution_count);
        $institution_count_row = mysqli_fetch_assoc($result_institution_count);
        $institution_count = $institution_count_row['institution_count'];

        // Display the quantity of institutions
        echo '<span class="badge bg-secondary rounded-pill ms-2">' . $institution_count . '</span>';

        // Close the statement
        mysqli_stmt_close($stmt_institution_count);

        echo '</li>';
    }

    echo '</ul>';
}
?>
