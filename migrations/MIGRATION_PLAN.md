# Database Field Standardization Migration Plan

This migration standardizes inconsistent field names across the database to improve maintainability and reduce bugs.

## Current Issues
- Primary keys: `id`, `id_post`, `id_news`, `id_vpo`, `id_spo`, `id_school`
- Foreign keys: `user_id`, `region_id` vs `id_region`, `id_town`, etc.

## Proposed Standards
- **Primary keys**: Always `id`
- **Foreign keys**: Always `table_id` (e.g., `region_id`, `user_id`, `town_id`)

## Migration Steps

### Phase 1: Preparation (REQUIRED)
1. **Full database backup**
   ```sql
   mysqldump -u username -p database_name > backup_before_migration.sql
   ```

2. **Full codebase backup**
   ```bash
   tar -czf codebase_backup_$(date +%Y%m%d).tar.gz /path/to/project
   ```

3. **Create test environment** (if possible)

### Phase 2: Database Migration
1. Run SQL migration script:
   ```sql
   SOURCE /path/to/standardize_field_names.sql;
   ```

2. Verify migration success:
   ```sql
   -- Check that all tables have 'id' as primary key
   SELECT table_name, column_name 
   FROM information_schema.key_column_usage 
   WHERE constraint_name = 'PRIMARY' 
   AND table_schema = 'your_database_name';
   ```

### Phase 3: Code Updates
1. Run PHP field name analysis:
   ```bash
   php migrations/update_php_field_names.php
   ```

2. Review generated update script before running

3. Update PHP files:
   ```bash
   ./migrations/update_field_names.sh
   ```

### Phase 4: Testing
1. Test major functionality:
   - User registration/login
   - Comments system
   - Region pages
   - Search functionality
   - Admin dashboard

2. Check error logs for any missed references

3. Test database queries manually

### Phase 5: Cleanup
1. Remove backup files if everything works
2. Update documentation
3. Commit changes to version control

## High-Risk Areas
These areas require manual review:

1. **Dynamic queries** - Code that builds field names dynamically
2. **JavaScript/AJAX** - Frontend code using these field names
3. **External integrations** - APIs that expect specific field names
4. **Reports/exports** - Code that outputs data with field names

## Rollback Plan
If issues occur:

1. **Database rollback**:
   ```sql
   DROP DATABASE current_database;
   CREATE DATABASE current_database;
   mysql -u username -p current_database < backup_before_migration.sql
   ```

2. **Code rollback**:
   ```bash
   # Restore PHP files from backup
   find . -name "*.php.bak" -exec sh -c 'mv "$1" "${1%.bak}"' _ {} \;
   ```

## Files Changed by This Migration

### Database Tables
- `posts`: `id_post` → `id`
- `news`: `id_news` → `id`, `id_vpo` → `vpo_id`, `id_spo` → `spo_id`, `id_school` → `school_id`
- `vpo`: `id_vpo` → `id`, `id_region` → `region_id`, `id_town` → `town_id`, etc.
- `spo`: `id_spo` → `id`, `id_region` → `region_id`, `id_town` → `town_id`, etc.
- `schools`: `id_school` → `id`, `id_region` → `region_id`, `id_town` → `town_id`, etc.

### PHP Files (Estimated)
- Region pages (`educational-institutions-in-region.php`)
- Comments system
- Admin dashboard
- Search functionality
- Template files

## Success Criteria
- ✅ All pages load without errors
- ✅ Database queries execute successfully
- ✅ No PHP errors in logs
- ✅ Comments system works
- ✅ Region pages work
- ✅ Admin dashboard functions properly

## Timeline
- **Preparation**: 30 minutes
- **Migration**: 10 minutes
- **Testing**: 60 minutes
- **Cleanup**: 15 minutes
- **Total**: ~2 hours

⚠️ **IMPORTANT**: This is a major structural change. Test thoroughly on a staging environment first!