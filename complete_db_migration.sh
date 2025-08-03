#!/bin/bash
# Complete migration to new database structure

echo "🚀 Starting database migration..."

# 1. Update educational institutions pages to use new table names
echo "📝 Updating educational institutions pages..."

# Update all-regions page
sed -i '' "s/\$table = 'spo';/\$table = 'colleges';/g" pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php
sed -i '' "s/\$table = 'vpo';/\$table = 'universities';/g" pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php
sed -i '' "s/id_region/region_id/g" pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php

# Update in-region page
sed -i '' "s/\$table = 'spo';/\$table = 'colleges';/g" pages/common/educational-institutions-in-region/educational-institutions-in-region.php
sed -i '' "s/\$table = 'vpo';/\$table = 'universities';/g" pages/common/educational-institutions-in-region/educational-institutions-in-region.php
sed -i '' "s/id_region/region_id/g" pages/common/educational-institutions-in-region/educational-institutions-in-region.php

# Update output functions
sed -i '' "s/\['vpo_name'\]/['name']/g" pages/common/educational-institutions-in-region/outputEducationalInstitutions.php
sed -i '' "s/\['spo_name'\]/['name']/g" pages/common/educational-institutions-in-region/outputEducationalInstitutions.php
sed -i '' "s/\['vpo_url'\]/['url']/g" pages/common/educational-institutions-in-region/outputEducationalInstitutions.php
sed -i '' "s/\['spo_url'\]/['url']/g" pages/common/educational-institutions-in-region/outputEducationalInstitutions.php
sed -i '' "s/\['vpo_city'\]/['city']/g" pages/common/educational-institutions-in-region/outputEducationalInstitutions.php
sed -i '' "s/\['spo_city'\]/['city']/g" pages/common/educational-institutions-in-region/outputEducationalInstitutions.php

# 2. Update .env file
echo "🔧 Updating .env configuration..."
sed -i '' 's/DB_NAME=11klassniki_ru/DB_NAME=11klassniki_claude/g' .env

# 3. Update database connection to remove force flag
echo "🔧 Updating database connection..."
sed -i '' 's/$force_new_db = true;/$force_new_db = false;/g' database/db_connections.php

echo "✅ Migration script complete!"
echo ""
echo "📋 Next steps:"
echo "1. Run this script: bash complete_db_migration.sh"
echo "2. Visit https://11klassniki.ru/fix_missing_records.php to migrate missing records"
echo "3. Test the application thoroughly"
echo "4. Once verified, the old database can be safely removed"