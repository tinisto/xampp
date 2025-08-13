<?php
// Redirect to the actual database connection file
// This file exists for backward compatibility
require_once __DIR__ . '/db_connections.php';

// Create helper functions that were expected in db_modern.php
if (!function_exists('db_fetch_all')) {
    function db_fetch_all($query, $params = []) {
        global $connection;
        $stmt = $connection->prepare($query);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

if (!function_exists('db_fetch_one')) {
    function db_fetch_one($query, $params = []) {
        global $connection;
        $stmt = $connection->prepare($query);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

if (!function_exists('db_fetch_column')) {
    function db_fetch_column($query, $params = []) {
        global $connection;
        $stmt = $connection->prepare($query);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        return $row ? $row[0] : null;
    }
}
?>