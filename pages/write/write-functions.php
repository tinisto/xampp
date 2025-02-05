<?php

function insertMessage($connection, $userEmail, $message, $userID)
{
  // Escape variables to prevent SQL injection
  $userEmail = mysqli_real_escape_string($connection, $userEmail);
  $message = mysqli_real_escape_string($connection, $message);

  // Prepare the SQL statement
  $sql = "INSERT INTO messages (userEmail, user_id, message)
          VALUES ('$userEmail', '$userID', '$message')";

  // Execute the query
  $result = mysqli_query($connection, $sql);

  // Check for errors
  if (!$result) {
    header("Location: /error");
    exit();
  }
}

?>
