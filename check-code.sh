#!/bin/bash

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 PHP Code Quality Check for 11klassniki${NC}"
echo "========================================="

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo -e "${RED}❌ Vendor directory not found. Run 'composer install' first.${NC}"
    exit 1
fi

# Run PHPStan
echo -e "\n${YELLOW}📊 Running PHPStan (Type Checking)...${NC}"
./vendor/bin/phpstan analyse --no-progress
STAN_EXIT=$?

if [ $STAN_EXIT -eq 0 ]; then
    echo -e "${GREEN}✅ PHPStan: No errors found${NC}"
else
    echo -e "${RED}❌ PHPStan: Found type errors${NC}"
fi

# Run PHP_CodeSniffer
echo -e "\n${YELLOW}🎨 Running PHP_CodeSniffer (Code Style)...${NC}"
./vendor/bin/phpcs --report=summary
CS_EXIT=$?

if [ $CS_EXIT -eq 0 ]; then
    echo -e "${GREEN}✅ PHP_CodeSniffer: Code style is good${NC}"
else
    echo -e "${RED}❌ PHP_CodeSniffer: Code style issues found${NC}"
    echo -e "${YELLOW}💡 Tip: Run './vendor/bin/php-cs-fixer fix' to auto-fix many issues${NC}"
fi

# Summary
echo -e "\n${BLUE}Summary:${NC}"
if [ $STAN_EXIT -eq 0 ] && [ $CS_EXIT -eq 0 ]; then
    echo -e "${GREEN}✅ All checks passed! Your code is clean.${NC}"
    exit 0
else
    echo -e "${RED}❌ Some checks failed. Please fix the issues above.${NC}"
    exit 1
fi