#!/bin/bash

# Remote server details
REMOTE_HOST="203.161.47.238"
REMOTE_USER="root"
REMOTE_PATH="/var/www/html/steveshop"

# First attempt to create the directory
echo "Creating directory on remote server..."
ssh ${REMOTE_USER}@${REMOTE_HOST} "mkdir -p ${REMOTE_PATH}"

# Check if the SSH command was successful
if [ $? -ne 0 ]; then
    echo "Failed to create directory via SSH. This could be due to:"
    echo "  - SSH service not running on the remote server"
    echo "  - Firewall blocking port 22"
    echo "  - Incorrect IP address or credentials"
    echo ""
    echo "Alternative options:"
    echo "1. Use FTP to create the directory first:"
    echo "   ftp ${REMOTE_HOST}"
    echo "   > login with your credentials"
    echo "   > mkdir -p ${REMOTE_PATH}"
    echo ""
    echo "2. Use cPanel or your hosting control panel to create the directory"
    echo ""
    echo "3. If you have web-based access, create a PHP script that creates the directory:"
    echo "   <?php mkdir('${REMOTE_PATH}', 0755, true); echo 'Directory created!'; ?>"
    echo ""
    echo "After creating the directory, run your rsync command again:"
    echo "rsync -avz --progress /local/path/ ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/"
    
    exit 1
fi

# If SSH was successful, run the rsync command
echo "Directory created successfully. Running rsync..."
rsync -avz --progress . ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/

echo "Transfer complete!" 