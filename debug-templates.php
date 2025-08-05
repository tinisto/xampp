<?php
echo "<h1>Template Debug</h1>";

$test_cases = [
    'School ID 2718' => '/pages/school/school-single-simplified.php?id_school=2718',
    'School Slug' => '/pages/school/school-single-simplified.php?url_slug=sosh-1-shimanovsk', 
    'VPO amijt' => '/pages/common/vpo-spo/single-simplified.php?url_slug=amijt&type=vpo',
    'SPO' => '/pages/common/vpo-spo/single-simplified.php?url_slug=belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti&type=spo'
];

foreach ($test_cases as $name => $url) {
    echo "<h2>Testing: {$name}</h2>";
    echo "<p><a href='{$url}' target='_blank'>Direct template: {$url}</a></p>";
    
    // Test the actual query that the template would run
    if (strpos($name, 'School') !== false) {
        if (strpos($name, 'ID') !== false) {
            echo "<p>Query: SELECT * FROM schools WHERE id = 2718</p>";
        } else {
            echo "<p>Query: SELECT * FROM schools WHERE url_slug = 'sosh-1-shimanovsk'</p>";
        }
    } elseif (strpos($name, 'VPO') !== false) {
        echo "<p>Query: SELECT * FROM vpo WHERE url_slug = 'amijt'</p>";
    } elseif (strpos($name, 'SPO') !== false) {
        echo "<p>Query: SELECT * FROM spo WHERE url_slug = 'belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti'</p>";
    }
}

// Test minimal template
echo "<h2>Test Minimal Template</h2>";
echo "<p><a href='/test-minimal-school.php?id=2718' target='_blank'>Minimal School Template</a></p>";
?>