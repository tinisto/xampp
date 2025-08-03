<?php
/**
 * Fix for regional queries - maps old URL types to correct table names and columns
 */

// This file shows the mapping that needs to be implemented
echo "<h1>Regional Query Mapping Fix</h1>";
echo "<style>
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; margin: 10px 0; }
</style>";

echo "<h2>URL Type to Table Mapping</h2>";
echo "<table>";
echo "<tr><th>URL Type</th><th>Table Name</th><th>Region Column</th><th>Name Column</th><th>URL Column</th></tr>";
echo "<tr><td>vpo</td><td>vpo</td><td>id_region</td><td>vpo_name</td><td>vpo_url</td></tr>";
echo "<tr><td>spo</td><td>spo</td><td>id_region</td><td>spo_name</td><td>spo_url</td></tr>";
echo "<tr><td>schools</td><td>schools</td><td>id_region</td><td>school_name</td><td>id_school</td></tr>";
echo "</table>";

echo "<p><strong>Note:</strong> The new tables (universities, colleges) are empty, so we continue using the old tables.</p>";

echo "<h2>Code Fix Required</h2>";
echo "<div class='code'>";
echo "In educational-institutions-in-region.php, replace direct table usage with:<br><br>";
echo htmlspecialchars('// Map URL type to actual table name
switch ($type) {
    case "vpo":
        $tableName = "vpo";
        $regionColumn = "id_region";
        $nameColumn = "vpo_name";
        $urlColumn = "vpo_url";
        break;
    case "spo":
        $tableName = "spo";
        $regionColumn = "id_region";
        $nameColumn = "spo_name";
        $urlColumn = "spo_url";
        break;
    case "schools":
        $tableName = "schools";
        $regionColumn = "id_region";
        $nameColumn = "school_name";
        $urlColumn = "id_school";
        break;
}

// Then use $tableName instead of $type in queries:
$institutions_query = "SELECT * FROM $tableName WHERE $regionColumn = ? LIMIT $pageOffset, $institutionsPerPage";');
echo "</div>";

echo "<h2>Additional Fixes Needed</h2>";
echo "<ul>";
echo "<li>Update function-query.php to use the correct table names</li>";
echo "<li>Update educational-institutions-all-regions.php to use the old table names</li>";
echo "<li>Update outputEducationalInstitutions.php to handle the correct column names</li>";
echo "</ul>";
?>