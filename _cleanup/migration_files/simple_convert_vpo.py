#!/usr/bin/env python3
"""
Simple VPO to Universities converter - handles column mismatch
"""

def convert_vpo_sql(input_file, output_file):
    """Convert VPO export to match universities table"""
    
    print("🔄 Converting VPO export...")
    
    # Read the file
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Change table name
    content = content.replace('INSERT INTO `vpo`', 'INSERT INTO `universities`')
    
    # Remove columns that don't exist in new table
    # These columns exist in vpo but NOT in universities:
    columns_to_remove = [
        '`filials_vpo`',
        '`email_pk`', 
        '`tel_pk`',
        '`otvetcek`',
        '`site_pk`',
        '`address_pk`'
    ]
    
    # First, let's modify the column list in the INSERT statement
    for col in columns_to_remove:
        # Remove column and its trailing comma
        content = content.replace(f'{col}, ', '')
        content = content.replace(f', {col}', '')
    
    # Now fix the column names
    replacements = [
        ('`id_vpo`', '`id`'),
        ('`parent_vpo_id`', '`parent_university_id`'),
        ('`vpo_name`', '`university_name`'),
        ('`name_rod`', '`university_name_genitive`'),
        ('`old_name`', '`old_names`'),
        ('`id_town`', '`town_id`'),
        ('`id_area`', '`area_id`'),
        ('`id_region`', '`region_id`'),
        ('`id_country`', '`country_id`'),
        ('`zip_code`', '`postal_code`'),
        ('`street`', '`street_address`'),
        ('`tel`', '`phone`'),
        ('`site`', '`website`'),
        ('`licence`', '`license`'),
        ('`year`', '`founding_year`'),
        ('`meta_d_vpo`', '`meta_description`'),
        ('`meta_k_vpo`', '`meta_keywords`'),
        ('`vpo_url`', '`url_slug`'),
        ('`image_vpo_1`', '`image_1`'),
        ('`image_vpo_2`', '`image_2`'),
        ('`image_vpo_3`', '`image_3`'),
        ('`vkontakte`', '`vkontakte_url`'),
        ('`view`', '`view_count`'),
        ('`approved`', '`is_approved`'),
        ('`updated`', '`updated_at`')
    ]
    
    # Apply replacements
    for old, new in replacements:
        content = content.replace(old, new)
    
    # Write the output
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Conversion complete!")
    print(f"📁 Output saved to: {output_file}")
    print("\n⚠️  IMPORTANT: You'll need to manually remove VALUES for the removed columns")
    print("The columns removed were: filials_vpo, email_pk, tel_pk, otvetcek, site_pk, address_pk")
    print("\nOpen the file and remove the corresponding values from each INSERT row")

if __name__ == "__main__":
    convert_vpo_sql(
        "/Users/anatolys/Downloads/vpo.sql",
        "/Users/anatolys/Downloads/universities_manual.sql"
    )