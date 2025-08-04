<?php
/**
 * Migration: MigrationAddPasswordResetTokens
 * Generated: 2025-08-04 20:00:02
 */

class MigrationAddPasswordResetTokens {
    
    /**
     * Run the migration
     * @param mysqli $connection Database connection
     */
    public function up($connection) {
        // Add password reset token columns
        $sql = "ALTER TABLE users 
                ADD COLUMN reset_token VARCHAR(255) NULL AFTER remember_token_expires,
                ADD COLUMN reset_token_expires TIMESTAMP NULL AFTER reset_token";
        
        $connection->query($sql);
        
        // Add index for token lookup
        $indexSql = "ALTER TABLE users ADD INDEX idx_reset_token (reset_token, reset_token_expires)";
        $connection->query($indexSql);
    }
    
    /**
     * Reverse the migration
     * @param mysqli $connection Database connection
     */
    public function down($connection) {
        // Remove index first
        $dropIndexSql = "ALTER TABLE users DROP INDEX idx_reset_token";
        $connection->query($dropIndexSql);
        
        // Remove reset token columns
        $sql = "ALTER TABLE users 
                DROP COLUMN reset_token,
                DROP COLUMN reset_token_expires";
        
        $connection->query($sql);
    }
}