<?php
/**
 * Migration: MigrationAddFailedLoginTracking
 * Generated: 2025-08-04 20:00:00
 */

class MigrationAddFailedLoginTracking {
    
    /**
     * Run the migration
     * @param mysqli $connection Database connection
     */
    public function up($connection) {
        // Add columns for tracking failed login attempts
        $sql = "ALTER TABLE users 
                ADD COLUMN failed_login_attempts INT DEFAULT 0 AFTER password,
                ADD COLUMN last_failed_login TIMESTAMP NULL AFTER failed_login_attempts,
                ADD COLUMN last_login TIMESTAMP NULL AFTER last_failed_login,
                ADD COLUMN last_login_ip VARCHAR(45) NULL AFTER last_login";
        
        $connection->query($sql);
        
        // Add index for performance
        $indexSql = "ALTER TABLE users ADD INDEX idx_failed_attempts (failed_login_attempts, last_failed_login)";
        $connection->query($indexSql);
    }
    
    /**
     * Reverse the migration
     * @param mysqli $connection Database connection
     */
    public function down($connection) {
        // Remove index first
        $dropIndexSql = "ALTER TABLE users DROP INDEX idx_failed_attempts";
        $connection->query($dropIndexSql);
        
        // Remove columns
        $sql = "ALTER TABLE users 
                DROP COLUMN failed_login_attempts,
                DROP COLUMN last_failed_login,
                DROP COLUMN last_login,
                DROP COLUMN last_login_ip";
        
        $connection->query($sql);
    }
}