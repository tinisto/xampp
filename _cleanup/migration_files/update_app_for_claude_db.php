<?php
/**
 * Guide for updating application to use 11klassniki_claude database
 */

echo "<h1>📋 Application Update Guide for 11klassniki_claude</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

echo "<h2>1️⃣ Database Connection Update</h2>";
echo "<p>Update your <strong>.env</strong> file:</p>";
echo "<div class='code'>
# OLD DATABASE (comment out or remove)
# DB_NAME=11klassniki_ru

# NEW DATABASE
DB_NAME=11klassniki_claude
DB_USER=admin_claude
DB_PASS=Secure9#Klass
</div>";

echo "<h2>2️⃣ Table Name Mappings</h2>";
echo "<p>Update all queries that reference old table names:</p>";
echo "<table>";
echo "<tr><th>Old Table Name</th><th>New Table Name</th><th>Primary Key</th></tr>";
echo "<tr><td>vpo</td><td class='success'>universities</td><td>id (was id_vpo)</td></tr>";
echo "<tr><td>spo</td><td class='success'>colleges</td><td>id (was id_spo)</td></tr>";
echo "<tr><td>schools</td><td>schools</td><td>id (was id_school)</td></tr>";
echo "<tr><td>areas</td><td>areas</td><td>id (was id_area)</td></tr>";
echo "<tr><td>towns</td><td>towns</td><td>id (was id_town)</td></tr>";
echo "<tr><td>regions</td><td>regions</td><td>id (was id_region)</td></tr>";
echo "</table>";

echo "<h2>3️⃣ Column Name Mappings</h2>";

echo "<h3>Universities (was vpo) - Major Changes:</h3>";
echo "<table>";
echo "<tr><th>Old Column</th><th>New Column</th></tr>";
echo "<tr><td>id_vpo</td><td class='success'>id</td></tr>";
echo "<tr><td>vpo_name</td><td class='success'>university_name</td></tr>";
echo "<tr><td>name_rod</td><td class='success'>university_name_genitive</td></tr>";
echo "<tr><td>id_town</td><td class='success'>town_id</td></tr>";
echo "<tr><td>id_area</td><td class='success'>area_id</td></tr>";
echo "<tr><td>id_region</td><td class='success'>region_id</td></tr>";
echo "<tr><td>id_country</td><td class='success'>country_id</td></tr>";
echo "<tr><td>zip_code</td><td class='success'>postal_code</td></tr>";
echo "<tr><td>street</td><td class='success'>street_address</td></tr>";
echo "<tr><td>tel</td><td class='success'>phone</td></tr>";
echo "<tr><td>site</td><td class='success'>website</td></tr>";
echo "<tr><td>licence</td><td class='success'>license</td></tr>";
echo "<tr><td>year</td><td class='success'>founding_year</td></tr>";
echo "<tr><td>vpo_url</td><td class='success'>url_slug</td></tr>";
echo "<tr><td>view</td><td class='success'>view_count</td></tr>";
echo "<tr><td>approved</td><td class='success'>is_approved</td></tr>";
echo "</table>";

echo "<h3>Colleges (was spo) - Major Changes:</h3>";
echo "<table>";
echo "<tr><th>Old Column</th><th>New Column</th></tr>";
echo "<tr><td>id_spo</td><td class='success'>id</td></tr>";
echo "<tr><td>spo_name</td><td class='success'>college_name</td></tr>";
echo "<tr><td>name_rod</td><td class='success'>college_name_genitive</td></tr>";
echo "<tr><td>spo_url</td><td class='success'>url_slug</td></tr>";
echo "<tr><td>(same pattern as universities)</td><td>...</td></tr>";
echo "</table>";

echo "<h2>4️⃣ Code Examples</h2>";

echo "<h3>OLD Code (VPO query):</h3>";
echo "<div class='code'>
\$query = \"SELECT * FROM vpo WHERE id_vpo = ?\";
\$stmt->bind_param(\"i\", \$_GET['id']);
</div>";

echo "<h3>NEW Code (Universities query):</h3>";
echo "<div class='code'>
\$query = \"SELECT * FROM universities WHERE id = ?\";
\$stmt->bind_param(\"i\", \$_GET['id']);
</div>";

echo "<h3>OLD Code (Join query):</h3>";
echo "<div class='code'>
\$query = \"SELECT v.vpo_name, t.name as town_name 
         FROM vpo v 
         JOIN towns t ON v.id_town = t.id_town 
         WHERE v.id_region = ?\";
</div>";

echo "<h3>NEW Code (Join query):</h3>";
echo "<div class='code'>
\$query = \"SELECT u.university_name, t.town_name 
         FROM universities u 
         JOIN towns t ON u.town_id = t.id 
         WHERE u.region_id = ?\";
</div>";

echo "<h2>5️⃣ Files That Need Updates</h2>";
echo "<p>Key files that likely need updates:</p>";
echo "<ul>";
echo "<li><strong>/pages/common/vpo-spo/</strong> - All files referencing VPO/SPO</li>";
echo "<li><strong>/pages/common/educational-institutions-*/</strong> - Institution listings</li>";
echo "<li><strong>/pages/dashboard/vpo-dashboard/</strong> - Admin VPO management</li>";
echo "<li><strong>/pages/dashboard/spo-dashboard/</strong> - Admin SPO management</li>";
echo "<li><strong>/includes/getEntityIdFromURL.php</strong> - URL parsing logic</li>";
echo "<li><strong>.htaccess</strong> - URL rewrite rules</li>";
echo "</ul>";

echo "<h2>6️⃣ Testing Checklist</h2>";
echo "<ul>";
echo "<li>✓ Update .env to use 11klassniki_claude database</li>";
echo "<li>✓ Test homepage loads correctly</li>";
echo "<li>✓ Test university pages (former VPO)</li>";
echo "<li>✓ Test college pages (former SPO)</li>";
echo "<li>✓ Test search functionality</li>";
echo "<li>✓ Test admin dashboard</li>";
echo "<li>✓ Test news and posts</li>";
echo "<li>✓ Test comments system</li>";
echo "</ul>";

echo "<h2>7️⃣ URL Structure</h2>";
echo "<p>Consider updating URLs for better SEO:</p>";
echo "<table>";
echo "<tr><th>Old URL</th><th>New URL (Recommended)</th></tr>";
echo "<tr><td>/vpo/[id]</td><td>/university/[id] or /universities/[id]</td></tr>";
echo "<tr><td>/spo/[id]</td><td>/college/[id] or /colleges/[id]</td></tr>";
echo "</table>";

echo "<p class='success'><strong>✅ Your new database 11klassniki_claude is ready with clean, consistent structure!</strong></p>";
?>