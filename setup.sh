#!/bin/bash

# Flashcard Pro - Project Setup Script
# This script sets up the development environment for the Flashcard Pro Laravel application

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="flashcard.local"
APP_PORT="8080" # Custom port to avoid conflicts
DB_PORT="3307"  # Using 3307 to avoid conflicts with local MySQL

echo -e "${BLUE}🚀 Flashcard Pro Setup Script${NC}"
echo "=================================="

# Function to print status messages
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if running on macOS
if [[ "$OSTYPE" != "darwin"* ]]; then
    print_error "This script is designed for macOS. Please adapt for your OS."
    exit 1
fi

# Check prerequisites
echo -e "\n${BLUE}Checking prerequisites...${NC}"

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker Desktop."
    exit 1
fi
print_status "Docker is running"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi
print_status "Composer is available"

# Create required directories
echo -e "\n${BLUE}Creating required directories...${NC}"
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set proper permissions
chmod -R 775 storage bootstrap/cache
print_status "Required directories created with proper permissions"

# Install dependencies
echo -e "\n${BLUE}Installing dependencies...${NC}"
if [ ! -d "vendor" ]; then
    composer install --no-interaction
    print_status "Composer dependencies installed"
else
    print_status "Composer dependencies already installed"
fi

# Set up environment file
echo -e "\n${BLUE}Setting up environment...${NC}"
if [ ! -f ".env" ]; then
    cp .env.example .env
    print_status "Environment file created from example"
else
    print_status "Environment file already exists"
fi

# Update .env with custom settings
echo -e "\n${BLUE}Configuring environment variables...${NC}"

# Update APP_URL, ports, and database settings
sed -i '' "s|APP_URL=.*|APP_URL=http://${DOMAIN}:${APP_PORT}|g" .env
sed -i '' "s|APP_PORT=.*|APP_PORT=${APP_PORT}|g" .env
sed -i '' "s|FORWARD_DB_PORT=.*|FORWARD_DB_PORT=${DB_PORT}|g" .env

# Add missing environment variables if they don't exist
if ! grep -q "APP_PORT=" .env; then
    echo "APP_PORT=${APP_PORT}" >> .env
fi

if ! grep -q "FORWARD_DB_PORT=" .env; then
    echo "FORWARD_DB_PORT=${DB_PORT}" >> .env
fi

print_status "Environment variables configured"

# Generate application key
echo -e "\n${BLUE}Generating application key...${NC}"
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --ansi
    print_status "Application key generated"
else
    print_status "Application key already exists"
fi

# Set up custom domain
echo -e "\n${BLUE}Setting up custom domain...${NC}"

# Check if domain already exists in hosts file
if grep -q "$DOMAIN" /etc/hosts; then
    print_status "Custom domain already configured in /etc/hosts"
else
    print_warning "Adding custom domain to /etc/hosts (requires sudo)"
    echo -e "\n127.0.0.1    $DOMAIN" | sudo tee -a /etc/hosts > /dev/null
    print_status "Custom domain added to /etc/hosts"
fi

# Stop any existing containers
echo -e "\n${BLUE}Stopping any existing containers...${NC}"
./vendor/bin/sail down > /dev/null 2>&1 || true
print_status "Existing containers stopped"

# Start the application
echo -e "\n${BLUE}Starting the application...${NC}"
./vendor/bin/sail up -d

# Wait for database to be ready
echo -e "\n${BLUE}Waiting for database to be ready...${NC}"
sleep 10

# Run migrations
echo -e "\n${BLUE}Running database migrations...${NC}"
./vendor/bin/sail artisan migrate --force
print_status "Database migrations completed"

# Run seeders
echo -e "\n${BLUE}Seeding database...${NC}"
./vendor/bin/sail artisan db:seed --force
print_status "Database seeded"

# Install npm dependencies and build assets
echo -e "\n${BLUE}Installing frontend dependencies...${NC}"
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
print_status "Frontend assets built"

echo -e "\n${GREEN}🎉 Setup completed successfully!${NC}"
echo "=================================="
echo -e "Your application is now running at: ${GREEN}http://${DOMAIN}:${APP_PORT}${NC}"
echo -e "Database is accessible on port: ${GREEN}${DB_PORT}${NC}"
echo ""
echo "Available commands:"
echo "  ./vendor/bin/sail up -d         # Start the application"
echo "  ./vendor/bin/sail down          # Stop the application"
echo "  ./vendor/bin/sail artisan       # Run artisan commands"
echo "  ./vendor/bin/sail npm           # Run npm commands"
echo "  ./vendor/bin/sail php           # Run PHP commands"
echo "  ./vendor/bin/sail artisan pail  # Watch logs"
echo ""
echo -e "${BLUE}💡 Pro Tip: Create a Sail alias for easier commands${NC}"
echo ""
echo "Instead of typing './vendor/bin/sail' every time, add an alias to your shell config (~/.zshrc or ~/.bashrc):"
echo ""
echo "  echo \"alias sail='sh \\\$([ -f sail ] && echo sail || echo vendor/bin/sail)'\" >> ~/.zshrc"
echo "  source ~/.zshrc"
echo ""
echo "Then you can use shorter commands like:"
echo ""
echo "  sail up -d        # Instead of ./vendor/bin/sail up -d"
echo "  sail artisan      # Instead of ./vendor/bin/sail artisan"
echo "  sail npm run dev  # Instead of ./vendor/bin/sail npm run dev"
echo ""
echo -e "${YELLOW}Note: If you encounter any issues, run './vendor/bin/sail down' and then './vendor/bin/sail up -d'${NC}"
echo ""