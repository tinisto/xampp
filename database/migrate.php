<?php
/**
 * Migration Command Line Interface
 * Usage: php migrate.php [command] [options]
 */

require_once __DIR__ . '/../config/loadEnv.php';
require_once __DIR__ . '/../database/db_connections.php';
require_once __DIR__ . '/../includes/database/migration_manager.php';

// CLI-only execution
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

$migrationManager = new MigrationManager($connection);

// Parse command line arguments
$command = $argv[1] ?? 'help';
$options = array_slice($argv, 2);

echo "11klassniki Migration Tool\n";
echo "==========================\n\n";

switch ($command) {
    case 'migrate':
    case 'up':
        echo "Running migrations...\n";
        $results = $migrationManager->migrate();
        
        if (empty($results)) {
            echo "✅ No pending migrations found.\n";
        } else {
            foreach ($results as $result) {
                $status = $result['success'] ? '✅' : '❌';
                echo "{$status} {$result['migration']}: {$result['message']}\n";
            }
        }
        break;
        
    case 'rollback':
    case 'down':
        $target = $options[0] ?? null;
        
        if ($target) {
            echo "Rolling back to migration: {$target}...\n";
            $results = $migrationManager->rollbackTo($target);
        } else {
            echo "Rolling back last migration...\n";
            $results = [$migrationManager->rollback()];
        }
        
        foreach ($results as $result) {
            $status = $result['success'] ? '✅' : '❌';
            echo "{$status} {$result['migration']}: {$result['message']}\n";
        }
        break;
        
    case 'status':
        echo "Migration Status:\n";
        $status = $migrationManager->getStatus();
        
        echo "Total migrations: {$status['total_migrations']}\n";
        echo "Applied: {$status['applied_migrations']}\n";
        echo "Pending: {$status['pending_migrations']}\n\n";
        
        if (!empty($status['migrations'])) {
            echo "Migration Details:\n";
            echo str_repeat('-', 60) . "\n";
            
            foreach ($status['migrations'] as $migration) {
                $statusIcon = $migration['applied'] ? '✅' : '⏳';
                $appliedAt = $migration['applied_at'] ? " (applied: {$migration['applied_at']})" : '';
                echo "{$statusIcon} {$migration['name']}{$appliedAt}\n";
            }
        }
        break;
        
    case 'create':
    case 'make':
        $name = $options[0] ?? null;
        $type = $options[1] ?? 'general';
        
        if (!$name) {
            echo "❌ Error: Migration name is required.\n";
            echo "Usage: php migrate.php create migration_name [type]\n";
            echo "Types: create_table, alter_table, add_index, general\n";
            break;
        }
        
        $filepath = $migrationManager->createMigration($name, $type);
        echo "✅ Migration created: {$filepath}\n";
        break;
        
    case 'refresh':
        echo "⚠️  WARNING: This will rollback all migrations and re-run them!\n";
        echo "This will DESTROY ALL DATA in your database!\n";
        echo "Are you sure? (y/N): ";
        
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) === 'y') {
            echo "Refreshing migrations...\n";
            $results = $migrationManager->refresh();
            
            echo "\nRollback results:\n";
            foreach ($results['rollbacks'] as $result) {
                $status = $result['success'] ? '✅' : '❌';
                echo "{$status} {$result['migration']}: {$result['message']}\n";
            }
            
            echo "\nMigration results:\n";
            foreach ($results['migrations'] as $result) {
                $status = $result['success'] ? '✅' : '❌';
                echo "{$status} {$result['migration']}: {$result['message']}\n";
            }
        } else {
            echo "Operation cancelled.\n";
        }
        break;
        
    case 'reset':
        echo "⚠️  WARNING: This will reset the migrations table!\n";
        echo "Migration history will be lost!\n";
        echo "Are you sure? (y/N): ";
        
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) === 'y') {
            $migrationManager->reset();
            echo "✅ Migrations table reset.\n";
        } else {
            echo "Operation cancelled.\n";
        }
        break;
        
    case 'help':
    default:
        echo "Available commands:\n\n";
        echo "migrate, up              Run pending migrations\n";
        echo "rollback, down [target]  Rollback migrations (optionally to target)\n";
        echo "status                   Show migration status\n";
        echo "create, make <name>      Create new migration\n";
        echo "refresh                  Rollback all and re-run migrations\n";
        echo "reset                    Reset migrations table\n";
        echo "help                     Show this help message\n\n";
        
        echo "Examples:\n";
        echo "php migrate.php migrate\n";
        echo "php migrate.php create add_user_profile_table create_table\n";
        echo "php migrate.php rollback\n";
        echo "php migrate.php status\n";
        break;
}

echo "\n";
$connection->close();