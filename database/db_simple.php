<?php
// Simple database connection for local development
try {
    $connection = new mysqli('localhost', 'root', '', '11klassniki_ru');
    
    if ($connection->connect_error) {
        // Fallback - create the connection without error redirect
        $connection = null;
    } else {
        $connection->set_charset('utf8mb4');
    }
} catch (Exception $e) {
    $connection = null;
}
?>