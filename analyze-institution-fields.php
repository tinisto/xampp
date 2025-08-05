<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Institution Tables Field Analysis</h1>";

// Get column info for all three tables
$tables = ['schools', 'spo', 'vpo'];
$all_fields = [];

foreach ($tables as $table) {
    echo "<h2>{$table} table fields:</h2>";
    $result = $connection->query("SHOW COLUMNS FROM {$table}");
    $fields = [];
    echo "<ul>";
    while ($col = $result->fetch_assoc()) {
        $fields[] = $col['Field'];
        echo "<li><strong>" . $col['Field'] . "</strong> (" . $col['Type'] . ")</li>";
    }
    echo "</ul>";
    $all_fields[$table] = $fields;
}

echo "<h2>Field Comparison Analysis</h2>";

// Common fields that should be standardized
$common_concepts = [
    'name' => [
        'schools' => 'school_name',
        'spo' => 'spo_name', 
        'vpo' => 'vpo_name'
    ],
    'images' => [
        'schools' => ['image_school_1', 'image_school_2', 'image_school_3'],
        'spo' => ['image_spo_1', 'image_spo_2', 'image_spo_3'],
        'vpo' => ['image_vpo_1', 'image_vpo_2', 'image_vpo_3']
    ],
    'url_field' => [
        'schools' => 'none (uses id)',
        'spo' => 'spo_url',
        'vpo' => 'vpo_url'
    ]
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Concept</th><th>Schools</th><th>SPO</th><th>VPO</th><th>Suggested Standard</th></tr>";

foreach ($common_concepts as $concept => $fields) {
    echo "<tr>";
    echo "<td><strong>{$concept}</strong></td>";
    
    if ($concept === 'images') {
        echo "<td>" . implode('<br>', $fields['schools']) . "</td>";
        echo "<td>" . implode('<br>', $fields['spo']) . "</td>";
        echo "<td>" . implode('<br>', $fields['vpo']) . "</td>";
        echo "<td>image_1, image_2, image_3</td>";
    } else {
        echo "<td>" . $fields['schools'] . "</td>";
        echo "<td>" . $fields['spo'] . "</td>";
        echo "<td>" . $fields['vpo'] . "</td>";
        
        if ($concept === 'name') {
            echo "<td>name</td>";
        } elseif ($concept === 'url_field') {
            echo "<td>url_slug</td>";
        }
    }
    echo "</tr>";
}

echo "</table>";

echo "<h3>Proposed Standardization:</h3>";
echo "<ul>";
echo "<li><strong>Name fields:</strong> school_name → name, spo_name → name, vpo_name → name</li>";
echo "<li><strong>Image fields:</strong> image_school_X → image_X, image_spo_X → image_X, image_vpo_X → image_X</li>";
echo "<li><strong>URL fields:</strong> Add url_slug to schools table, keep spo_url/vpo_url as url_slug</li>";
echo "</ul>";

echo "<h3>Other common fields found:</h3>";
$common_fields = array_intersect($all_fields['schools'], $all_fields['spo'], $all_fields['vpo']);
echo "<p>Fields that exist in all three tables: " . implode(', ', $common_fields) . "</p>";

// Check for other inconsistencies
echo "<h3>Field Naming Inconsistencies:</h3>";
$patterns = [
    'director' => [],
    'address' => [],
    'contact' => []
];

foreach ($tables as $table) {
    foreach ($all_fields[$table] as $field) {
        if (strpos($field, 'director') !== false) {
            $patterns['director'][$table][] = $field;
        }
        if (strpos($field, 'street') !== false || strpos($field, 'address') !== false) {
            $patterns['address'][$table][] = $field;
        }
        if (strpos($field, 'tel') !== false || strpos($field, 'phone') !== false || strpos($field, 'email') !== false) {
            $patterns['contact'][$table][] = $field;
        }
    }
}

foreach ($patterns as $pattern => $data) {
    if (!empty(array_filter($data))) {
        echo "<h4>{$pattern} fields:</h4>";
        foreach ($data as $table => $fields) {
            if (!empty($fields)) {
                echo "<strong>{$table}:</strong> " . implode(', ', $fields) . "<br>";
            }
        }
    }
}
?>