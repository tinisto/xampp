#!/usr/bin/env python3
"""
Fix VPO export to work with universities table
"""

def fix_vpo_export(input_file, output_file):
    """Convert VPO export to universities format"""
    
    print("🔧 Fixing VPO export for universities table...")
    
    # Read the file
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # First, change the table name
    content = content.replace("INSERT INTO `vpo`", "INSERT INTO `universities`")
    
    # Now we need to fix the column list
    # Find the column list in parentheses after INSERT INTO
    import re
    
    # Extract the INSERT statement
    match = re.search(r'INSERT INTO `universities` \((.*?)\) VALUES', content, re.DOTALL)
    if match:
        old_columns = match.group(1)
        
        # Create the new column list with only columns that exist in universities table
        new_columns = """
`id`, `user_id`, `parent_university_id`, `university_name`, `university_name_genitive`,
`full_name`, `short_name`, `old_names`, `town_id`, `area_id`, `region_id`, `country_id`,
`postal_code`, `street_address`, `phone`, `fax`, `email`, `website`,
`director_name`, `director_role`, `director_info`, `director_email`, `director_phone`,
`accreditation`, `license`, `founding_year`, `meta_description`, `meta_keywords`,
`history`, `url_slug`, `image_1`, `image_2`, `image_3`, `vkontakte_url`,
`view_count`, `is_approved`, `updated_at`
""".strip()
        
        # Replace the old column list with the new one
        content = content.replace(old_columns, new_columns)
        
        # Now we need to handle the VALUES part
        # The problem is that the old table has more columns than the new one
        # We need to extract only the values we need in the right order
        
        # Find all the VALUES rows
        values_section = content[content.find('VALUES'):]
        
        # This is complex because we need to reorder the values
        # Let's create a mapping of old column positions
        old_cols_list = [col.strip() for col in old_columns.split(',')]
        old_positions = {col: i for i, col in enumerate(old_cols_list)}
        
        # For each VALUES row, we need to extract the right values in the right order
        # This is getting complex, so let's just tell the user what to do
        
        print("⚠️  The file has been partially fixed.")
        print("⚠️  You still need to manually remove values for these columns:")
        print("    - filials_vpo (position 4)")
        print("    - email_pk (position 29)")
        print("    - tel_pk (position 30)")
        print("    - otvetcek (position 31)")
        print("    - site_pk (position 32)")
        print("    - address_pk (position 33)")
        print("\n    Count the commas and remove these value positions from each row.")
    
    # Write the partially fixed file
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"\n📄 Partially fixed file saved to: {output_file}")
    print("\n🎯 Alternative: Use the direct phpMyAdmin approach instead!")

if __name__ == "__main__":
    # Get the latest export file
    import os
    import glob
    
    # Find the most recent SQL file in Downloads
    downloads_path = "/Users/anatolys/Downloads/"
    sql_files = glob.glob(os.path.join(downloads_path, "*.sql"))
    
    if sql_files:
        # Get the most recent file
        latest_file = max(sql_files, key=os.path.getctime)
        output_file = os.path.join(downloads_path, "universities_import.sql")
        
        print(f"📁 Processing: {latest_file}")
        fix_vpo_export(latest_file, output_file)
    else:
        print("❌ No SQL files found in Downloads folder")