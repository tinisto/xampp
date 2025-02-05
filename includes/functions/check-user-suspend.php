<?php
if (!function_exists('getUserSuspensionStatus')) {
  function getUserSuspensionStatus($userEmail)
  {
    global $connection;

    $isSuspended = false;

    $sql = "SELECT is_suspended FROM users WHERE email = ?";
    $stmt = $connection->prepare($sql);

    if (!$stmt) {
      // Handle error
      return false;
    }

    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($isSuspended);
    $stmt->fetch();
    $stmt->close();

    return $isSuspended;
  }
}
