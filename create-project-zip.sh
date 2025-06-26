#!/bin/bash

# Script to create a zip archive of the FlashCard Pro project
# Includes .git directory but excludes gitignored files

# Set the project directory (current directory)
PROJECT_DIR="$(pwd)"
PROJECT_NAME="$(basename "$PROJECT_DIR")"
ZIP_NAME="${PROJECT_NAME}-$(date +%Y%m%d-%H%M%S).zip"

echo "Creating zip archive: $ZIP_NAME"
echo "Project directory: $PROJECT_DIR"

# Create the zip file excluding gitignored patterns
# Note: We need to go to parent directory to include the project folder in the zip
cd ..

zip -r "$ZIP_NAME" "$PROJECT_NAME" \
    -x "*.log" \
    -x "*/.DS_Store" \
    -x "*.DS_Store" \
    -x "$PROJECT_NAME/.env" \
    -x "$PROJECT_NAME/.env.backup" \
    -x "$PROJECT_NAME/.env.production" \
    -x "$PROJECT_NAME/.phpactor.json" \
    -x "$PROJECT_NAME/.phpunit.result.cache" \
    -x "$PROJECT_NAME/.fleet/*" \
    -x "$PROJECT_NAME/.idea/*" \
    -x "$PROJECT_NAME/.nova/*" \
    -x "$PROJECT_NAME/.phpunit.cache/*" \
    -x "$PROJECT_NAME/.vscode/*" \
    -x "$PROJECT_NAME/.zed/*" \
    -x "$PROJECT_NAME/auth.json" \
    -x "$PROJECT_NAME/node_modules/*" \
    -x "$PROJECT_NAME/public/build/*" \
    -x "$PROJECT_NAME/public/hot" \
    -x "$PROJECT_NAME/public/storage/*" \
    -x "$PROJECT_NAME/storage/*.key" \
    -x "$PROJECT_NAME/storage/pail/*" \
    -x "$PROJECT_NAME/vendor/*" \
    -x "$PROJECT_NAME/Homestead.json" \
    -x "$PROJECT_NAME/Homestead.yaml" \
    -x "*Thumbs.db" \
    -x "$PROJECT_NAME/storage/logs/*.log" \
    -x "$PROJECT_NAME/storage/framework/cache/*" \
    -x "$PROJECT_NAME/storage/framework/sessions/*" \
    -x "$PROJECT_NAME/storage/framework/testing/*" \
    -x "$PROJECT_NAME/storage/framework/views/*" \
    -x "$PROJECT_NAME/bootstrap/cache/*"

# Move the zip file to the project directory
mv "$ZIP_NAME" "$PROJECT_NAME/"

cd "$PROJECT_NAME"

echo ""
echo "✅ Zip archive created successfully: $ZIP_NAME"
echo "📁 Archive size: $(du -h "$ZIP_NAME" | cut -f1)"

# Show what's included (high-level overview)
echo ""
echo "📋 Archive contents overview:"
echo "   • Source code (PHP, JavaScript, CSS, Blade templates)"
echo "   • Configuration files"
echo "   • Database migrations and seeders"
echo "   • Tests"
echo "   • Documentation (README.md)"
echo "   • Git history (.git directory)"
echo ""
echo "❌ Excluded from archive:"
echo "   • node_modules/"
echo "   • vendor/"
echo "   • .env files"
echo "   • Log files"
echo "   • Build artifacts (public/build/)"
echo "   • Cache files"
echo "   • IDE configuration files"
echo ""
echo "🎉 Done! Your project is ready for sharing or backup."