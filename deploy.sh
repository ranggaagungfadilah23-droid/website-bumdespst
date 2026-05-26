#!/bin/bash

# Deploy Script untuk Railway - BUMDes Putra Samudra Patimban
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production

set -e

ENVIRONMENT=${1:-production}

echo "🚀 Starting deployment to Railway ($ENVIRONMENT)..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo -e "${RED}❌ Railway CLI not found!${NC}"
    echo "Install it with: npm i -g @railway/cli"
    exit 1
fi

echo -e "${GREEN}✅ Railway CLI found${NC}"

# Check if logged in
if ! railway whoami &> /dev/null; then
    echo -e "${YELLOW}⚠️  Not logged in to Railway${NC}"
    echo "Logging in..."
    railway login
fi

echo -e "${GREEN}✅ Logged in to Railway${NC}"

# Confirm deployment
echo ""
echo -e "${YELLOW}⚠️  You are about to deploy to $ENVIRONMENT${NC}"
echo "This will:"
echo "  1. Push code to Railway"
echo "  2. Build the application"
echo "  3. Run migrations"
echo "  4. Cache configurations"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Deployment cancelled."
    exit 1
fi

# Pre-deployment checks
echo ""
echo "📋 Running pre-deployment checks..."

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    exit 1
fi

# Check if APP_DEBUG is false
if grep -q "APP_DEBUG=true" .env; then
    echo -e "${RED}❌ APP_DEBUG is true! Must be false for production.${NC}"
    exit 1
fi

# Check if APP_ENV is production
if ! grep -q "APP_ENV=production" .env; then
    echo -e "${YELLOW}⚠️  APP_ENV is not production${NC}"
fi

echo -e "${GREEN}✅ Pre-deployment checks passed${NC}"

# Git checks
echo ""
echo "📦 Checking Git status..."

if [[ -n $(git status -s) ]]; then
    echo -e "${YELLOW}⚠️  You have uncommitted changes:${NC}"
    git status -s
    echo ""
    read -p "Commit and push changes? (y/n) " -n 1 -r
    echo ""
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        read -p "Enter commit message: " commit_message
        git add .
        git commit -m "$commit_message"
        git push origin main
        echo -e "${GREEN}✅ Changes committed and pushed${NC}"
    else
        echo -e "${YELLOW}⚠️  Deploying with uncommitted changes${NC}"
    fi
else
    echo -e "${GREEN}✅ No uncommitted changes${NC}"
    
    # Push to remote
    echo "Pushing to remote..."
    git push origin main
fi

# Deploy to Railway
echo ""
echo "🚀 Deploying to Railway..."
railway up

echo -e "${GREEN}✅ Code deployed${NC}"

# Wait for deployment
echo ""
echo "⏳ Waiting for deployment to complete..."
sleep 10

# Run migrations
echo ""
echo "🗄️  Running database migrations..."
railway run php artisan migrate --force

echo -e "${GREEN}✅ Migrations completed${NC}"

# Cache configurations
echo ""
echo "⚡ Caching configurations..."
railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache

echo -e "${GREEN}✅ Configurations cached${NC}"

# Generate storage link
echo ""
echo "🔗 Generating storage link..."
railway run php artisan storage:link

echo -e "${GREEN}✅ Storage link generated${NC}"

# Clear old caches (optional)
echo ""
read -p "Clear old caches? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    railway run php artisan cache:clear
    railway run php artisan view:clear
    echo -e "${GREEN}✅ Caches cleared${NC}"
fi

# Get deployment URL
echo ""
echo "🌐 Getting deployment URL..."
DEPLOY_URL=$(railway domain)

# Summary
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🌐 Application URL: $DEPLOY_URL"
echo ""
echo "📊 Next steps:"
echo "  1. Test the application: $DEPLOY_URL"
echo "  2. Check logs: railway logs"
echo "  3. Monitor metrics: railway status"
echo ""
echo "🐛 Troubleshooting:"
echo "  - View logs: railway logs --follow"
echo "  - Restart app: railway restart"
echo "  - Rollback: git revert HEAD && ./deploy.sh"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Open in browser
read -p "Open application in browser? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    if command -v xdg-open &> /dev/null; then
        xdg-open "$DEPLOY_URL"
    elif command -v open &> /dev/null; then
        open "$DEPLOY_URL"
    elif command -v start &> /dev/null; then
        start "$DEPLOY_URL"
    else
        echo "Please open manually: $DEPLOY_URL"
    fi
fi

echo ""
echo "🎉 Happy deploying!"
