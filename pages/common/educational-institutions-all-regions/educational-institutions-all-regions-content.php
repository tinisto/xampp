<?php
// Debug output
echo "<!-- Debug Info:\n";
echo "Table: " . ($table ?? 'NOT SET') . "\n";
echo "Count Field: " . ($countField ?? 'NOT SET') . "\n";
echo "Link Prefix: " . ($linkPrefix ?? 'NOT SET') . "\n";
echo "Page Title: " . ($pageTitle ?? 'NOT SET') . "\n";
echo "Connection: " . (isset($connection) ? 'SET' : 'NOT SET') . "\n";

$sql = "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1 ORDER BY region_name ASC";

// Perform the query
$result = $connection->query($sql);

echo "Query executed: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
if ($result) {
    echo "Regions found: " . $result->num_rows . "\n";
}
echo "-->\n";

if (!$result) {
    header("Location: /error");
    exit();
}
?>

<h5 class="text-center fw-bold mb-3"><?= $pageTitle ?></h5>

<div class="container">
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            // Query to count the number of institutions in the region
            // Use the appropriate column name based on table
            $region_col = ($table == 'schools') ? 'id_region' : 'region_id';
            $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE $region_col = {$row['id']}";
            $count_result = $connection->query($count_sql);

            if (!$count_result) {
                echo "<!-- Count Query Error for region {$row['id']}: " . $connection->error . " -->\n";
                continue;
            }

            $row_data = $count_result->fetch_assoc();
            $institution_count = $row_data['count'] ?? 0;
            echo "<!-- Region {$row['region_name']} (ID: {$row['id']}): {$institution_count} institutions -->\n";

            // Check if $institution_count is greater than 0 before displaying the region
            if ($institution_count > 0):
            ?>
                <div class="col-md-4">
                    <div class="region" data-region-id="<?= $row['id'] ?>">
                        <h6 class="text-lg font-bold">
                            <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>"
                                class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                                <?= $row['region_name'] ?>
                            </a>
                            <span class="badge bg-secondary rounded-pill ms-2"><?= $institution_count ?></span>
                        </h6>
                    </div>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
    <?php 
    // Check if any regions were displayed
    // Use the appropriate column name based on table
    $region_col = ($table == 'schools') ? 'id_region' : 'region_id';
    $displayed_sql = "SELECT COUNT(DISTINCT r.id) as total FROM regions r 
                      INNER JOIN $table i ON r.id = i.$region_col 
                      WHERE r.country_id = 1";
    $displayed_result = mysqli_query($connection, $displayed_sql);
    $total_with_institutions = 0;
    if ($displayed_result) {
        $row = mysqli_fetch_assoc($displayed_result);
        $total_with_institutions = $row['total'];
    }
    
    echo "<!-- Total regions with institutions: $total_with_institutions -->\n";
    
    if ($total_with_institutions == 0): ?>
        <div class="text-center mt-4">
            <p>В данный момент нет доступных учебных заведений.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// Close the database connection after displaying regions
$connection->close();
?>