<?php
/**
 * Database Migration Manager for 11klassniki
 * Handles database schema changes and versioning
 */

class MigrationManager {
    
    private $connection;
    private $migrationsTable = 'migrations';
    private $migrationsDir;
    
    public function __construct($connection) {
        $this->connection = $connection;
        $this->migrationsDir = $_SERVER['DOCUMENT_ROOT'] . '/database/migrations';
        $this->ensureMigrationsTable();
    }
    
    /**
     * Run pending migrations
     * @return array Results of migration execution
     */
    public function migrate() {
        $this->ensureMigrationsDirectory();
        
        $pendingMigrations = $this->getPendingMigrations();
        $results = [];
        
        foreach ($pendingMigrations as $migration) {
            $results[] = $this->runMigration($migration);
        }
        
        return $results;
    }
    
    /**
     * Rollback last migration
     * @return array Rollback result
     */
    public function rollback() {
        $lastMigration = $this->getLastMigration();
        
        if (!$lastMigration) {
            return ['success' => false, 'message' => 'No migrations to rollback'];
        }
        
        return $this->rollbackMigration($lastMigration);
    }
    
    /**
     * Rollback to specific migration
     * @param string $targetMigration Target migration name
     * @return array Rollback results
     */
    public function rollbackTo($targetMigration) {
        $migrationsToRollback = $this->getMigrationsToRollback($targetMigration);
        $results = [];
        
        foreach (array_reverse($migrationsToRollback) as $migration) {
            $results[] = $this->rollbackMigration($migration);
        }
        
        return $results;
    }
    
    /**
     * Get migration status
     * @return array Migration status information
     */
    public function getStatus() {
        $allMigrations = $this->getAllMigrationFiles();
        $appliedMigrations = $this->getAppliedMigrations();
        $pendingMigrations = $this->getPendingMigrations();
        
        return [
            'total_migrations' => count($allMigrations),
            'applied_migrations' => count($appliedMigrations),
            'pending_migrations' => count($pendingMigrations),
            'migrations' => array_map(function($migration) use ($appliedMigrations) {
                return [
                    'name' => $migration,
                    'applied' => in_array($migration, $appliedMigrations),
                    'applied_at' => $this->getMigrationAppliedAt($migration)
                ];
            }, $allMigrations)
        ];
    }
    
    /**
     * Create new migration file
     * @param string $name Migration name
     * @param string $type Migration type (create_table, alter_table, etc.)
     * @return string Migration file path
     */
    public function createMigration($name, $type = 'general') {
        $timestamp = date('Y_m_d_His');
        $className = $this->formatClassName($name);
        $filename = "{$timestamp}_{$name}.php";
        $filepath = $this->migrationsDir . '/' . $filename;
        
        $template = $this->getMigrationTemplate($className, $type);
        
        file_put_contents($filepath, $template);
        
        return $filepath;
    }
    
    /**
     * Refresh migrations (rollback all and re-run)
     * WARNING: This will lose all data!
     * @return array Refresh results
     */
    public function refresh() {
        $results = ['rollbacks' => [], 'migrations' => []];
        
        // Rollback all migrations
        $appliedMigrations = $this->getAppliedMigrations();
        foreach (array_reverse($appliedMigrations) as $migration) {
            $results['rollbacks'][] = $this->rollbackMigration($migration);
        }
        
        // Re-run all migrations
        $results['migrations'] = $this->migrate();
        
        return $results;
    }
    
    /**
     * Reset migrations table
     * WARNING: This will lose migration history!
     */
    public function reset() {
        $sql = "DELETE FROM {$this->migrationsTable}";
        return $this->connection->query($sql);
    }
    
    /**
     * Ensure migrations table exists
     */
    private function ensureMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_migration (migration),
            INDEX idx_batch (batch)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->connection->query($sql);
    }
    
    /**
     * Ensure migrations directory exists
     */
    private function ensureMigrationsDirectory() {
        if (!is_dir($this->migrationsDir)) {
            mkdir($this->migrationsDir, 0755, true);
        }
    }
    
    /**
     * Get all migration files
     * @return array Migration file names
     */
    private function getAllMigrationFiles() {
        $files = glob($this->migrationsDir . '/*.php');
        $migrations = [];
        
        foreach ($files as $file) {
            $migrations[] = basename($file, '.php');
        }
        
        sort($migrations);
        return $migrations;
    }
    
    /**
     * Get applied migrations
     * @return array Applied migration names
     */
    private function getAppliedMigrations() {
        $sql = "SELECT migration FROM {$this->migrationsTable} ORDER BY id";
        $result = $this->connection->query($sql);
        
        $migrations = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $migrations[] = $row['migration'];
            }
        }
        
        return $migrations;
    }
    
    /**
     * Get pending migrations
     * @return array Pending migration names
     */
    private function getPendingMigrations() {
        $allMigrations = $this->getAllMigrationFiles();
        $appliedMigrations = $this->getAppliedMigrations();
        
        return array_diff($allMigrations, $appliedMigrations);
    }
    
    /**
     * Get last applied migration
     * @return string|null Last migration name
     */
    private function getLastMigration() {
        $sql = "SELECT migration FROM {$this->migrationsTable} ORDER BY id DESC LIMIT 1";
        $result = $this->connection->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            return $row['migration'];
        }
        
        return null;
    }
    
    /**
     * Get migration applied timestamp
     * @param string $migration Migration name
     * @return string|null Applied timestamp
     */
    private function getMigrationAppliedAt($migration) {
        $sql = "SELECT executed_at FROM {$this->migrationsTable} WHERE migration = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('s', $migration);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['executed_at'];
        }
        
        return null;
    }
    
    /**
     * Get migrations to rollback to reach target
     * @param string $targetMigration Target migration
     * @return array Migrations to rollback
     */
    private function getMigrationsToRollback($targetMigration) {
        $appliedMigrations = $this->getAppliedMigrations();
        $targetIndex = array_search($targetMigration, $appliedMigrations);
        
        if ($targetIndex === false) {
            return [];
        }
        
        return array_slice($appliedMigrations, $targetIndex + 1);
    }
    
    /**
     * Run a single migration
     * @param string $migration Migration name
     * @return array Migration result
     */
    private function runMigration($migration) {
        $filepath = $this->migrationsDir . '/' . $migration . '.php';
        
        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'migration' => $migration,
                'message' => 'Migration file not found'
            ];
        }
        
        try {
            $this->connection->autocommit(false);
            
            // Include and run migration
            $migrationInstance = $this->loadMigration($filepath);
            $migrationInstance->up($this->connection);
            
            // Record migration
            $batch = $this->getNextBatch();
            $sql = "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param('si', $migration, $batch);
            $stmt->execute();
            
            $this->connection->commit();
            $this->connection->autocommit(true);
            
            return [
                'success' => true,
                'migration' => $migration,
                'message' => 'Migration applied successfully'
            ];
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->connection->autocommit(true);
            
            return [
                'success' => false,
                'migration' => $migration,
                'message' => 'Migration failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Rollback a single migration
     * @param string $migration Migration name
     * @return array Rollback result
     */
    private function rollbackMigration($migration) {
        $filepath = $this->migrationsDir . '/' . $migration . '.php';
        
        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'migration' => $migration,
                'message' => 'Migration file not found'
            ];
        }
        
        try {
            $this->connection->autocommit(false);
            
            // Include and run rollback
            $migrationInstance = $this->loadMigration($filepath);
            $migrationInstance->down($this->connection);
            
            // Remove migration record
            $sql = "DELETE FROM {$this->migrationsTable} WHERE migration = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param('s', $migration);
            $stmt->execute();
            
            $this->connection->commit();
            $this->connection->autocommit(true);
            
            return [
                'success' => true,
                'migration' => $migration,
                'message' => 'Migration rolled back successfully'
            ];
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->connection->autocommit(true);
            
            return [
                'success' => false,
                'migration' => $migration,
                'message' => 'Rollback failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Load migration instance
     * @param string $filepath Migration file path
     * @return object Migration instance
     */
    private function loadMigration($filepath) {
        require_once $filepath;
        
        $className = $this->getClassNameFromFile($filepath);
        return new $className();
    }
    
    /**
     * Get class name from migration file
     * @param string $filepath Migration file path
     * @return string Class name
     */
    private function getClassNameFromFile($filepath) {
        $content = file_get_contents($filepath);
        
        if (preg_match('/class\s+([a-zA-Z_][a-zA-Z0-9_]*)/i', $content, $matches)) {
            return $matches[1];
        }
        
        throw new Exception('Could not determine migration class name');
    }
    
    /**
     * Get next batch number
     * @return int Next batch number
     */
    private function getNextBatch() {
        $sql = "SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}";
        $result = $this->connection->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            return ($row['max_batch'] ?? 0) + 1;
        }
        
        return 1;
    }
    
    /**
     * Format class name for migration
     * @param string $name Migration name
     * @return string Formatted class name
     */
    private function formatClassName($name) {
        return 'Migration' . str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $name)));
    }
    
    /**
     * Get migration template
     * @param string $className Class name
     * @param string $type Migration type
     * @return string Migration template
     */
    private function getMigrationTemplate($className, $type) {
        $template = <<<PHP
<?php
/**
 * Migration: {$className}
 * Generated: %s
 */

class {$className} {
    
    /**
     * Run the migration
     * @param mysqli \$connection Database connection
     */
    public function up(\$connection) {
        // TODO: Implement migration up logic
        %s
    }
    
    /**
     * Reverse the migration
     * @param mysqli \$connection Database connection
     */
    public function down(\$connection) {
        // TODO: Implement migration down logic
        %s
    }
}
PHP;

        $upExample = $this->getUpExample($type);
        $downExample = $this->getDownExample($type);
        
        return sprintf($template, date('Y-m-d H:i:s'), $upExample, $downExample);
    }
    
    /**
     * Get up() method example based on type
     * @param string $type Migration type
     * @return string Example code
     */
    private function getUpExample($type) {
        switch ($type) {
            case 'create_table':
                return '
        $sql = "CREATE TABLE example_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $connection->query($sql);';
                
            case 'alter_table':
                return '
        $sql = "ALTER TABLE example_table ADD COLUMN new_column VARCHAR(255) NULL";
        $connection->query($sql);';
                
            case 'add_index':
                return '
        $sql = "ALTER TABLE example_table ADD INDEX idx_name (name)";
        $connection->query($sql);';
                
            default:
                return '
        // Add your migration logic here
        // $sql = "...";
        // $connection->query($sql);';
        }
    }
    
    /**
     * Get down() method example based on type
     * @param string $type Migration type
     * @return string Example code
     */
    private function getDownExample($type) {
        switch ($type) {
            case 'create_table':
                return '
        $sql = "DROP TABLE IF EXISTS example_table";
        $connection->query($sql);';
                
            case 'alter_table':
                return '
        $sql = "ALTER TABLE example_table DROP COLUMN new_column";
        $connection->query($sql);';
                
            case 'add_index':
                return '
        $sql = "ALTER TABLE example_table DROP INDEX idx_name";
        $connection->query($sql);';
                
            default:
                return '
        // Add your rollback logic here
        // $sql = "...";
        // $connection->query($sql);';
        }
    }
}