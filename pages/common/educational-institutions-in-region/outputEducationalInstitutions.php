<?php
function outputInstitutions($institutions_result, $type)
{
    // Add Bootstrap's list-unstyled class to remove bullet points
    echo '<ul class="list-unstyled">';

    while ($institution_row = $institutions_result->fetch_assoc()) {
        echo '<li>';

        // Debug ALL schools to see what's in the data
        if ($type === 'schools') {
            echo '<!-- School: ' . $institution_row['school_name'] . ', id_school: ' . (isset($institution_row['id_school']) ? $institution_row['id_school'] : 'NOT SET') . ' -->';
        }

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

        // Get the URL value
        $urlValue = isset($institution_row[$urlColumn]) ? $institution_row[$urlColumn] : null;
        
        // Debug for specific schools
        if ($type === 'schools' && (strpos($institution_row[$nameColumn], 'Восход') !== false || strpos($institution_row[$nameColumn], 'Илезская') !== false)) {
            echo '<!-- School: ' . $institution_row[$nameColumn] . ', URL Column: ' . $urlColumn . ', Value: ' . var_export($urlValue, true) . ', All data: ' . htmlspecialchars(print_r($institution_row, true)) . ' -->';
        }
        
        // Build the link based on type
        if ($type === 'schools') {
            // Force direct access to id_school column
            $schoolId = isset($institution_row['id_school']) ? $institution_row['id_school'] : '';
            
            // Show ID number before school name
            if ($schoolId) {
                echo $schoolId . ' ';
                echo '<a href="/school/' . $schoolId . '" class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">' . html_entity_decode($institution_row[$nameColumn], ENT_QUOTES, 'UTF-8') . '</a>';
            } else {
                // No ID - show without link and number
                echo '??? ' . html_entity_decode($institution_row[$nameColumn], ENT_QUOTES, 'UTF-8');
            }
        } else {
            // For VPO/SPO
            if (!empty($urlValue)) {
                echo '<a href="/' . $link . '/' . htmlspecialchars($urlValue) . '" class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">' . html_entity_decode($institution_row[$nameColumn], ENT_QUOTES, 'UTF-8') . '</a>';
            } else {
                echo '<span class="text-muted">' . html_entity_decode($institution_row[$nameColumn], ENT_QUOTES, 'UTF-8') . '</span>';
            }
        }

        // Check if user has 'admin' role in session and display the institution URL in bold
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo ' <strong class="font-weight-bold text-dark">' . $institution_row[$urlColumn] . '</strong>';
        }

        echo '</li>';
    }

    echo '</ul>';
}
