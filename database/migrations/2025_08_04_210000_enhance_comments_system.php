<?php
/**
 * Migration: EnhanceCommentsSystem
 * Generated: 2025-08-04 21:00:00
 */

class MigrationEnhanceCommentsSystem {
    
    /**
     * Run the migration
     * @param mysqli $connection Database connection
     */
    public function up($connection) {
        // Add new columns to comments table
        $alterCommentsQuery = "ALTER TABLE comments 
                              ADD COLUMN pinned BOOLEAN DEFAULT FALSE AFTER approved,
                              ADD COLUMN report_count INT DEFAULT 0 AFTER pinned,
                              ADD COLUMN moderated_by INT NULL AFTER report_count,
                              ADD COLUMN moderated_at TIMESTAMP NULL AFTER moderated_by";
        
        $connection->query($alterCommentsQuery);
        
        // Create comment_reactions table
        $createReactionsQuery = "CREATE TABLE comment_reactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            comment_id INT NOT NULL,
            user_id INT NOT NULL,
            reaction_type ENUM('like', 'dislike') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_comment_reaction (comment_id, user_id),
            INDEX idx_comment_reactions (comment_id, reaction_type),
            INDEX idx_user_reactions (user_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $connection->query($createReactionsQuery);
        
        // Create comment_reports table
        $createReportsQuery = "CREATE TABLE comment_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            comment_id INT NOT NULL,
            reporter_id INT NOT NULL,
            reason VARCHAR(500) NOT NULL,
            status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            resolved_at TIMESTAMP NULL,
            resolved_by INT NULL,
            FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
            FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_comment_reports_status (status, created_at),
            INDEX idx_comment_reports_comment (comment_id),
            INDEX idx_comment_reports_reporter (reporter_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $connection->query($createReportsQuery);
        
        // Add indexes to existing comments table for better performance
        $addIndexesQuery = "ALTER TABLE comments 
                           ADD INDEX idx_comments_post_approved (post_id, approved, pinned, created_at),
                           ADD INDEX idx_comments_user (user_id, created_at),
                           ADD INDEX idx_comments_moderation (approved, report_count, created_at),
                           ADD INDEX idx_comments_parent (parent_id)";
        
        $connection->query($addIndexesQuery);
        
        // Add foreign key constraint for moderated_by
        $addForeignKeyQuery = "ALTER TABLE comments 
                              ADD CONSTRAINT fk_comments_moderator 
                              FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL";
        
        $connection->query($addForeignKeyQuery);
    }
    
    /**
     * Reverse the migration
     * @param mysqli $connection Database connection
     */
    public function down($connection) {
        // Drop foreign key constraint
        $dropForeignKeyQuery = "ALTER TABLE comments DROP FOREIGN KEY fk_comments_moderator";
        $connection->query($dropForeignKeyQuery);
        
        // Drop indexes from comments table
        $dropIndexesQuery = "ALTER TABLE comments 
                            DROP INDEX idx_comments_post_approved,
                            DROP INDEX idx_comments_user,
                            DROP INDEX idx_comments_moderation,
                            DROP INDEX idx_comments_parent";
        
        $connection->query($dropIndexesQuery);
        
        // Drop comment_reports table
        $dropReportsQuery = "DROP TABLE IF EXISTS comment_reports";
        $connection->query($dropReportsQuery);
        
        // Drop comment_reactions table
        $dropReactionsQuery = "DROP TABLE IF EXISTS comment_reactions";
        $connection->query($dropReactionsQuery);
        
        // Remove new columns from comments table
        $removeColumnsQuery = "ALTER TABLE comments 
                              DROP COLUMN pinned,
                              DROP COLUMN report_count,
                              DROP COLUMN moderated_by,
                              DROP COLUMN moderated_at";
        
        $connection->query($removeColumnsQuery);
    }
}