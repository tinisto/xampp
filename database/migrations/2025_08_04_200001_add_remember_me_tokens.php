<?php
/**
 * Migration: MigrationAddRememberMeTokens
 * Generated: 2025-08-04 20:00:01
 */

class MigrationAddRememberMeTokens {
    
    /**
     * Run the migration
     * @param mysqli $connection Database connection
     */
    public function up($connection) {
        // Add remember me token columns
        $sql = "ALTER TABLE users 
                ADD COLUMN remember_token VARCHAR(255) NULL AFTER last_login_ip,
                ADD COLUMN remember_token_expires TIMESTAMP NULL AFTER remember_token";
        
        $connection->query($sql);
        
        // Add index for token lookup
        $indexSql = "ALTER TABLE users ADD INDEX idx_remember_token (remember_token, remember_token_expires)";
        $connection->query($indexSql);
    }
    
    /**
     * Reverse the migration
     * @param mysqli $connection Database connection
     */
    public function down($connection) {
        // Remove index first
        $dropIndexSql = "ALTER TABLE users DROP INDEX idx_remember_token";
        $connection->query($dropIndexSql);
        
        // Remove remember me columns
        $sql = "ALTER TABLE users 
                DROP COLUMN remember_token,
                DROP COLUMN remember_token_expires";
        
        $connection->query($sql);
    }
}