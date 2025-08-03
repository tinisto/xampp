# VPO/SPO Database Migration Summary

This document summarizes all the database field mappings that have been updated in the VPO/SPO pages to use the new database structure.

## Table Mappings
- `vpo` → `universities`
- `spo` → `colleges`

## Field Mappings

### Primary Keys and IDs
- `id_vpo` → `id`
- `id_spo` → `id`
- `id_region` → `region_id`
- `id_town` → `town_id`
- `id_area` → `area_id`

### Name Fields
- `vpo_name` → `university_name`
- `spo_name` → `college_name`
- `name_rod` → `university_name_genitive` / `college_name_genitive`
- `old_name` → `former_names`

### URL and Status Fields
- `vpo_url` → `url_slug`
- `spo_url` → `url_slug`
- `view` → `view_count`
- `approved` → `is_approved`
- `updated` → `updated_at`

### Contact Information
- `tel` → `phone`
- `site` → `website`
- `street` → `address`

### Admission Office Fields
- `tel_pk` → `admission_phone`
- `site_pk` → `admission_website`
- `email_pk` → `admission_email`
- `address_pk` → `admission_address`

### Parent/Branch Relationships
- `parent_vpo_id` → `parent_university_id`
- `parent_spo_id` → `parent_college_id`
- `filials_vpo` → `branch_ids`
- `filials_spo` → `branch_ids`

### Image Fields
- `image_vpo_1`, `image_vpo_2`, `image_vpo_3` → `image_1`, `image_2`, `image_3`
- `image_spo_1`, `image_spo_2`, `image_spo_3` → `image_1`, `image_2`, `image_3`

### News Table Fields
- `id_vpo` (in news table) → `university_id`
- `id_spo` (in news table) → `college_id`
- `url_news` → `url_slug`
- `title_news` → `title`

### Region/Town Table Fields
- `id_region` (in regions table) → `id`
- `id_town` (in towns table) → `id`

## Files Updated
1. `single-content.php` - Updated field references for display and form inputs
2. `single-data-fetch.php` - Already using new structure (no changes needed)
3. `generic-tabs.php` - Updated all field references in tabs
4. `single-header-links.php` - Updated region/town ID references
5. `fetchNewsContent.php` - Updated news table field references
6. `send_emails.php` - Updated field references for email form

## Notes
- The image path structure remains the same (`/images/vpo-images/` and `/images/spo-images/`)
- Edit form URLs remain unchanged (`/vpo-edit-form.php` and `/spo-edit-form.php`)
- Delete functions remain unchanged (`deleteVPO` and `deleteSPO`)