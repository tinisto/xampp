<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Security check - only allow this in specific cases
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo "<h1>News Approval Fix</h1>";
    echo "<p>This script will approve all news articles in the database.</p>";
    echo "<p><strong>Warning:</strong> This will set approved=1 for all news.</p>";
    echo "<p><a href='?confirm=yes' style='background: red; color: white; padding: 10px; text-decoration: none;'>CONFIRM: Approve All News</a></p>";
    exit;
}

echo "<h1>Fixing News Approval</h1>";

// Check database connection
if (!isset($connection)) {
    die("❌ Database connection not available");
}

echo "✅ Database connection active<br>";

// Count current news
$countQuery = "SELECT COUNT(*) as total FROM news";
$countResult = mysqli_query($connection, $countQuery);
$totalNews = mysqli_fetch_assoc($countResult)['total'];

$approvedQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
$approvedResult = mysqli_query($connection, $approvedQuery);
$approvedNews = mysqli_fetch_assoc($approvedResult)['total'];

echo "<h2>Before Fix:</h2>";
echo "Total news: <strong>$totalNews</strong><br>";
echo "Approved news: <strong>$approvedNews</strong><br>";

if ($totalNews > 0 && $approvedNews < $totalNews) {
    // Approve all news
    $updateQuery = "UPDATE news SET approved = 1 WHERE approved = 0";
    $updateResult = mysqli_query($connection, $updateQuery);
    
    if ($updateResult) {
        $affectedRows = mysqli_affected_rows($connection);
        echo "<h2>✅ Success!</h2>";
        echo "Approved <strong>$affectedRows</strong> news articles<br>";
        
        // Check after update
        $newApprovedResult = mysqli_query($connection, $approvedQuery);
        $newApproved = mysqli_fetch_assoc($newApprovedResult)['total'];
        echo "Now approved: <strong>$newApproved</strong><br>";
        
        echo "<p><a href='/news'>✅ View News Page</a></p>";
    } else {
        echo "❌ Update failed: " . mysqli_error($connection);
    }
} else {
    echo "<p>No action needed - all news already approved or no news found.</p>";
}

echo "<p><a href='/news'>← Back to News</a></p>";
?>