#!/bin/bash

# Auto-save script for CLAUDE_SESSION.md
# This script automatically commits changes to CLAUDE_SESSION.md every 5 minutes

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
WATCH_FILE="CLAUDE_SESSION.md"
COMMIT_INTERVAL=300  # 5 minutes in seconds
PROJECT_DIR="/Applications/XAMPP/xamppfiles/htdocs"

echo -e "${GREEN}Starting auto-save for $WATCH_FILE${NC}"
echo -e "${YELLOW}Auto-commit interval: $COMMIT_INTERVAL seconds${NC}"

# Change to project directory
cd "$PROJECT_DIR" || exit 1

# Function to check if file has changes
has_changes() {
    git diff --quiet "$WATCH_FILE" || git diff --cached --quiet "$WATCH_FILE"
    return $?
}

# Function to commit changes
commit_changes() {
    if has_changes; then
        echo -e "${YELLOW}Changes detected in $WATCH_FILE${NC}"
        
        # Add the file
        git add "$WATCH_FILE"
        
        # Create commit with timestamp
        TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
        git commit -m "Auto-save: Update CLAUDE_SESSION.md - $TIMESTAMP" --quiet
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Changes committed at $TIMESTAMP${NC}"
        else
            echo -e "${RED}✗ Failed to commit changes${NC}"
        fi
    fi
}

# Initial check
commit_changes

# Main loop
while true; do
    sleep "$COMMIT_INTERVAL"
    commit_changes
done