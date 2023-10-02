#!/bin/bash

# Define deployment directory and repository URL
DEPLOY_DIR="/home/efcas/public_html/filemgt.e-fcas.net"
REPO_URL="https://github.com/Sammiiie/file_management.git"
BRANCH="main"

# Email notification settings
TO_EMAIL="olochesamuel2@gmail.com"
FROM_EMAIL="tech@e-fcas.net"
SUBJECT="Deployment Complete"

# Navigate to the deployment directory
cd $DEPLOY_DIR

# Pull the latest code from the Git repository
git pull origin $BRANCH

# Install or update dependencies (if needed)
# For example, if you use Composer for PHP projects:
# composer install

# Perform any additional setup or migration tasks
# For example, if you have a database migration script:
# php artisan migrate

# Clean up any temporary files or caches (if needed)
# For example, if you're using Laravel:
# php artisan cache:clear

# Restart your web server or application service
# For example, if you're using Apache:
# systemctl restart apache2

# Optionally, send a notification that the deployment is complete
# For example, you can use a messaging service like Slack or email

# Send a deployment completion email
echo "File Management Deployment completed." | mail -s "$SUBJECT" -r "$FROM_EMAIL" "$TO_EMAIL"

# Exit the script
exit 0
