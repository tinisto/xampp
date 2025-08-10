#!/usr/bin/env python3
import subprocess
import sys

def download_database():
    """Download database from iPage to local MySQL"""
    
    # iPage database credentials
    REMOTE_HOST = "11klassnikiru67871.ipagemysql.com"
    REMOTE_USER = "admin_claude"
    REMOTE_PASS = "W4eZ!#9uwLmrMay"
    REMOTE_DB = "11klassniki_claude"
    
    # Local database settings
    LOCAL_HOST = "localhost"
    LOCAL_USER = "root"
    LOCAL_PASS = ""  # Default XAMPP MySQL password
    LOCAL_DB = "11klassniki_claude_local"
    
    print("📥 Downloading database from iPage...")
    
    try:
        # Step 1: Dump remote database
        dump_cmd = [
            "mysqldump",
            f"--host={REMOTE_HOST}",
            f"--user={REMOTE_USER}",
            f"--password={REMOTE_PASS}",
            "--single-transaction",
            "--routines",
            "--triggers",
            REMOTE_DB
        ]
        
        print("🔄 Creating database dump from iPage...")
        with open("database_backup.sql", "w") as dump_file:
            result = subprocess.run(dump_cmd, stdout=dump_file, stderr=subprocess.PIPE, text=True)
            
        if result.returncode != 0:
            print(f"❌ Error creating dump: {result.stderr}")
            return False
            
        print("✅ Database dump created: database_backup.sql")
        
        # Step 2: Create local database
        create_db_cmd = [
            "mysql",
            f"--host={LOCAL_HOST}",
            f"--user={LOCAL_USER}"
        ]
        
        if LOCAL_PASS:
            create_db_cmd.append(f"--password={LOCAL_PASS}")
            
        create_db_cmd.extend(["-e", f"CREATE DATABASE IF NOT EXISTS {LOCAL_DB} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"])
        
        print("🔄 Creating local database...")
        result = subprocess.run(create_db_cmd, stderr=subprocess.PIPE, text=True)
        
        if result.returncode != 0:
            print(f"❌ Error creating local database: {result.stderr}")
            return False
            
        print(f"✅ Local database created: {LOCAL_DB}")
        
        # Step 3: Import dump to local database
        import_cmd = [
            "mysql",
            f"--host={LOCAL_HOST}",
            f"--user={LOCAL_USER}",
            LOCAL_DB
        ]
        
        if LOCAL_PASS:
            import_cmd.append(f"--password={LOCAL_PASS}")
            
        print("🔄 Importing data to local database...")
        with open("database_backup.sql", "r") as dump_file:
            result = subprocess.run(import_cmd, stdin=dump_file, stderr=subprocess.PIPE, text=True)
            
        if result.returncode != 0:
            print(f"❌ Error importing database: {result.stderr}")
            return False
            
        print("✅ Database imported successfully!")
        
        # Step 4: Create local .env file
        local_env = f"""# Local Development Environment
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME={LOCAL_DB}

# Email Configuration (disabled for local)
SMTP_HOST=localhost
SMTP_USER=test@localhost
SMTP_PASS=test
"""
        
        with open(".env.local", "w") as env_file:
            env_file.write(local_env)
            
        print("✅ Local .env created")
        print(f"\n🎉 SUCCESS! Database downloaded and ready for local testing")
        print(f"📊 Local database: {LOCAL_DB}")
        print(f"🔗 Test at: http://localhost:8000")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    download_database()