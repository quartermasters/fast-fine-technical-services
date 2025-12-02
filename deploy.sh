#!/bin/bash

###############################################################################
# Fast and Fine Technical Services FZE - Production Deployment Script
#
# This script automates the deployment process to a production server
#
# Usage:
#   chmod +x deploy.sh
#   ./deploy.sh [environment]
#
# Environments:
#   staging    - Deploy to staging server for testing
#   production - Deploy to live production server
#
# @package FastAndFine
# @version 1.0.0
###############################################################################

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT="${1:-production}"
PROJECT_NAME="fast-fine-website"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Server Configuration (Update these with your actual values)
if [ "$ENVIRONMENT" = "production" ]; then
    SERVER_HOST="your-production-server.com"
    SERVER_USER="your-username"
    SERVER_PATH="/var/www/html/fastandfine.com"
    SERVER_PORT="22"
elif [ "$ENVIRONMENT" = "staging" ]; then
    SERVER_HOST="staging.your-server.com"
    SERVER_USER="your-username"
    SERVER_PATH="/var/www/html/staging.fastandfine.com"
    SERVER_PORT="22"
else
    echo -e "${RED}Error: Invalid environment '${ENVIRONMENT}'${NC}"
    echo -e "${YELLOW}Usage: ./deploy.sh [staging|production]${NC}"
    exit 1
fi

# Banner
echo -e "${CYAN}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Fast & Fine Deployment Script v1.0.0"
echo "  Environment: ${ENVIRONMENT}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${NC}"

# Confirmation
echo -e "${YELLOW}You are about to deploy to ${ENVIRONMENT} environment${NC}"
echo -e "${BLUE}Server: ${NC}${SERVER_HOST}"
echo -e "${BLUE}Path:   ${NC}${SERVER_PATH}"
echo ""
read -p "Continue? (yes/no): " -r
echo
if [[ ! $REPLY =~ ^[Yy]es$ ]]; then
    echo -e "${RED}Deployment cancelled${NC}"
    exit 0
fi

# Step 1: Pre-deployment checks
echo -e "${CYAN}━━━ Step 1: Pre-deployment Checks ━━━${NC}"

# Check if git repo is clean
if [ -n "$(git status --porcelain)" ]; then
    echo -e "${YELLOW}⚠ Warning: Git working directory is not clean${NC}"
    read -p "Continue anyway? (yes/no): " -r
    if [[ ! $REPLY =~ ^[Yy]es$ ]]; then
        echo -e "${RED}Deployment cancelled${NC}"
        exit 1
    fi
fi

# Check if on correct branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$ENVIRONMENT" = "production" ] && [ "$CURRENT_BRANCH" != "main" ]; then
    echo -e "${RED}Error: Must be on 'main' branch to deploy to production${NC}"
    echo -e "${YELLOW}Current branch: ${CURRENT_BRANCH}${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Git checks passed${NC}"

# Check if Node.js is available for build
if ! command -v node &> /dev/null; then
    echo -e "${YELLOW}⚠ Warning: Node.js not found, build step will be skipped${NC}"
    BUILD_AVAILABLE=false
else
    BUILD_AVAILABLE=true
    echo -e "${GREEN}✓ Node.js found${NC}"
fi

# Check if SSH connection works
if ! ssh -q -p $SERVER_PORT $SERVER_USER@$SERVER_HOST exit; then
    echo -e "${RED}Error: Cannot connect to ${SERVER_HOST}${NC}"
    echo -e "${YELLOW}Check your SSH credentials and server access${NC}"
    exit 1
fi

echo -e "${GREEN}✓ SSH connection successful${NC}"
echo ""

# Step 2: Build production assets
if [ "$BUILD_AVAILABLE" = true ]; then
    echo -e "${CYAN}━━━ Step 2: Building Production Assets ━━━${NC}"

    # Install dependencies if needed
    if [ ! -d "node_modules" ]; then
        echo -e "${BLUE}Installing Node.js dependencies...${NC}"
        npm install
    fi

    # Build minified assets
    echo -e "${BLUE}Building production assets...${NC}"
    NODE_ENV=production npm run build

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Build successful${NC}"
    else
        echo -e "${RED}Error: Build failed${NC}"
        exit 1
    fi
    echo ""
else
    echo -e "${YELLOW}━━━ Step 2: Skipping Build (Node.js not available) ━━━${NC}"
    echo ""
fi

# Step 3: Create deployment archive
echo -e "${CYAN}━━━ Step 3: Creating Deployment Archive ━━━${NC}"

DEPLOY_DIR="deploy_${TIMESTAMP}"
ARCHIVE_NAME="${PROJECT_NAME}_${ENVIRONMENT}_${TIMESTAMP}.tar.gz"

mkdir -p "$DEPLOY_DIR"

echo -e "${BLUE}Copying files to deployment directory...${NC}"

# Copy files (excluding unnecessary ones)
rsync -av --progress \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='deploy_*' \
    --exclude='*.tar.gz' \
    --exclude='.DS_Store' \
    --exclude='*.log' \
    --exclude='deploy.sh' \
    --exclude='optimize-images.sh' \
    --exclude='TESTING.md' \
    --exclude='PROJECT_SUMMARY.md' \
    --exclude='assets/images/original' \
    ./ "$DEPLOY_DIR/"

# Create archive
echo -e "${BLUE}Creating compressed archive...${NC}"
tar -czf "$ARCHIVE_NAME" -C "$DEPLOY_DIR" .

ARCHIVE_SIZE=$(du -h "$ARCHIVE_NAME" | cut -f1)
echo -e "${GREEN}✓ Archive created: ${ARCHIVE_NAME} (${ARCHIVE_SIZE})${NC}"
echo ""

# Step 4: Create backup on server
echo -e "${CYAN}━━━ Step 4: Creating Server Backup ━━━${NC}"

BACKUP_DIR="${SERVER_PATH}_backup_${TIMESTAMP}"

ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST <<EOF
    if [ -d "$SERVER_PATH" ]; then
        echo "Creating backup of existing deployment..."
        cp -r "$SERVER_PATH" "$BACKUP_DIR"
        echo "✓ Backup created: $BACKUP_DIR"
    else
        echo "No existing deployment found, skipping backup"
    fi
EOF

echo ""

# Step 5: Upload to server
echo -e "${CYAN}━━━ Step 5: Uploading to Server ━━━${NC}"

echo -e "${BLUE}Uploading ${ARCHIVE_NAME}...${NC}"
scp -P $SERVER_PORT "$ARCHIVE_NAME" "$SERVER_USER@$SERVER_HOST:/tmp/"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Upload successful${NC}"
else
    echo -e "${RED}Error: Upload failed${NC}"
    exit 1
fi
echo ""

# Step 6: Extract and deploy on server
echo -e "${CYAN}━━━ Step 6: Extracting Files on Server ━━━${NC}"

ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST <<EOF
    set -e

    # Create deployment directory if doesn't exist
    sudo mkdir -p "$SERVER_PATH"
    cd "$SERVER_PATH"

    # Clear existing files (except uploads and config)
    echo "Clearing old files..."
    sudo find . -mindepth 1 -maxdepth 1 ! -name 'uploads' ! -name 'config.php' -exec rm -rf {} +

    # Extract new files
    echo "Extracting archive..."
    sudo tar -xzf /tmp/$ARCHIVE_NAME -C "$SERVER_PATH"

    # Set proper permissions
    echo "Setting file permissions..."
    sudo chown -R www-data:www-data "$SERVER_PATH"
    sudo find "$SERVER_PATH" -type d -exec chmod 755 {} \;
    sudo find "$SERVER_PATH" -type f -exec chmod 644 {} \;
    sudo chmod -R 775 "$SERVER_PATH/uploads"
    sudo chmod -R 775 "$SERVER_PATH/database/backups"

    # Clean up
    rm /tmp/$ARCHIVE_NAME

    echo "✓ Deployment extracted successfully"
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Deployment successful${NC}"
else
    echo -e "${RED}Error: Deployment failed${NC}"
    echo -e "${YELLOW}Backup available at: ${BACKUP_DIR}${NC}"
    exit 1
fi
echo ""

# Step 7: Run post-deployment tasks
echo -e "${CYAN}━━━ Step 7: Post-Deployment Tasks ━━━${NC}"

ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST <<EOF
    cd "$SERVER_PATH"

    # Update config for production
    if [ -f "config.php" ]; then
        echo "Updating environment to production..."
        sudo sed -i "s/define('ENVIRONMENT', 'development');/define('ENVIRONMENT', 'production');/" config.php
        sudo sed -i "s/define('DEBUG_MODE', true);/define('DEBUG_MODE', false);/" config.php
    fi

    # Clear any cache if exists
    if [ -d "cache" ]; then
        echo "Clearing cache..."
        sudo rm -rf cache/*
    fi

    # Restart PHP-FPM
    echo "Restarting PHP-FPM..."
    sudo systemctl restart php8.1-fpm || sudo systemctl restart php-fpm || echo "PHP-FPM restart failed (may need manual restart)"

    # Restart Apache/Nginx
    if systemctl is-active --quiet apache2; then
        echo "Restarting Apache..."
        sudo systemctl restart apache2
    elif systemctl is-active --quiet nginx; then
        echo "Restarting Nginx..."
        sudo systemctl restart nginx
    fi

    echo "✓ Post-deployment tasks completed"
EOF

echo ""

# Step 8: Verify deployment
echo -e "${CYAN}━━━ Step 8: Verifying Deployment ━━━${NC}"

# Check if website is accessible
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "https://${SERVER_HOST}")

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ Website is accessible (HTTP $HTTP_CODE)${NC}"
else
    echo -e "${YELLOW}⚠ Website returned HTTP $HTTP_CODE${NC}"
fi

# Check database connection
ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST <<EOF
    cd "$SERVER_PATH"
    php -r "
        require_once 'config.php';
        require_once 'includes/db-connect.php';
        try {
            \$db = Database::getInstance()->getConnection();
            echo '✓ Database connection successful\n';
        } catch (Exception \$e) {
            echo '✗ Database connection failed: ' . \$e->getMessage() . '\n';
            exit(1);
        }
    "
EOF

echo ""

# Step 9: Cleanup
echo -e "${CYAN}━━━ Step 9: Cleanup ━━━${NC}"

echo -e "${BLUE}Removing local temporary files...${NC}"
rm -rf "$DEPLOY_DIR"
rm "$ARCHIVE_NAME"

echo -e "${GREEN}✓ Cleanup completed${NC}"
echo ""

# Deployment summary
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Deployment Completed Successfully!${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${BLUE}Environment:    ${NC}${ENVIRONMENT}"
echo -e "${BLUE}Server:         ${NC}${SERVER_HOST}"
echo -e "${BLUE}Deployed to:    ${NC}${SERVER_PATH}"
echo -e "${BLUE}Timestamp:      ${NC}${TIMESTAMP}"
echo -e "${BLUE}Backup location:${NC}${BACKUP_DIR}"
echo ""
echo -e "${BLUE}Website URL:    ${NC}https://${SERVER_HOST}"
echo -e "${BLUE}Admin Panel:    ${NC}https://${SERVER_HOST}/admin/"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "1. Visit the website and verify all pages load correctly"
echo -e "2. Test forms (contact, booking) to ensure they work"
echo -e "3. Check admin panel login and dashboard"
echo -e "4. Verify email sending is configured correctly"
echo -e "5. Test on multiple devices and browsers"
echo -e "6. Submit sitemap to Google Search Console"
echo -e "7. Configure monitoring and backups"
echo ""
echo -e "${GREEN}Deployment log saved${NC}"
echo ""
