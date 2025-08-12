#!/bin/bash
# Deployment script for 11klassniki.ru security and template updates

echo "🚀 Starting deployment of 11klassniki.ru updates..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "template.php" ] || [ ! -f "DEPLOYMENT_CHECKLIST.md" ]; then
    echo -e "${RED}Error: Please run this script from the project root directory${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Creating backup...${NC}"
# You should backup your production files before running this
read -p "Have you backed up your production database and files? (y/n): " -r
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}Please backup your production environment first!${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 2: Database setup...${NC}"
echo "Please run the SQL commands in deployment/create_tables.sql on your production database"
echo "This will create the rate_limit_attempts and security_logs tables"
read -p "Press enter when database tables have been created..."

echo -e "${YELLOW}Step 3: File upload preparation...${NC}"
echo "Files to upload are listed in: deployment/files_to_upload.txt"
echo "Files to delete are listed in: deployment/files_to_delete.txt"

echo -e "${YELLOW}Step 4: Checking critical files...${NC}"

# Check if critical new files exist
CRITICAL_FILES=(
    "includes/csrf-protection.php"
    "includes/rate-limiter.php"
    "includes/security-headers.php"
    "includes/security-logger.php"
    "includes/input-validator.php"
    "includes/header.php"
    "includes/footer.php"
    "template.php"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file exists"
    else
        echo -e "${RED}✗${NC} $file is missing!"
        exit 1
    fi
done

echo -e "${GREEN}All critical files are present!${NC}"

echo -e "${YELLOW}Step 5: Post-deployment testing checklist...${NC}"
echo "After uploading files, test these functions:"
echo "1. Login (should have rate limiting)"
echo "2. Registration (should validate strong passwords)"  
echo "3. Search functionality"
echo "4. Comment submission"
echo "5. Check that no PHP errors are displayed"
echo "6. Verify CSRF protection is working"

echo -e "${GREEN}🎯 Deployment preparation complete!${NC}"
echo ""
echo "Next steps:"
echo "1. Upload all files listed in deployment/files_to_upload.txt"
echo "2. Delete files listed in deployment/files_to_delete.txt"
echo "3. Run the SQL in deployment/create_tables.sql"
echo "4. Test all functionality"
echo "5. Monitor error logs for any issues"

echo ""
echo -e "${YELLOW}Important: The site now requires:${NC}"
echo "- PHP 7.4+ (for password_hash functions)"
echo "- mysqli extension"
echo "- Session support"
echo "- Write permissions for security logs (if file logging is used)"