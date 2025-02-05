<?php 

// Include the transliteration function
include_once __DIR__ . '/transliterateToLatin.php';

function generateUniqueSlugForNews($title, $connection) {
    // Transliterate Cyrillic characters to Latin
    $transliteratedTitle = transliterateToLatin($title);

    // Generate initial slug
    $slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($transliteratedTitle)));
    $slug = rtrim($slug, '-');

    // Check if slug already exists in the database
    $uniqueSlug = $slug;
    $counter = 1;

    while (true) {
        $query = "SELECT COUNT(*) AS count FROM news WHERE url_news = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $uniqueSlug);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            // Slug is unique, break the loop
            break;
        } else {
            // Append a counter to make the slug unique
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }
    }

    return $uniqueSlug;
}
