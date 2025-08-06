#!/usr/bin/env python3

import subprocess
import sys
from datetime import datetime

def upload_with_curl():
    """Upload files using curl."""
    files = [
        ('common-components/test-card.php', 'common-components/test-card.php'),
        ('pages/tests/tests-main-content.php', 'pages/tests/tests-main-content.php')
    ]
    
    for local_path, remote_path in files:
        full_local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{local_path}'
        
        print(f"Uploading {local_path}...")
        
        cmd = [
            'curl', '-T', full_local_path,
            f'ftp://77.232.131.89/domains/11klassniki.ru/public_html/{remote_path}',
            '--user', '8b6cdc76_sitearchive:jU9%mHr1',
            '--ftp-create-dirs'
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True)
            if result.returncode == 0:
                print(f"✓ Successfully uploaded {local_path}")
            else:
                print(f"✗ Error uploading {local_path}: {result.stderr}")
        except Exception as e:
            print(f"✗ Exception uploading {local_path}: {e}")

def main():
    print(f"\n=== Uploading test card updates at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')} ===\n")
    
    upload_with_curl()
    
    print("\n=== Upload process completed ===")

if __name__ == "__main__":
    main()