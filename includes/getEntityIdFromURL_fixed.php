<?php
// Fixed version of getEntityIdFromURL.php that won't redirect to error

function getEntityIdFromURL($connection, $entity_type = '') {
    // Get the current URL
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Default return value
    $result = [
        'id_entity' => 0,
        'entity_type' => $entity_type
    ];
    
    // Extract ID based on URL pattern
    if (preg_match('/\/school\/(\d+)/', $current_url, $matches)) {
        $result['id_entity'] = (int)$matches[1];
        $result['entity_type'] = 'school';
    } elseif (preg_match('/\/vpo\/([^\/]+)/', $current_url, $matches)) {
        // For VPO, need to look up by URL
        $url = mysqli_real_escape_string($connection, $matches[1]);
        $query = "SELECT id_vpo FROM vpo WHERE url_vpo = '$url' LIMIT 1";
        $res = mysqli_query($connection, $query);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $result['id_entity'] = $row['id_vpo'];
            $result['entity_type'] = 'vpo';
        }
    } elseif (preg_match('/\/spo\/([^\/]+)/', $current_url, $matches)) {
        // For SPO, need to look up by URL
        $url = mysqli_real_escape_string($connection, $matches[1]);
        $query = "SELECT id_spo FROM spo WHERE url_spo = '$url' LIMIT 1";
        $res = mysqli_query($connection, $query);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $result['id_entity'] = $row['id_spo'];
            $result['entity_type'] = 'spo';
        }
    } elseif (preg_match('/\/post\/([^\/]+)/', $current_url, $matches)) {
        // For posts, need to look up by URL
        $url = mysqli_real_escape_string($connection, $matches[1]);
        $query = "SELECT id_post FROM posts WHERE url_post = '$url' LIMIT 1";
        $res = mysqli_query($connection, $query);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $result['id_entity'] = $row['id_post'];
            $result['entity_type'] = 'post';
        }
    }
    
    return $result;
}

// For backward compatibility
function getEntityIdFromPostURL($connection) {
    return getEntityIdFromURL($connection, 'post');
}

function getEntityIdFromSchoolURL($connection) {
    return getEntityIdFromURL($connection, 'school');
}

function getEntityIdFromVpoURL($connection) {
    return getEntityIdFromURL($connection, 'vpo');
}

function getEntityIdFromSpoURL($connection) {
    return getEntityIdFromURL($connection, 'spo');
}
?>