#!/usr/bin/env python3
"""
Fix VPO SQL export to match new universities table structure
"""
import re

def fix_vpo_to_universities(input_file, output_file):
    """Convert VPO table export to universities table format"""
    
    print("🔧 Converting VPO export to Universities format...")
    
    # Read the SQL file
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # First, let's extract the column list from the INSERT statement
    insert_match = re.search(r'INSERT INTO `\w+` \((.*?)\) VALUES', content, re.DOTALL)
    if not insert_match:
        print("❌ Could not find INSERT statement")
        return
    
    old_columns = insert_match.group(1)
    
    # Define the exact columns that exist in the new universities table
    new_columns = [
        '`id`', '`user_id`', '`parent_university_id`', '`university_name`', 
        '`university_name_genitive`', '`full_name`', '`short_name`', '`old_names`',
        '`town_id`', '`area_id`', '`region_id`', '`country_id`', '`postal_code`',
        '`street_address`', '`phone`', '`fax`', '`email`', '`website`',
        '`director_name`', '`director_role`', '`director_info`', '`director_email`',
        '`director_phone`', '`accreditation`', '`license`', '`founding_year`',
        '`meta_description`', '`meta_keywords`', '`history`', '`url_slug`',
        '`image_1`', '`image_2`', '`image_3`', '`vkontakte_url`', '`view_count`',
        '`is_approved`', '`updated_at`'
    ]
    
    # Map old column positions to new positions
    old_cols_list = [col.strip() for col in old_columns.split(',')]
    
    # Create mapping of old columns to their positions
    old_col_positions = {}
    for i, col in enumerate(old_cols_list):
        old_col_positions[col] = i
    
    # Build the new INSERT statement with proper column order
    new_insert = f"INSERT INTO `universities` ({', '.join(new_columns)}) VALUES\n"
    
    # Extract all the VALUES rows
    values_pattern = r'\((.*?)\)(?:,|\s*;)'
    values_matches = re.findall(values_pattern, content[content.find('VALUES'):], re.DOTALL)
    
    new_values = []
    for i, values_row in enumerate(values_matches):
        # Split the values carefully (accounting for quoted commas)
        values = re.split(r",(?=(?:[^']*'[^']*')*[^']*$)", values_row)
        
        # Build new row with correct column mapping
        new_row_values = []
        
        # Map each column
        new_row_values.append(values[old_col_positions.get('`id_vpo`', 0)])  # id
        new_row_values.append(values[old_col_positions.get('`user_id`', 1)])  # user_id
        
        # Handle parent_vpo_id
        parent_id_pos = old_col_positions.get('`parent_vpo_id`', 2)
        parent_val = values[parent_id_pos] if parent_id_pos < len(values) else '0'
        new_row_values.append('NULL' if parent_val == '0' else parent_val)  # parent_university_id
        
        new_row_values.append(values[old_col_positions.get('`vpo_name`', 10)])  # university_name
        new_row_values.append(values[old_col_positions.get('`name_rod`', 11)])  # university_name_genitive
        new_row_values.append(values[old_col_positions.get('`full_name`', 12)])  # full_name
        new_row_values.append(values[old_col_positions.get('`short_name`', 13)])  # short_name
        new_row_values.append(values[old_col_positions.get('`old_name`', 14)])  # old_names
        new_row_values.append(values[old_col_positions.get('`id_town`', 16)])  # town_id
        new_row_values.append(values[old_col_positions.get('`id_area`', 17)])  # area_id
        new_row_values.append(values[old_col_positions.get('`id_region`', 18)])  # region_id
        new_row_values.append(values[old_col_positions.get('`id_country`', 19)])  # country_id
        new_row_values.append(values[old_col_positions.get('`zip_code`', 15)])  # postal_code
        new_row_values.append(values[old_col_positions.get('`street`', 21)])  # street_address
        new_row_values.append(values[old_col_positions.get('`tel`', 22)])  # phone
        new_row_values.append(values[old_col_positions.get('`fax`', 23)])  # fax
        new_row_values.append(values[old_col_positions.get('`email`', 27)])  # email
        new_row_values.append(values[old_col_positions.get('`site`', 26)])  # website
        new_row_values.append(values[old_col_positions.get('`director_name`', 7)])  # director_name
        new_row_values.append(values[old_col_positions.get('`director_role`', 8)])  # director_role
        new_row_values.append(values[old_col_positions.get('`director_info`', 9)])  # director_info
        new_row_values.append(values[old_col_positions.get('`director_email`', 34)])  # director_email
        new_row_values.append(values[old_col_positions.get('`director_phone`', 35)])  # director_phone
        new_row_values.append(values[old_col_positions.get('`accreditation`', 24)])  # accreditation
        new_row_values.append(values[old_col_positions.get('`licence`', 25)])  # license
        new_row_values.append(values[old_col_positions.get('`year`', 20)])  # founding_year
        new_row_values.append(values[old_col_positions.get('`meta_d_vpo`', 4)])  # meta_description
        new_row_values.append(values[old_col_positions.get('`meta_k_vpo`', 5)])  # meta_keywords
        new_row_values.append(values[old_col_positions.get('`history`', 38)])  # history
        new_row_values.append(values[old_col_positions.get('`vpo_url`', 39)])  # url_slug
        new_row_values.append(values[old_col_positions.get('`image_vpo_1`', 40)])  # image_1
        new_row_values.append(values[old_col_positions.get('`image_vpo_2`', 41)])  # image_2
        new_row_values.append(values[old_col_positions.get('`image_vpo_3`', 42)])  # image_3
        new_row_values.append(values[old_col_positions.get('`vkontakte`', 33)])  # vkontakte_url
        new_row_values.append(values[old_col_positions.get('`view`', 37)])  # view_count
        new_row_values.append(values[old_col_positions.get('`approved`', 6)])  # is_approved
        new_row_values.append(values[old_col_positions.get('`updated`', 36)])  # updated_at
        
        # Add row to new values
        new_values.append(f"({', '.join(new_row_values)})")
    
    # Combine everything
    final_sql = new_insert + ',\n'.join(new_values) + ';'
    
    # Write the converted file
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(final_sql)
    
    print(f"✅ Successfully converted {len(new_values)} records")
    print(f"📄 Output saved to: {output_file}")
    print("\n🎯 Next steps:")
    print("1. Go to phpMyAdmin")
    print("2. Select '11klassniki_new' database")
    print("3. Click 'Import'")
    print(f"4. Choose file: {output_file}")
    print("5. Click 'Go' to import")

if __name__ == "__main__":
    input_file = "/Users/anatolys/Downloads/vpo.sql"
    output_file = "/Users/anatolys/Downloads/universities_fixed.sql"
    
    try:
        fix_vpo_to_universities(input_file, output_file)
    except FileNotFoundError:
        print(f"❌ File not found: {input_file}")
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()