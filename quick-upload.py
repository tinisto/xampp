#!/usr/bin/env python3

import subprocess
import time

files = [
    ('common-components/test-card.php', 'common-components/test-card.php'),
    ('pages/tests/tests-main-content.php', 'pages/tests/tests-main-content.php')
]

for local, remote in files:
    print(f"Uploading {local}...")
    cmd = f'curl -T /Applications/XAMPP/xamppfiles/htdocs/{local} ftp://8b6cdc76_sitearchive:jU9%25mHr1@77.232.131.89/domains/11klassniki.ru/public_html/{remote}'
    subprocess.run(cmd, shell=True)
    time.sleep(1)

print("Done!")