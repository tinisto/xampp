<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
$sqlCount = "SELECT COUNT(*) as total FROM users";
$resultCount = $connection->query($sqlCount);
$rowCount = $resultCount->fetch_assoc()["total"];

// Pagination variables
$perPage = 100; // Adjust the number of records per page as needed
$currentPage = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
$offset = ($currentPage - 1) * $perPage;

// Query to fetch paginated results
$sql = "SELECT * FROM users ORDER BY registration_date DESC LIMIT $perPage OFFSET $offset";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered align-middle text-center" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>First</th>";
    echo "<th>Last</th>";
    echo "<th>Email</th>";
    echo "<th>Occupation</th>";
    echo "<th>Timezone</th>";
    echo "<th>Avatar</th>";
    echo "<th>Registration</th>";
    echo "<th>Active</th>";
    echo "<th>Role</th>";

    // Show `is_suspended` column only if the user is NOT an admin
    echo "<th>Suspended</th>";
    echo "<th>Delete</th>";
    echo "<th>Suspend</th>";

    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        // Check role after fetching the row
        $isSuspended = $row["is_suspended"] === "1";
        $rowClass = $isSuspended ? "table-warning" : "";
        $roleClass = $row["role"] === "admin" ? "table-success" : "";

        // If the user is an admin, remove `is_suspended` and span the last 3 columns
        if ($row["role"] === "admin") {
            echo "<tr class=\"$rowClass $roleClass\">";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["firstname"] . "</td>";
            echo "<td>" . $row["lastname"] . "</td>";
            echo "<td><a href='/dashboard/user.php?id=" . $row["id"] . "'>" . $row["email"] . "</a></td>";
            echo "<td>" . $row["occupation"] . "</td>";
            echo "<td>" . $row["timezone"] . "</td>";
            echo "<td>" . $row["avatar"] . "</td>";
            echo "<td>" . $row["registration_date"] . "</td>";
            echo "<td>" . $row["is_active"] . "</td>";
            echo "<td>" . $row["role"] . "</td>";
            echo '<td colspan="3">Admin actions not available</td>';
            echo "</tr>";
        } else {
            // If the user is NOT an admin, show `is_suspended` column
            echo "<tr class=\"$rowClass $roleClass\">";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["firstname"] . "</td>";
            echo "<td>" . $row["lastname"] . "</td>";
            // echo "<td><a href='/dashboard/user.php?id=" . $row["id"] . "'>" . $row["email"] . "</a></td>";
            echo "<td><a href='/pages/dashboard/users-dashboard/user.php?id=" . $row["id"] . "'>" . $row["email"] . "</a></td>";
            echo "<td>" . $row["occupation"] . "</td>";
            echo "<td>" . $row["timezone"] . "</td>";
            echo "<td>" . $row["avatar"] . "</td>";
            echo "<td>" . $row["registration_date"] . "</td>";
            echo "<td>" . $row["is_active"] . "</td>";
            echo "<td>" . $row["role"] . "</td>";
            echo "<td>" . $row["is_suspended"] . "</td>";
            echo '<td><i class="fas fa-trash-alt" onclick="deleteUser(' .
                $row["id"] .
                ', \'' .
                addslashes($row["email"]) .
                '\')" style="color: red; cursor: pointer;"></i></td>';
            echo "<td>";
            if ($isSuspended) {
                // Show "Suspended" message and unsuspend button
                echo "Suspended";
                echo '<i class="fas fa-undo" onclick="unsuspendUser(' .
                    $row["id"] .
                    ', \'' .
                    addslashes($row["email"]) .
                    '\')" style="color: green; cursor: pointer;"></i>';
            } else {
                // Show suspend button
                echo '<i class="fas fa-pause" onclick="suspendUser(' .
                    $row["id"] .
                    ', \'' .
                    addslashes($row["email"]) .
                    '\')" style="color: orange; cursor: pointer;"></i>';
            }
            echo "</td>";
            echo "</tr>";
        }
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    // Pagination links
    $totalPages = ceil($rowCount / $perPage);

    echo '<div class="pagination d-flex justify-content-center mt-3">';
    echo '<ul class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<li class="page-item ' .
            ($i == $currentPage ? "active" : "") .
            '">';
        echo '<a class="page-link" href="?page=' . $i . '">' . $i . "</a>";
        echo "</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "No users found";
}
?>

<script>
    function deleteUser(userId, userEmail) {
        if (confirm('Are you sure you want to delete this user? Email: ' + userEmail)) {
            window.location.href = '/pages/dashboard/users-dashboard/users-view/admin-user-delete.php?id=' + userId;
        }
    }

    function suspendUser(userId, userEmail) {
        if (confirm('Are you sure you want to suspend this user? Email: ' + userEmail)) {
            window.location.href = '/dashboard/admin-user-suspend.php?id=' + userId;
        }
    }

    function unsuspendUser(userId, userEmail) {
        if (confirm('Are you sure you want to unsuspend this user? Email: ' + userEmail)) {
            window.location.href = '/dashboard/admin-user-unsuspend.php?id=' + userId;
        }
    }
</script>