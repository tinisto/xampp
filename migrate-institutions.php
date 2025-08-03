<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$step = $_GET['step'] ?? 'start';

echo "<h1>📚 Migrate Educational Institutions & Content</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .warning { color: orange; }
    .step { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .button { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
    .danger { background: #dc3545; }
    .success-btn { background: #28a745; }
</style>";

// Database connections
$old_db = $connection;
$new_db_user = 'admin_claude';
$new_db_pass = 'Secure9#Klass';
$new_db_name = '11klassniki_new';

// Connect to new database
try {
    $new_db = new mysqli(DB_HOST, $new_db_user, $new_db_pass, $new_db_name);
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
} catch (Exception $e) {
    die("<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>");
}

if ($step === 'start') {
    echo "<div class='step'>";
    echo "<h2>📊 Migration Status</h2>";
    
    // Check what's already migrated
    $tables = [
        'countries' => 'Countries',
        'regions' => 'Regions', 
        'areas' => 'Areas',
        'towns' => 'Towns',
        'categories' => 'Categories',
        'users' => 'Users',
        'universities' => 'Universities (VPO)',
        'colleges' => 'Colleges (SPO)',
        'schools' => 'Schools',
        'news' => 'News',
        'posts' => 'Posts',
        'comments' => 'Comments'
    ];
    
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr><th style='border: 1px solid #ddd; padding: 8px;'>Table</th><th style='border: 1px solid #ddd; padding: 8px;'>Status</th><th style='border: 1px solid #ddd; padding: 8px;'>Records</th></tr>";
    
    foreach ($tables as $table => $name) {
        $count_query = "SELECT COUNT(*) as count FROM $table";
        $result = $new_db->query($count_query);
        $count = $result ? $result->fetch_assoc()['count'] : 0;
        
        $status = $count > 0 ? "<span class='success'>✅ Migrated</span>" : "<span class='error'>❌ Empty</span>";
        $row_style = $count > 0 ? "background: #f0f8ff;" : "background: #fff0f0;";
        
        echo "<tr style='$row_style'>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$name</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$status</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$count</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>🚀 Migrate Empty Tables:</h3>";
    echo "<p class='warning'>⚠️ These tables need migration:</p>";
    echo "<p><a href='?step=vpo' class='button'>1. Migrate Universities (VPO)</a></p>";
    echo "<p><a href='?step=spo' class='button'>2. Migrate Colleges (SPO)</a></p>";
    echo "<p><a href='?step=schools' class='button'>3. Migrate Schools</a></p>";
    echo "<p><a href='?step=news' class='button'>4. Migrate News</a></p>";
    echo "<p><a href='?step=posts' class='button'>5. Migrate Posts</a></p>";
    echo "<p><a href='?step=comments' class='button'>6. Migrate Comments</a></p>";
    echo "</div>";
    exit;
}

switch ($step) {
    case 'vpo':
        echo "<div class='step'>";
        echo "<h3>🎓 Migrating Universities (VPO)</h3>";
        
        // First, check if old table exists
        $check_query = "SHOW TABLES LIKE 'vpo'";
        $check_result = $old_db->query($check_query);
        
        if (!$check_result || $check_result->num_rows == 0) {
            echo "<p class='error'>❌ Table 'vpo' not found in old database</p>";
            echo "<p><a href='?step=spo' class='button'>Skip to Colleges →</a></p>";
            break;
        }
        
        $query = "SELECT * FROM vpo";
        $result = $old_db->query($query);
        $count = 0;
        $errors = 0;
        
        if ($result && $result->num_rows > 0) {
            echo "<p class='info'>Found " . $result->num_rows . " universities to migrate...</p>";
            
            while ($row = $result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT IGNORE INTO universities (
                        id, user_id, parent_university_id, university_name, university_name_genitive, 
                        full_name, short_name, old_names, town_id, area_id, region_id, country_id, 
                        postal_code, street_address, phone, fax, email, website, 
                        director_name, director_role, director_info, director_email, director_phone, 
                        accreditation, license, founding_year, meta_description, meta_keywords, 
                        history, url_slug, image_1, image_2, image_3, vkontakte_url, 
                        view_count, is_approved, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $is_approved = isset($row['approved']) && $row['approved'] ? 1 : 0;
                $parent_id = (isset($row['parent_vpo_id']) && $row['parent_vpo_id'] != 0) ? $row['parent_vpo_id'] : null;
                
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
                
                if ($insert->execute()) {
                    $count++;
                } else {
                    $errors++;
                    if ($errors < 5) { // Show first 5 errors
                        echo "<p class='error'>Error: " . $insert->error . "</p>";
                    }
                }
            }
            
            echo "<p class='success'>✅ Migrated $count universities</p>";
            if ($errors > 0) {
                echo "<p class='warning'>⚠️ $errors errors occurred</p>";
            }
        } else {
            echo "<p class='warning'>No universities found in old database</p>";
        }
        
        echo "<p><a href='?step=spo' class='button success-btn'>Next: Colleges →</a></p>";
        echo "</div>";
        break;
        
    case 'spo':
        echo "<div class='step'>";
        echo "<h3>🏫 Migrating Colleges (SPO)</h3>";
        
        $query = "SELECT * FROM spo";
        $result = $old_db->query($query);
        $count = 0;
        
        if ($result && $result->num_rows > 0) {
            echo "<p class='info'>Found " . $result->num_rows . " colleges to migrate...</p>";
            
            while ($row = $result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT IGNORE INTO colleges (
                        id, user_id, parent_college_id, college_name, college_name_genitive, 
                        full_name, short_name, old_names, town_id, area_id, region_id, country_id, 
                        postal_code, street_address, phone, fax, email, website, 
                        director_name, director_role, director_info, director_email, director_phone, 
                        accreditation, license, founding_year, meta_description, meta_keywords, 
                        history, url_slug, image_1, image_2, image_3, vkontakte_url, 
                        view_count, is_approved, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $is_approved = isset($row['approved']) && $row['approved'] ? 1 : 0;
                $parent_id = (isset($row['parent_spo_id']) && $row['parent_spo_id'] != 0) ? $row['parent_spo_id'] : null;
                
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
                
                if ($insert->execute()) {
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count colleges</p>";
        } else {
            echo "<p class='warning'>No colleges found in old database</p>";
        }
        
        echo "<p><a href='?step=schools' class='button success-btn'>Next: Schools →</a></p>";
        echo "</div>";
        break;
        
    case 'schools':
        echo "<div class='step'>";
        echo "<h3>🏫 Migrating Schools</h3>";
        
        $query = "SELECT * FROM schools";
        $result = $old_db->query($query);
        $count = 0;
        
        if ($result && $result->num_rows > 0) {
            echo "<p class='info'>Found " . $result->num_rows . " schools to migrate...</p>";
            
            while ($row = $result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT IGNORE INTO schools (
                        id, user_id, school_name, full_name, short_name, 
                        town_id, area_id, region_id, country_id, street_address, 
                        phone, fax, email, website, director_name, director_role, 
                        director_info, director_email, director_phone, founding_year, 
                        history, logo_url, image_1, image_2, image_3, 
                        view_count, is_approved, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $is_approved = isset($row['approved']) && $row['approved'] ? 1 : 0;
                $town_id = ($row['id_town'] == 0) ? null : $row['id_town'];
                
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
                
                if ($insert->execute()) {
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count schools</p>";
        } else {
            echo "<p class='warning'>No schools found in old database</p>";
        }
        
        echo "<p><a href='?step=news' class='button success-btn'>Next: News →</a></p>";
        echo "</div>";
        break;
        
    case 'news':
        echo "<div class='step'>";
        echo "<h3>📰 Migrating News</h3>";
        
        $query = "SELECT * FROM news LIMIT 100"; // Start with 100 to avoid timeout
        $result = $old_db->query($query);
        $count = 0;
        
        if ($result && $result->num_rows > 0) {
            echo "<p class='info'>Migrating first 100 news articles...</p>";
            
            while ($row = $result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT IGNORE INTO news (
                        id, user_id, category_id, news_title, news_description, 
                        news_content, news_author, meta_description, meta_keywords, 
                        url_slug, university_id, college_id, school_id, 
                        image_1, image_2, image_3, image_source, 
                        view_count, is_approved, published_at, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $is_approved = isset($row['approved']) && $row['approved'] ? 1 : 0;
                $university_id = ($row['id_vpo'] == 0) ? null : $row['id_vpo'];
                $college_id = ($row['id_spo'] == 0) ? null : $row['id_spo'];
                $school_id = ($row['id_school'] == 0) ? null : $row['id_school'];
                
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
                
                if ($insert->execute()) {
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count news articles</p>";
            
            // Check if there are more
            $total_query = "SELECT COUNT(*) as total FROM news";
            $total_result = $old_db->query($total_query);
            $total = $total_result->fetch_assoc()['total'];
            
            if ($total > 100) {
                echo "<p class='warning'>⚠️ There are $total total news articles. Run migration again for next batch.</p>";
            }
        } else {
            echo "<p class='warning'>No news found in old database</p>";
        }
        
        echo "<p><a href='?step=posts' class='button success-btn'>Next: Posts →</a></p>";
        echo "</div>";
        break;
        
    case 'posts':
        echo "<div class='step'>";
        echo "<h3>📝 Migrating Posts</h3>";
        
        $query = "SELECT * FROM posts";
        $result = $old_db->query($query);
        $count = 0;
        
        if ($result && $result->num_rows > 0) {
            echo "<p class='info'>Found " . $result->num_rows . " posts to migrate...</p>";
            
            while ($row = $result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT IGNORE INTO posts (
                        id, user_id, category_id, post_title, post_description, 
                        post_content, post_author, post_bio, meta_description, 
                        meta_keywords, url_slug, image_1, image_2, image_3, 
                        view_count, is_approved, published_at, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $is_approved = isset($row['approved']) && $row['approved'] ? 1 : 0;
                
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
                
                if ($insert->execute()) {
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count posts</p>";
        } else {
            echo "<p class='warning'>No posts found in old database</p>";
        }
        
        echo "<p><a href='?step=comments' class='button success-btn'>Next: Comments →</a></p>";
        echo "</div>";
        break;
        
    case 'comments':
        echo "<div class='step'>";
        echo "<h3>💬 Migrating Comments</h3>";
        
        // Check if comments table exists in old database
        $check_query = "SHOW TABLES LIKE 'comments'";
        $check_result = $old_db->query($check_query);
        
        if (!$check_result || $check_result->num_rows == 0) {
            echo "<p class='warning'>⚠️ Comments table not found in old database</p>";
            echo "<p class='info'>Looking for comment-related tables...</p>";
            
            // Try to find comment tables
            $tables_query = "SHOW TABLES LIKE '%comment%'";
            $tables_result = $old_db->query($tables_query);
            
            if ($tables_result && $tables_result->num_rows > 0) {
                echo "<p>Found these comment-related tables:</p><ul>";
                while ($table = $tables_result->fetch_array()) {
                    echo "<li>" . $table[0] . "</li>";
                }
                echo "</ul>";
            }
        } else {
            // Migrate comments
            $query = "SELECT * FROM comments LIMIT 500"; // Batch to avoid timeout
            $result = $old_db->query($query);
            $count = 0;
            
            if ($result && $result->num_rows > 0) {
                echo "<p class='info'>Found " . $result->num_rows . " comments to migrate...</p>";
                
                while ($row = $result->fetch_assoc()) {
                    // Determine comment type based on id_entity_type or other field
                    $comment_type = 'news'; // Default to news
                    
                    $insert = $new_db->prepare("
                        INSERT IGNORE INTO comments (
                            user_id, commentable_type, commentable_id, 
                            comment_content, commenter_name, commenter_email, 
                            is_approved, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $is_approved = 1; // Assuming existing comments are approved
                    
                    $insert->bind_param("isisssis", 
                        $row['user_id'], 
                        $comment_type,
                        $row['id_entity'], 
                        $row['text_comment'], 
                        $row['name_user'], 
                        $row['email_user'], 
                        $is_approved, 
                        $row['date_comment']
                    );
                    
                    if ($insert->execute()) {
                        $count++;
                    }
                }
                
                echo "<p class='success'>✅ Migrated $count comments</p>";
            } else {
                echo "<p class='warning'>No comments found to migrate</p>";
            }
        }
        
        echo "<p class='success-btn' style='padding: 20px; margin: 20px 0;'>🎉 All Migrations Complete!</p>";
        echo "<p><a href='?step=start' class='button'>← Check Final Status</a></p>";
        echo "</div>";
        break;
}

echo "<p><a href='?step=start'>← Back to Status</a> | <a href='/'>← Back to Site</a></p>";
?>