#!/usr/bin/env bash
set -euo pipefail

# Create storage directories and placeholders for Linux/macOS
BASE_DIR="$(pwd)"
PHP="php"

# Directories to create
mkdir -p storage/app/public/villas
mkdir -p storage/app/public/dine-relax/menus/sample
mkdir -p storage/app/public/dine-relax/blocks/restaurant/gallery
mkdir -p storage/app/public/dine-relax/hero
mkdir -p public/uploads/villas

# Create .gitkeep placeholders where missing
touch storage/app/public/villas/.gitkeep
touch storage/app/public/dine-relax/menus/sample/.gitkeep
touch storage/app/public/dine-relax/blocks/restaurant/gallery/.gitkeep
touch storage/app/public/dine-relax/hero/.gitkeep
touch public/uploads/villas/.gitkeep

# Ensure storage symlink exists
if [ ! -L public/storage ]; then
  echo "Linking storage: php artisan storage:link"
  $PHP artisan storage:link
else
  echo "public/storage already linked"
fi

echo "Created storage folders and placeholders."
