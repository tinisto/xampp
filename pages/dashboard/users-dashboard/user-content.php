<?php
// Initialize a variable to hold the message
$message = "";

// Check if ID is passed via URL
if (isset($_GET['id'])) {
    $userId = (int) $_GET['id'];

    // Query to fetch user details by ID
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
    } else {
        $message = "User not found!";
    }
} else {
    $message = "No user ID provided!";
}
?>

<div class="d-flex flex-column justify-content-center align-items-center">
    <?php if (!empty($message)): ?>
        <h3 class="text-center text-white mb-3"><?php echo htmlspecialchars($message); ?></h3>
    <?php else: ?>
        <h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>
        <h3 class="text-center text-white mb-3">
            <?php
            if (!empty($user['firstname']) && !empty($user['lastname'])) {
                echo $user['firstname'] . " " . $user['lastname'];
            } else {
                echo "Name not available";
            }
            ?>
        </h3>

        <?php
        // Get the user's avatar URL, fetch the full avatar path, and display the image with styling
        if (isset($user['avatar'])) {
            $avatarUrl = $user['avatar'];
            $avatarPath = getAvatar($avatarUrl);
            echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar" style="width: 150px; height: 150px; border-radius: 50%;">';
        }
        ?>
    <?php endif; ?>
</div>

<?php if (empty($message) && isset($user)): ?>
<table class="table table-bordered mt-3">
    <tr>
        <th>ID</th>
        <td><?php echo $user['id']; ?></td>
    </tr>
    <tr>
        <th>First Name</th>
        <td><?php echo $user['firstname']; ?></td>
    </tr>
    <tr>
        <th>Last Name</th>
        <td><?php echo $user['lastname']; ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?php echo $user['email']; ?></td>
    </tr>
    <tr>
        <th>Occupation</th>
        <td><?php echo $user['occupation']; ?></td>
    </tr>
    <tr>
        <th>Timezone</th>
        <td><?php echo $user['timezone']; ?></td>
    </tr>
    <tr>
        <th>Avatar</th>
        <td><?php echo $user['avatar']; ?></td>
    </tr>
    <tr>
        <th>Registration Date</th>
        <td><?php echo $user['registration_date']; ?></td>
    </tr>
    <tr>
        <th>Active</th>
        <td><?php echo $user['is_active'] == 1 ? 'Yes' : 'No'; ?></td>
    </tr>
    <tr>
        <th>Role</th>
        <td><?php echo $user['role']; ?></td>
    </tr>
    <tr>
        <th>Suspended</th>
        <td><?php echo $user['is_suspended'] == 1 ? 'Yes' : 'No'; ?></td>
    </tr>
</table>
<?php endif; ?>

<a href="/pages/dashboard/users-dashboard/users-view/users-view.php" class="btn btn-primary">Back to User List</a>
