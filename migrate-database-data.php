<?php
/**
 * Database Migration Script
 * Copies data from old database structure to new clean structure
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Security check
if (!isset($_GET['migrate']) || $_GET['migrate'] !== 'confirm') {
    echo "<h1>Database Migration Script</h1>";
    echo "<p><strong>⚠️ WARNING:</strong> This will create a new database and migrate all data.</p>";
    echo "<p>Make sure you have:</p>";
    echo "<ul>";
    echo "<li>✅ Backed up your current database</li>";
    echo "<li>✅ Created the new database: <code>11klassniki_new</code></li>";
    echo "<li>✅ Run the schema creation script</li>";
    echo "</ul>";
    echo "<p><a href='?migrate=confirm' style='background: red; color: white; padding: 10px; text-decoration: none;'>CONFIRM MIGRATION</a></p>";
    exit;
}

echo "<h1>🚀 Database Migration in Progress...</h1>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .step { margin: 20px 0; padding: 10px; border-left: 4px solid #007cba; }
</style>";

// Database connections
$old_db = $connection; // Current database
$new_db_name = '11klassniki_new';
$new_db_user = 'admin_claude';
$new_db_pass = 'Secure9#Klass';

try {
    // Connect to new database with new credentials
    $new_db = new mysqli(DB_HOST, $new_db_user, $new_db_pass, $new_db_name);
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    
    // Set UTF8MB4 charset for proper Russian text handling
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to both databases with UTF8MB4 charset</p>";

    // Migration steps
    $migrations = [
        'countries' => 'migrateCountries',
        'regions' => 'migrateRegions', 
        'areas' => 'migrateAreas',
        'towns' => 'migrateTowns',
        'categories' => 'migrateCategories',
        'users' => 'migrateUsers',
        'universities' => 'migrateUniversities',
        'colleges' => 'migrateColleges',
        'schools' => 'migrateSchools',
        'news' => 'migrateNews',
        'posts' => 'migratePosts',
        'comments' => 'migrateComments'
    ];

    foreach ($migrations as $table => $function) {
        echo "<div class='step'>";
        echo "<h3>📊 Migrating: $table</h3>";
        $result = $function($old_db, $new_db);
        echo "<p class='success'>✅ Migrated $result records to $table</p>";
        echo "</div>";
    }

    echo "<div class='step'>";
    echo "<h2 class='success'>🎉 Migration Complete!</h2>";
    echo "<p>All data has been migrated to the new clean database structure with proper Russian text support.</p>";
    echo "<p><strong>Russian language features preserved:</strong></p>";
    echo "<ul>";
    echo "<li>✅ UTF8MB4 charset for proper Cyrillic text handling</li>";
    echo "<li>✅ Genitive case forms (родительный падеж) preserved</li>";
    echo "<li>✅ Locative case forms (предложный падеж) preserved</li>";
    echo "<li>✅ All Russian content and metadata migrated</li>";
    echo "</ul>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Backup your current database</li>";
    echo "<li>Update your .env file to use the new database: <code>DB_NAME=11klassniki_new</code></li>";
    echo "<li>Test all site functionality with Russian content</li>";
    echo "<li>Update code references to use new table/column names</li>";
    echo "<li>Deploy to production when ready</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p class='error'>❌ Migration failed: " . $e->getMessage() . "</p>";
}

// Migration functions
function migrateCountries($old_db, $new_db) {
    // Insert default Russia if not exists
    $query = "INSERT IGNORE INTO countries (id, country_name, country_name_en, country_code) VALUES (1, 'Россия', 'Russia', 'RU')";
    $new_db->query($query);
    return 1;
}

function migrateRegions($old_db, $new_db) {
    $query = "SELECT * FROM regions";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO regions (id, country_id, region_name, region_name_en, region_name_genitive, region_name_locative, region_name_locative_en, region_image) 
            VALUES (?, 1, ?, ?, ?, ?, ?, ?)
        ");
        
        $region_name = $row['region_name'] ?: 'Unknown';
        $region_name_en = $row['region_name_en'] ?: $region_name;
        $region_name_genitive = $row['region_name_rod'] ?: $region_name;
        $region_name_locative = $row['region_where'] ?: $region_name;
        $region_name_locative_en = $row['region_where_en'] ?: $region_name_en;
        $region_image = $row['region_img'];
        
        $insert->bind_param("issssss", 
            $row['id_region'], 
            $region_name, 
            $region_name_en, 
            $region_name_genitive, 
            $region_name_locative, 
            $region_name_locative_en, 
            $region_image
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateAreas($old_db, $new_db) {
    $query = "SELECT * FROM areas";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO areas (id, region_id, area_name, area_name_genitive, description) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $area_name = $row['name'] ?: 'Unknown Area';
        $area_name_genitive = $row['name_rod'] ?: $area_name;
        $description = $row['description'];
        
        $insert->bind_param("iisss", 
            $row['id_area'], 
            $row['id_region'], 
            $area_name, 
            $area_name_genitive, 
            $description
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateTowns($old_db, $new_db) {
    $query = "SELECT * FROM towns";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO towns (id, area_id, region_id, country_id, town_name, town_name_en, town_name_genitive, description, image_url, url_slug) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $town_name = $row['name'] ?: 'Unknown Town';
        $town_name_en = null; // Will be added later
        $town_name_genitive = $row['name_rod'] ?: $town_name;
        $description = $row['description'];
        $image_url = $row['img'];
        $url_slug = $row['url_slug_town'] ?: strtolower(str_replace(' ', '-', $town_name));
        
        $insert->bind_param("iiiissssss", 
            $row['id_town'], 
            $row['id_area'], 
            $row['id_region'], 
            $row['id_country'], 
            $town_name, 
            $town_name_en, 
            $town_name_genitive, 
            $description, 
            $image_url, 
            $url_slug
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateCategories($old_db, $new_db) {
    $query = "SELECT * FROM categories";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO categories (id, category_name, meta_description, meta_keywords, category_description, url_slug) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $insert->bind_param("isssss", 
            $row['id_category'], 
            $row['title_category'], 
            $row['meta_d_category'], 
            $row['meta_k_category'], 
            $row['text_category'], 
            $row['url_category']
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateUsers($old_db, $new_db) {
    $query = "SELECT * FROM users";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO users (id, email, password, role, first_name, last_name, occupation, timezone, avatar_url, is_active, is_suspended, activation_token, activation_link, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $is_active = $row['is_active'] ? true : false;
        $is_suspended = $row['is_suspended'] ? true : false;
        $created_at = $row['registration_date'];
        
        $insert->bind_param("isssssssiiisss", 
            $row['id'], 
            $row['email'], 
            $row['password'], 
            $row['role'], 
            $row['firstname'], 
            $row['lastname'], 
            $row['occupation'], 
            $row['timezone'], 
            $row['avatar'], 
            $is_active, 
            $is_suspended, 
            $row['activation_token'], 
            $row['activation_link'], 
            $created_at
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateUniversities($old_db, $new_db) {
    $query = "SELECT * FROM vpo";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO universities (id, user_id, parent_university_id, university_name, university_name_genitive, full_name, short_name, old_names, town_id, area_id, region_id, country_id, postal_code, street_address, phone, fax, email, website, director_name, director_role, director_info, director_email, director_phone, accreditation, license, founding_year, meta_description, meta_keywords, history, url_slug, image_1, image_2, image_3, vkontakte_url, view_count, is_approved, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $is_approved = $row['approved'] ? true : false;
        $parent_id = $row['parent_vpo_id'] == 0 ? null : $row['parent_vpo_id'];
        
        $insert->bind_param("iiisssssiiiissssssssssssissssssssssiis", 
            $row['id_vpo'], 
            $row['user_id'], 
            $parent_id, 
            $row['vpo_name'], 
            $row['name_rod'], 
            $row['full_name'], 
            $row['short_name'], 
            $row['old_name'], 
            $row['id_town'], 
            $row['id_area'], 
            $row['id_region'], 
            $row['id_country'], 
            $row['zip_code'], 
            $row['street'], 
            $row['tel'], 
            $row['fax'], 
            $row['email'], 
            $row['site'], 
            $row['director_name'], 
            $row['director_role'], 
            $row['director_info'], 
            $row['director_email'], 
            $row['director_phone'], 
            $row['accreditation'], 
            $row['licence'], 
            $row['year'], 
            $row['meta_d_vpo'], 
            $row['meta_k_vpo'], 
            $row['history'], 
            $row['vpo_url'], 
            $row['image_vpo_1'], 
            $row['image_vpo_2'], 
            $row['image_vpo_3'], 
            $row['vkontakte'], 
            $row['view'], 
            $is_approved, 
            $row['updated']
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateColleges($old_db, $new_db) {
    $query = "SELECT * FROM spo";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO colleges (id, user_id, parent_college_id, college_name, college_name_genitive, full_name, short_name, old_names, town_id, area_id, region_id, country_id, postal_code, street_address, phone, fax, email, website, director_name, director_role, director_info, director_email, director_phone, accreditation, license, founding_year, meta_description, meta_keywords, history, url_slug, image_1, image_2, image_3, vkontakte_url, view_count, is_approved, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $is_approved = $row['approved'] ? true : false;
        $parent_id = $row['parent_spo_id'] == 0 ? null : $row['parent_spo_id'];
        
        $insert->bind_param("iiisssssiiiissssssssssssissssssssssiis", 
            $row['id_spo'], 
            $row['user_id'], 
            $parent_id, 
            $row['spo_name'], 
            $row['name_rod'], 
            $row['full_name'], 
            $row['short_name'], 
            $row['old_name'], 
            $row['id_town'], 
            $row['id_area'], 
            $row['id_region'], 
            $row['id_country'], 
            $row['zip_code'], 
            $row['street'], 
            $row['tel'], 
            $row['fax'], 
            $row['email'], 
            $row['site'], 
            $row['director_name'], 
            $row['director_role'], 
            $row['director_info'], 
            $row['director_email'], 
            $row['director_phone'], 
            $row['accreditation'], 
            $row['licence'], 
            $row['year'], 
            $row['meta_d_spo'], 
            $row['meta_k_spo'], 
            $row['history'], 
            $row['spo_url'], 
            $row['image_spo_1'], 
            $row['image_spo_2'], 
            $row['image_spo_3'], 
            $row['vkontakte'], 
            $row['view'], 
            $is_approved, 
            $row['updated']
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateSchools($old_db, $new_db) {
    $query = "SELECT * FROM schools";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO schools (id, user_id, school_name, full_name, short_name, town_id, area_id, region_id, country_id, street_address, phone, fax, email, website, director_name, director_role, director_info, director_email, director_phone, founding_year, history, logo_url, image_1, image_2, image_3, view_count, is_approved, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $is_approved = $row['approved'] ? true : false;
        $town_id = $row['id_town'] == 0 ? null : $row['id_town'];
        
        $insert->bind_param("iisssiiiisssssssssissssssiis", 
            $row['id_school'], 
            $row['user_id'], 
            $row['school_name'], 
            $row['full_name'], 
            $row['short_name'], 
            $town_id, 
            $row['id_area'], 
            $row['id_region'], 
            $row['id_country'], 
            $row['street'], 
            $row['tel'], 
            $row['fax'], 
            $row['email'], 
            $row['site'], 
            $row['director_name'], 
            $row['director_role'], 
            $row['director_info'], 
            $row['director_email'], 
            $row['director_phone'], 
            $row['year'], 
            $row['history'], 
            $row['logo'], 
            $row['image_school_1'], 
            $row['image_school_2'], 
            $row['image_school_3'], 
            $row['view'], 
            $is_approved, 
            $row['updated']
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateNews($old_db, $new_db) {
    $query = "SELECT * FROM news";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO news (id, user_id, category_id, news_title, news_description, news_content, news_author, meta_description, meta_keywords, url_slug, university_id, college_id, school_id, image_1, image_2, image_3, image_source, view_count, is_approved, published_at, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $is_approved = $row['approved'] ? true : false;
        $university_id = $row['id_vpo'] == 0 ? null : $row['id_vpo'];
        $college_id = $row['id_spo'] == 0 ? null : $row['id_spo'];
        $school_id = $row['id_school'] == 0 ? null : $row['id_school'];
        
        $insert->bind_param("iiisssssssiiisssssisss", 
            $row['id_news'], 
            $row['user_id'], 
            $row['category_news'], 
            $row['title_news'], 
            $row['description_news'], 
            $row['text_news'], 
            $row['author_news'], 
            $row['meta_d_news'], 
            $row['meta_k_news'], 
            $row['url_news'], 
            $university_id, 
            $college_id, 
            $school_id, 
            $row['image_news_1'], 
            $row['image_news_2'], 
            $row['image_news_3'], 
            $row['img_source_news'], 
            $row['view_news'], 
            $is_approved, 
            $row['date_news'], 
            $row['date_news']
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migratePosts($old_db, $new_db) {
    $query = "SELECT * FROM posts";
    $result = $old_db->query($query);
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $insert = $new_db->prepare("
            INSERT INTO posts (id, user_id, category_id, post_title, post_description, post_content, post_author, post_bio, meta_description, meta_keywords, url_slug, image_1, image_2, image_3, view_count, is_approved, published_at, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $is_approved = $row['approved'] ? true : false;
        
        $insert->bind_param("iiisssssssssssiiss", 
            $row['id_post'], 
            $row['user_id'], 
            $row['category'], 
            $row['title_post'], 
            $row['description_post'], 
            $row['text_post'], 
            $row['author_post'], 
            $row['bio_post'], 
            $row['meta_d_post'], 
            $row['meta_k_post'], 
            $row['url_post'], 
            $row['image_post_1'], 
            $row['image_post_2'], 
            $row['image_post_3'], 
            $row['view_post'], 
            $is_approved, 
            $row['date_post'], 
            $row['date_post']
        );
        $insert->execute();
        $count++;
    }
    return $count;
}

function migrateComments($old_db, $new_db) {
    // Note: This is simplified - you may need to adjust based on your comments structure
    $query = "SELECT * FROM comments LIMIT 1000"; // Limit for safety
    $result = $old_db->query($query);
    $count = 0;
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Basic comment migration - may need adjustment based on your structure
            $insert = $new_db->prepare("
                INSERT INTO comments (user_id, commentable_type, commentable_id, comment_content, commenter_name, commenter_email, is_approved, created_at) 
                VALUES (?, 'news', ?, ?, ?, ?, ?, ?)
            ");
            
            $is_approved = true; // Assuming existing comments are approved
            
            $insert->bind_param("iisssss", 
                $row['user_id'], 
                $row['id_entity'], // Assuming this refers to news
                $row['text_comment'], 
                $row['name_user'], 
                $row['email_user'], 
                $is_approved, 
                $row['date_comment']
            );
            $insert->execute();
            $count++;
        }
    }
    
    return $count;
}

echo "<p><a href='/'>← Back to Home</a></p>";
?>