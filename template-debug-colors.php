<?php
// This is a debug version of real_template.php to visualize all sections

// Load environment and database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Set page title
$pageTitle = 'Template Debug View';

// Section 1: Title/Header (GREEN)
$greyContent1 = '<div style="background: green !important; color: white; padding: 20px; text-align: center;">
    <h2>Раздел 1: Title/Header (GREEN)</h2>
    <p>This is greyContent1</p>
</div>';

// Section 2: Navigation/Categories (YELLOW)
$greyContent2 = '<div style="background: yellow !important; color: black; padding: 20px; text-align: center;">
    <h2>Раздел 2: Navigation/Breadcrumb (YELLOW)</h2>
    <p>This is greyContent2</p>
</div>';

// Section 3: Metadata (ORANGE)
$greyContent3 = '<div style="background: orange !important; color: black; padding: 20px; text-align: center;">
    <h2>Раздел 3: Metadata/Categories (ORANGE)</h2>
    <p>This is greyContent3</p>
</div>';

// Section 4: Filters/Sorting (PURPLE)
$greyContent4 = '<div style="background: purple !important; color: white; padding: 20px; text-align: center;">
    <h2>Раздел 4: Filters/Sorting (PURPLE)</h2>
    <p>This is greyContent4</p>
</div>';

// Section 5: Main Content (PINK)
$greyContent5 = '<div style="background: pink !important; color: black; padding: 20px; text-align: center;">
    <h2>Раздел 5: Main Content (PINK)</h2>
    <p>This is greyContent5 - Main content goes here</p>
</div>';

// Section 6: Pagination (BROWN)
$greyContent6 = '<div style="background: brown !important; color: white; padding: 20px; text-align: center;">
    <h2>Раздел 6: Pagination (BROWN)</h2>
    <p>This is greyContent6</p>
</div>';

// Comments section (BLUE - stays blue)
$blueContent = '<div style="text-align: center; padding: 20px; margin: 0; color: white;">
    <h2>Comments Section (BLUE)</h2>
    <p>This is blueContent - for comments on posts, schools, etc.</p>
</div>';

// Include the real template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>