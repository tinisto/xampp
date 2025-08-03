#!/usr/bin/env python3
"""
Convert VPO SQL export to Universities table format
"""

def convert_vpo_to_universities(input_file, output_file):
    """Convert VPO table export to universities table format"""
    
    # Read the SQL file
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Table name change
    content = content.replace('INSERT INTO `vpo`', 'INSERT INTO `universities`')
    
    # Column mappings
    replacements = [
        ('`id_vpo`', '`id`'),
        ('`vpo_name`', '`university_name`'),
        ('`name_rod`', '`university_name_genitive`'),
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
        ('`updated`', '`updated_at`'),
        ('`parent_vpo_id`', '`parent_university_id`'),
    ]
    
    # Apply all replacements
    for old, new in replacements:
        content = content.replace(old, new)
    
    # Write the converted file
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Converted {input_file} to {output_file}")
    print(f"📋 Applied {len(replacements)} column mappings")
    print("\n🎯 Next steps:")
    print("1. Go to phpMyAdmin")
    print("2. Select '11klassniki_new' database")
    print("3. Click 'Import'")
    print(f"4. Choose file: {output_file}")
    print("5. Click 'Go' to import")

if __name__ == "__main__":
    input_file = "/Users/anatolys/Downloads/vpo.sql"
    output_file = "/Users/anatolys/Downloads/universities.sql"
    
    try:
        convert_vpo_to_universities(input_file, output_file)
    except FileNotFoundError:
        print(f"❌ File not found: {input_file}")
        print("Please make sure the file path is correct")
    except Exception as e:
        print(f"❌ Error: {e}")