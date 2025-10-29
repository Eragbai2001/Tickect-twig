#!/bin/bash

# Tickety-Twig Setup Script
set -e

echo "================================"
echo "Tickety-Twig Project Setup"
echo "================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

echo "✓ PHP and Composer detected"
echo ""

# Get the script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd "$SCRIPT_DIR"

echo "📦 Installing Composer dependencies..."
composer install

echo ""
echo "✓ Composer dependencies installed"
echo ""

echo "📁 Creating project structure..."

# Create necessary directories
mkdir -p src/App
mkdir -p src/Controllers
mkdir -p src/Middleware
mkdir -p templates/layouts
mkdir -p templates/pages
mkdir -p templates/components
mkdir -p public/css
mkdir -p public/js
mkdir -p public/images
mkdir -p var/cache
mkdir -p var/logs

echo "✓ Project directories created"
echo ""

echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Run the development server: php -S localhost:8000 -t public"
echo "2. Open http://localhost:8000 in your browser"
echo ""
