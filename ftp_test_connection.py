#!/usr/bin/env python3

import ftplib
import socket

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "u2709849"
FTP_PASS = "Qazwsxedc123"

def test_ftp_connection():
    """Test FTP connection with various methods"""
    
    print("🔍 Testing FTP connection...")
    print(f"   Host: {FTP_HOST}")
    print(f"   User: {FTP_USER}")
    print(f"   Pass: {'*' * len(FTP_PASS)}")
    
    # Test 1: Basic connection
    try:
        print("\n📡 Test 1: Basic FTP connection...")
        ftp = ftplib.FTP()
        ftp.connect(FTP_HOST, 21, timeout=30)
        print("✅ Connected to server")
        
        # Try login
        response = ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Login successful: {response}")
        
        # Get current directory
        pwd = ftp.pwd()
        print(f"📁 Current directory: {pwd}")
        
        # List files
        print("\n📋 Directory listing:")
        files = []
        ftp.retrlines('LIST', files.append)
        for f in files[:5]:  # Show first 5
            print(f"   {f}")
        
        ftp.quit()
        print("\n✅ FTP connection test successful!")
        return True
        
    except socket.gaierror as e:
        print(f"❌ DNS resolution failed: {e}")
        print("   The hostname might be incorrect or DNS is not resolving")
        
    except ftplib.error_perm as e:
        print(f"❌ Permission error: {e}")
        print("   The username or password might be incorrect")
        
    except Exception as e:
        print(f"❌ Connection failed: {type(e).__name__}: {e}")
    
    # Test 2: Try with ftp. prefix
    try:
        print("\n📡 Test 2: Trying ftp.11klassniki.ru...")
        ftp = ftplib.FTP()
        ftp.connect("ftp.11klassniki.ru", 21, timeout=30)
        print("✅ Connected to ftp.11klassniki.ru")
        ftp.login(FTP_USER, FTP_PASS)
        ftp.quit()
        return True
    except Exception as e:
        print(f"❌ ftp.11klassniki.ru failed: {e}")
    
    # Test 3: Check if it's an IP-based connection
    try:
        print("\n📡 Test 3: Resolving hostname to IP...")
        import socket
        ip = socket.gethostbyname(FTP_HOST)
        print(f"📍 Resolved to IP: {ip}")
        
        ftp = ftplib.FTP()
        ftp.connect(ip, 21, timeout=30)
        print(f"✅ Connected to IP {ip}")
        ftp.login(FTP_USER, FTP_PASS)
        ftp.quit()
        return True
    except Exception as e:
        print(f"❌ IP connection failed: {e}")
    
    return False

if __name__ == "__main__":
    test_ftp_connection()