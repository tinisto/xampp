#!/usr/bin/env python3
"""
FTP Credentials for 11klassniki.ru
IMPORTANT: This file contains sensitive credentials. Do not commit to version control.
"""

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

# Database Configuration (from db_connections.php)
DB_HOST = "localhost"
DB_USER = "root"
DB_PASS = ""
DB_NAME = "bd11klassniki"

# Server SSH/SFTP (if needed)
SSH_HOST = "31.31.196.162"
SSH_USER = "franko"
SSH_PASS = "8YB7v2o6K9h4"
SSH_PATH = "www/11klassniki.ru"

def get_ftp_config():
    """Return FTP configuration"""
    return {
        'host': FTP_HOST,
        'user': FTP_USER,
        'pass': FTP_PASS,
        'root': FTP_ROOT
    }

def get_db_config():
    """Return database configuration"""
    return {
        'host': DB_HOST,
        'user': DB_USER,
        'pass': DB_PASS,
        'name': DB_NAME
    }

if __name__ == "__main__":
    print("FTP Credentials loaded successfully")
    print(f"FTP: {FTP_USER}@{FTP_HOST}")
    print(f"Root: {FTP_ROOT}")