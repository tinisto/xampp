<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>School URL Slug Generator</h1>";

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
        // Special liceums without numbers
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

// Test with some examples
echo "<h2>Testing slug generation:</h2>";
$testCases = [
    "Средняя общеобразовательная школа № 1 г. Шимановска",
    "Гимназия № 2 г. Свободный", 
    "Лицей № 3 г. Благовещенска",
    "Основная общеобразовательная школа № 5",
    "Лицей информационных технологий",
    "Начальная школа № 10"
];

foreach ($testCases as $testName) {
    $slug = generateSchoolSlug($testName);
    echo "<p><strong>{$testName}</strong><br>";
    echo "→ <code>/school/{$slug}</code></p>";
}

// Now generate slugs for actual schools in database
echo "<h2>Generating slugs for database schools:</h2>";

// Get schools with town information
$query = "
    SELECT s.id, s.name, t.town_name 
    FROM schools s 
    LEFT JOIN towns t ON s.town_id = t.town_id 
    ORDER BY s.id 
    LIMIT 10
";

$result = $connection->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>School Name</th><th>Town</th><th>Generated Slug</th><th>Full URL</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $slug = generateSchoolSlug($row['name'], $row['town_name']);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['town_name'] ?? 'N/A') . "</td>";
        echo "<td><code>" . $slug . "</code></td>";
        echo "<td><code>/school/" . $slug . "</code></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ Could not fetch schools. Check if migration was run and fields are named correctly.</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Run the institution standardization migration first</li>";
echo "<li>Generate and populate url_slug field for all schools</li>";
echo "<li>Update .htaccess to handle slug-based URLs</li>";
echo "<li>Update templates to use new field names</li>";
echo "</ol>";
?>