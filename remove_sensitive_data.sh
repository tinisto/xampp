#!/bin/bash

# Script to remove sensitive data from git history
echo "🔒 Removing sensitive data from git history..."

# Create a backup branch
git branch backup-before-cleanup

# Remove the files with sensitive data from all commits
git filter-branch --force --index-filter \
  'git rm --cached --ignore-unmatch .env.production env_backup.txt' \
  --prune-empty --tag-name-filter cat -- --all

# Clean up
rm -rf .git/refs/original/
git reflog expire --expire=now --all
git gc --prune=now --aggressive

echo "✅ Sensitive data removed from history"
echo "⚠️  You'll need to force push to update the remote repository"
echo "Run: git push --force --all"
echo "And: git push --force --tags"