<?php
$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";

// Perform the query
$result = $connection->query($sql);

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
            $count_sql = "SELECT COUNT(*) AS $countField FROM $table WHERE id_region = {$row['id_region']}";
            $count_result = $connection->query($count_sql);

            if (!$count_result) {
                redirectToErrorPage($connection->error, __FILE__, __LINE__);
            }

            $institution_count = $count_result->fetch_assoc()[$countField];

            // Check if $institution_count is greater than 0 before displaying the region
            if ($institution_count > 0):
            ?>
                <div class="col-md-4">
                    <div class="region" data-region-id="<?= $row['id_region'] ?>">
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
</div>

<?php
// Close the database connection after displaying regions
$connection->close();
?>