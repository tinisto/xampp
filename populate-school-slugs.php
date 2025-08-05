<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Populate School URL Slugs</h1>";

// Include the slug generation function
function generateSchoolSlug($schoolName, $townName = '') {
    // Transliteration mapping
    $translitMap = [
        'А' => 'a', 'а' => 'a', 'Б' => 'b', 'б' => 'b', 'В' => 'v', 'в' => 'v',
        'Г' => 'g', 'г' => 'g', 'Д' => 'd', 'д' => 'd', 'Е' => 'e', 'е' => 'e',
        'Ё' => 'yo', 'ё' => 'yo', 'Ж' => 'zh', 'ж' => 'zh', 'З' => 'z', 'з' => 'z',
        'И' => 'i', 'и' => 'i', 'Й' => 'y', 'й' => 'y', 'К' => 'k', 'к' => 'k',
        'Л' => 'l', 'л' => 'l', 'М' => 'm', 'м' => 'm', 'Н' => 'n', 'н' => 'n',
        'О' => 'o', 'о' => 'o', 'П' => 'p', 'п' => 'p', 'Р' => 'r', 'р' => 'r',
        'С' => 's', 'с' => 's', 'Т' => 't', 'т' => 't', 'У' => 'u', 'у' => 'u',
        'Ф' => 'f', 'ф' => 'f', 'Х' => 'h', 'х' => 'h', 'Ц' => 'ts', 'ц' => 'ts',
        'Ч' => 'ch', 'ч' => 'ch', 'Ш' => 'sh', 'ш' => 'sh', 'Щ' => 'sch', 'щ' => 'sch',
        'Ъ' => '', 'ъ' => '', 'Ы' => 'y', 'ы' => 'y', 'Ь' => '', 'ь' => '',
        'Э' => 'e', 'э' => 'e', 'Ю' => 'yu', 'ю' => 'yu', 'Я' => 'ya', 'я' => 'ya'
    ];
    
    // Clean and normalize the school name
    $name = trim($schoolName);
    
    // Extract school type and number
    $slug = '';
    
    // Check for different school types
    if (preg_match('/средняя.*?общеобразовательная.*?школа.*?№?\s*(\d+)/iu', $name, $matches)) {
        $slug = 'sosh-' . $matches[1];
    } elseif (preg_match('/гимназия.*?№?\s*(\d+)/iu', $name, $matches)) {
        $slug = 'gimnazia-' . $matches[1];
    } elseif (preg_match('/лицей.*?№?\s*(\d+)/iu', $name, $matches)) {
        $slug = 'licey-' . $matches[1];
    } elseif (preg_match('/основная.*?школа.*?№?\s*(\d+)/iu', $name, $matches)) {
        $slug = 'osh-' . $matches[1];
    } elseif (preg_match('/начальная.*?школа.*?№?\s*(\d+)/iu', $name, $matches)) {
        $slug = 'nash-' . $matches[1];
    } elseif (preg_match('/школа.*?№?\s*(\d+)/iu', $name, $matches)) {
        $slug = 'school-' . $matches[1];
    } elseif (strpos($name, 'лицей') !== false) {
        if (strpos($name, 'информационных технологий') !== false) {
            $slug = 'licey-it';
        } elseif (strpos($name, 'экономический') !== false) {
            $slug = 'licey-ekonom';
        } else {
            $slug = 'licey';
        }
    } elseif (strpos($name, 'гимназия') !== false) {
        $slug = 'gimnazia';
    } else {
        // Fallback: use first significant words
        $words = explode(' ', $name);
        $slug = strtolower($words[0]);
        if (isset($words[1]) && is_numeric($words[1])) {
            $slug .= '-' . $words[1];
        }
    }
    
    // Add city name if provided
    if (!empty($townName)) {
        $citySlug = $townName;
        
        // Extract city name from "г. Cityname" format
        if (preg_match('/г\.\s*(.+)/u', $citySlug, $matches)) {
            $citySlug = trim($matches[1]);
        }
        
        // Transliterate city name
        $citySlug = strtr($citySlug, $translitMap);
        $citySlug = strtolower($citySlug);
        $citySlug = preg_replace('/[^a-z0-9]+/', '', $citySlug);
        
        if (!empty($citySlug)) {
            $slug .= '-' . $citySlug;
        }
    }
    
    // Final cleanup
    $slug = strtr($slug, $translitMap);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    return $slug;
}

if (!isset($_POST['populate_slugs'])) {
    echo "<h2>Ready to populate URL slugs for all schools</h2>";
    echo "<p>This will generate friendly URLs for all schools in the database.</p>";
    
    // Show preview of first 10 schools
    $preview_query = "
        SELECT s.id, s.name, t.town_name 
        FROM schools s 
        LEFT JOIN towns t ON s.town_id = t.town_id 
        WHERE s.url_slug IS NULL OR s.url_slug = ''
        ORDER BY s.id 
        LIMIT 10
    ";
    
    $preview_result = $connection->query($preview_query);
    
    if ($preview_result && $preview_result->num_rows > 0) {
        echo "<h3>Preview (first 10 schools):</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>School Name</th><th>Town</th><th>Generated Slug</th></tr>";
        
        while ($row = $preview_result->fetch_assoc()) {
            $slug = generateSchoolSlug($row['name'], $row['town_name']);
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['town_name'] ?? 'N/A') . "</td>";
            echo "<td><code>" . $slug . "</code></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Count total schools without slugs
    $count_result = $connection->query("SELECT COUNT(*) as total FROM schools WHERE url_slug IS NULL OR url_slug = ''");
    $total = $count_result->fetch_assoc()['total'];
    
    echo "<p><strong>Total schools to process: {$total}</strong></p>";
    
    echo "<form method='post'>";
    echo "<p><input type='checkbox' required> I understand this will generate URL slugs for all schools</p>";
    echo "<button type='submit' name='populate_slugs' value='1' style='background: green; color: white; padding: 15px 30px; font-size: 18px; border: none;'>POPULATE SCHOOL SLUGS</button>";
    echo "</form>";
    
} else {
    echo "<h2>🔄 Populating school URL slugs...</h2>";
    
    // Get all schools without slugs
    $query = "
        SELECT s.id, s.name, t.town_name 
        FROM schools s 
        LEFT JOIN towns t ON s.town_id = t.town_id 
        WHERE s.url_slug IS NULL OR s.url_slug = ''
        ORDER BY s.id
    ";
    
    $result = $connection->query($query);
    $processed = 0;
    $errors = 0;
    $duplicates = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $slug = generateSchoolSlug($row['name'], $row['town_name']);
            
            // Check for duplicates and make unique
            $original_slug = $slug;
            $counter = 1;
            while (true) {
                $check_query = "SELECT id FROM schools WHERE url_slug = ? AND id != ?";
                $check_stmt = $connection->prepare($check_query);
                $check_stmt->bind_param("si", $slug, $row['id']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows == 0) {
                    break; // Slug is unique
                }
                
                $slug = $original_slug . '-' . $counter;
                $counter++;
                $check_stmt->close();
            }
            
            // Update the school with the slug
            $update_query = "UPDATE schools SET url_slug = ? WHERE id = ?";
            $update_stmt = $connection->prepare($update_query);
            $update_stmt->bind_param("si", $slug, $row['id']);
            
            if ($update_stmt->execute()) {
                echo "✅ ID {$row['id']}: " . htmlspecialchars($row['name']) . " → <code>{$slug}</code><br>";
                $processed++;
                
                if ($slug !== $original_slug) {
                    $duplicates[] = "Duplicate resolved: {$original_slug} → {$slug}";
                }
            } else {
                echo "❌ ID {$row['id']}: Failed to update - " . $connection->error . "<br>";
                $errors++;
            }
            
            $update_stmt->close();
        }
    }
    
    echo "<h2>📊 Results:</h2>";
    echo "<p>✅ Processed: {$processed}</p>";
    echo "<p>❌ Errors: {$errors}</p>";
    
    if (!empty($duplicates)) {
        echo "<h3>Duplicate slugs resolved:</h3>";
        foreach ($duplicates as $duplicate) {
            echo "<p>⚠️ {$duplicate}</p>";
        }
    }
    
    if ($errors == 0) {
        echo "<h2>🎉 SLUG POPULATION SUCCESSFUL!</h2>";
        echo "<p>All schools now have friendly URL slugs!</p>";
        echo "<p><strong>Next step:</strong> Update templates to use the new field names and URL routing.</p>";
    }
}
?>