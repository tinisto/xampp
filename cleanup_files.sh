#!/bin/bash

# Move debug, test, and migration files to cleanup directory
CLEANUP_DIR="_cleanup"

echo "Moving debug and test files to $CLEANUP_DIR..."

# Create subdirectories
mkdir -p "$CLEANUP_DIR/debug_files"
mkdir -p "$CLEANUP_DIR/test_files"
mkdir -p "$CLEANUP_DIR/migration_files"
mkdir -p "$CLEANUP_DIR/ftp_scripts"
mkdir -p "$CLEANUP_DIR/backup_files"

# Move debug files
find . -maxdepth 1 -name "debug_*.php" -exec mv {} "$CLEANUP_DIR/debug_files/" \;
find . -maxdepth 1 -name "*_debug.php" -exec mv {} "$CLEANUP_DIR/debug_files/" \;
find . -maxdepth 1 -name "check_*.php" -exec mv {} "$CLEANUP_DIR/debug_files/" \;
find . -maxdepth 1 -name "fix_*.php" -exec mv {} "$CLEANUP_DIR/debug_files/" \;

# Move test files
find . -maxdepth 1 -name "test_*.php" -exec mv {} "$CLEANUP_DIR/test_files/" \;
find . -maxdepth 1 -name "*_test.php" -exec mv {} "$CLEANUP_DIR/test_files/" \;
find . -maxdepth 1 -name "*-test*.php" -exec mv {} "$CLEANUP_DIR/test_files/" \;

# Move migration files
find . -maxdepth 1 -name "migrate_*.php" -exec mv {} "$CLEANUP_DIR/migration_files/" \;
find . -maxdepth 1 -name "migrate_*.py" -exec mv {} "$CLEANUP_DIR/migration_files/" \;
find . -maxdepth 1 -name "migrate-*.php" -exec mv {} "$CLEANUP_DIR/migration_files/" \;
find . -maxdepth 1 -name "*_migration*.php" -exec mv {} "$CLEANUP_DIR/migration_files/" \;

# Move FTP scripts
find . -maxdepth 1 -name "ftp_*.py" -exec mv {} "$CLEANUP_DIR/ftp_scripts/" \;

# Move backup files
find . -maxdepth 1 -name "*.backup" -exec mv {} "$CLEANUP_DIR/backup_files/" \;
find . -maxdepth 1 -name "*.bak" -exec mv {} "$CLEANUP_DIR/backup_files/" \;
find . -maxdepth 1 -name "*.old" -exec mv {} "$CLEANUP_DIR/backup_files/" \;
find . -maxdepth 1 -name "*backup.php" -exec mv {} "$CLEANUP_DIR/backup_files/" \;

# Move other temporary files
mv force_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv update_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv use_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv override_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv compare_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv copy_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv final_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv safe-*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv create_*.php "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv complete_*.sh "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv *.sql "$CLEANUP_DIR/migration_files/" 2>/dev/null
mv *.py "$CLEANUP_DIR/migration_files/" 2>/dev/null

echo "Cleanup complete! Files moved to $CLEANUP_DIR"
echo "Review the files in $CLEANUP_DIR and delete when ready."