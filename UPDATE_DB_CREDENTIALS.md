# Update Database Credentials

## IMPORTANT: Before uploading to production

You need to update the production database credentials in `/database/db_connections.php`

Find these lines (around line 54-57) and replace with your actual production database info:

```php
$username = "your_production_db_user"; // Replace with actual username
$password = "your_production_db_pass"; // Replace with actual password  
$dbname = "your_production_db_name";   // Replace with actual database name
```

## To get your production credentials:

1. Log into your iPage control panel
2. Go to MySQL Databases section
3. Find your database name and username
4. Use the password you set when creating the database

## The automatic detection will:

- Use **local settings** when accessed via:
  - localhost
  - 127.0.0.1
  - Any .local domain
  - Command line (CLI)

- Use **production settings** when accessed via:
  - 11klassniki.ru
  - www.11klassniki.ru
  - Any other domain

## Testing:

In development, add `?debug` to any URL to see which environment is active:
http://localhost:8000/?debug

This will show:
- Environment: development/production
- Host: current hostname
- Database: current database name

## Benefits:

✅ Same code works on both local and production
✅ No need to change files after upload
✅ Automatic environment detection
✅ Different error handling per environment
✅ Secure - production errors are logged, not displayed