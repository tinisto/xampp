# Import Production Database to Local

## Step 1: Export from Production Server

On your live server, run this command to export the database:

```bash
mysqldump -u your_username -p your_database_name > production_backup.sql
```

Or use phpMyAdmin:
1. Go to your production phpMyAdmin
2. Select your database
3. Click "Export" 
4. Choose "Quick" export method
5. Download the .sql file

## Step 2: Import to Local XAMPP

1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin at http://localhost/phpmyadmin
3. Create a new database with same name as production
4. Click "Import"
5. Choose your production_backup.sql file
6. Click "Go"

## Alternative: Command Line Import

```bash
# Navigate to your MySQL bin directory (XAMPP)
cd /Applications/XAMPP/xamppfiles/bin

# Import the database
./mysql -u root -p your_database_name < /path/to/production_backup.sql
```

## Step 3: Add New Security Tables

After importing, add the new tables we need:

```sql
-- Rate limiting table
CREATE TABLE IF NOT EXISTS `rate_limit_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `attempt_time` datetime NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT 'login',
  PRIMARY KEY (`id`),
  KEY `idx_ip_email_action` (`ip_address`, `email`, `action`),
  KEY `idx_attempt_time` (`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Security logs table
CREATE TABLE IF NOT EXISTS `security_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `event_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Step 4: Update Database Connection

Make sure your local `/database/db_connections.php` has correct local settings:

```php
$servername = "localhost";
$username = "root";  // or your XAMPP MySQL username
$password = "";      // or your XAMPP MySQL password
$dbname = "your_database_name";  // same as production
```

## Step 5: Test Local Site

1. Start PHP server: `php -S localhost:8000`
2. Visit http://localhost:8000
3. Test login, search, comments with real production data
4. Run http://localhost:8000/deployment/test_deployment.php