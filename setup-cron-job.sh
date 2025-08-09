#!/bin/bash
# Comment System Cron Job Setup Script
# This script sets up the email notification cron job

echo "========================================"
echo "Comment System Cron Job Setup"
echo "========================================"
echo ""

# Get the current directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
CRON_SCRIPT_PATH="/home/11066451/public_html/cron/send-comment-notifications.php"

# Check if running as root or with sudo
if [ "$EUID" -eq 0 ]; then 
   echo "✓ Running with appropriate permissions"
else
   echo "⚠️  Warning: You may need sudo permissions to edit crontab"
fi

# Create the cron job entry
CRON_JOB="*/10 * * * * /usr/bin/php $CRON_SCRIPT_PATH >> /var/log/comment-notifications.log 2>&1"

echo "Setting up cron job for email notifications..."
echo ""
echo "The following cron job will be added:"
echo "$CRON_JOB"
echo ""
echo "This will:"
echo "- Run every 10 minutes"
echo "- Send email notifications for new replies and mentions"
echo "- Log output to /var/log/comment-notifications.log"
echo ""

# Check if cron job already exists
if crontab -l 2>/dev/null | grep -q "send-comment-notifications.php"; then
    echo "⚠️  Warning: A cron job for comment notifications already exists:"
    crontab -l | grep "send-comment-notifications.php"
    echo ""
    read -p "Do you want to replace it? (y/n): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Skipping cron job setup."
        exit 0
    fi
    # Remove existing job
    crontab -l | grep -v "send-comment-notifications.php" | crontab -
fi

# Add the cron job
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

if [ $? -eq 0 ]; then
    echo "✅ Cron job added successfully!"
else
    echo "❌ Failed to add cron job. Please add manually:"
    echo "$CRON_JOB"
    exit 1
fi

# Create log file with proper permissions
sudo touch /var/log/comment-notifications.log
sudo chmod 666 /var/log/comment-notifications.log

echo ""
echo "✅ Setup complete!"
echo ""
echo "To verify the cron job, run:"
echo "crontab -l | grep comment-notifications"
echo ""
echo "To monitor the logs, run:"
echo "tail -f /var/log/comment-notifications.log"
echo ""
echo "To test the script manually, run:"
echo "php $CRON_SCRIPT_PATH"
echo ""
echo "========================================"