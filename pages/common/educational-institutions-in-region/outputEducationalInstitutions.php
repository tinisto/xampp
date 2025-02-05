<?php
function outputInstitutions($institutions_result, $type)
{
    // Add Bootstrap's list-unstyled class to remove bullet points
    echo '<ul class="list-unstyled">';

    while ($institution_row = $institutions_result->fetch_assoc()) {
        echo '<li>';

        // Determine the column names based on the type of institution
        $nameColumn = '';
        $urlColumn = '';
        switch ($type) {
            case 'schools':
                $link = 'school';
                $nameColumn = 'school_name';
                $urlColumn = 'id_school';
                break;
            case 'spo':
                $link = 'spo';
                $nameColumn = 'spo_name';
                $urlColumn = 'spo_url';
                break;
            case 'vpo':
                $link = 'vpo';
                $nameColumn = 'vpo_name';
                $urlColumn = 'vpo_url';
                break;
        }

        // Display the institution name with the link
        echo '<a href="/' . $link . '/' . $institution_row[$urlColumn] . '" class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">' . $institution_row[$nameColumn] . '</a>';

        // Check if user has 'admin' role in session and display the institution URL in bold
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo ' <strong class="font-weight-bold text-dark">' . $institution_row[$urlColumn] . '</strong>';
        }

        echo '</li>';
    }

    echo '</ul>';
}
