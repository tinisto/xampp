<?php
function deleteChildComments($parentIds, $connection) {
    if (count($parentIds) > 0) {
        $placeholders = implode(',', array_fill(0, count($parentIds), '?'));
        $deleteChildCommentsQuery = "DELETE FROM comments WHERE parent_id IN ($placeholders)";
        $stmtDeleteChildComments = $connection->prepare($deleteChildCommentsQuery);
        
        // Bind the parameters dynamically based on parentIds array
        $types = str_repeat('i', count($parentIds));  // 'i' for integer type
        $stmtDeleteChildComments->bind_param($types, ...$parentIds);
        
        if ($stmtDeleteChildComments === false) {
            echo "Error preparing child comments query: " . $connection->error;
            return false;
        }

        if (!$stmtDeleteChildComments->execute()) {
            echo "Error executing child comments deletion: " . $stmtDeleteChildComments->error;
            return false;
        }
        
        return true;
    }
    return false;
}