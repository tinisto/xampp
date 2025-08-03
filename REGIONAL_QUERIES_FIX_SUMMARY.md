# Regional Queries Fix Summary

## Problem Identified
The regional queries for universities and colleges were returning no results because:
1. The code was updated to use new table names (`universities` and `colleges`)
2. But these new tables are empty - all data is still in the old tables (`vpo` and `spo`)
3. The new tables also use different column names (`region_id` vs `id_region`)

## Solution Applied
Updated the following files to use the correct old table names and column mappings:

### 1. `/pages/common/educational-institutions-in-region/educational-institutions-in-region.php`
- Added table name and column mappings based on type (vpo → vpo table, spo → spo table)
- Updated queries to use `$tableName` and `$regionColumn` variables
- Fixed display logic to use correct column names for each type
- Fixed address/phone/website field names based on institution type

### 2. `/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php`
- Changed table mappings back to old tables (vpo, spo instead of universities, colleges)
- Added `$regionColumn` variable (always 'id_region' for old tables)

### 3. `/pages/common/educational-institutions-in-region/function-query.php`
- Updated to map types to correct table names
- All tables now correctly use `id_region` column

### 4. `/pages/common/educational-institutions-in-region/educational-institutions-in-region-content.php`
- Added table name mapping
- Updated queries to use `$tableName` variable

### 5. `/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php`
- Removed logic that tried to use `region_id` column
- All queries now use `id_region` column

## Table Mappings Now Used
| URL Type | Table Name | Region Column | Name Column | URL Column |
|----------|------------|---------------|-------------|------------|
| vpo      | vpo        | id_region     | vpo_name    | vpo_url    |
| spo      | spo        | id_region     | spo_name    | spo_url    |
| schools  | schools    | id_region     | school_name | id_school  |

## Result
The regional queries should now work correctly, displaying:
- VPO (universities) from the `vpo` table
- SPO (colleges) from the `spo` table  
- Schools from the `schools` table

All using the correct `id_region` column for filtering by region.