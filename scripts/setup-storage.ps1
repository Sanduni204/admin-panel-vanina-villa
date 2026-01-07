Param()

# PowerShell script to create storage folders and placeholders on Windows
$root = Split-Path -Path $MyInvocation.MyCommand.Definition -Parent | Split-Path -Parent
Set-Location $root

$dirs = @(
  'storage/app/public/villas',
  'storage/app/public/dine-relax/menus/sample',
  'storage/app/public/dine-relax/blocks/restaurant/gallery',
  'storage/app/public/dine-relax/hero',
  'public/uploads/villas'
)

foreach ($d in $dirs) {
  if (-not (Test-Path $d)) {
    New-Item -ItemType Directory -Path $d -Force | Out-Null
  }
}

$files = @(
  'storage/app/public/villas/.gitkeep',
  'storage/app/public/dine-relax/menus/sample/.gitkeep',
  'storage/app/public/dine-relax/blocks/restaurant/gallery/.gitkeep',
  'storage/app/public/dine-relax/hero/.gitkeep',
  'public/uploads/villas/.gitkeep'
)

foreach ($f in $files) {
  if (-not (Test-Path $f)) {
    New-Item -ItemType File -Path $f -Force | Out-Null
  }
}

# Run artisan storage:link if missing
if (-not (Test-Path "public\storage")) {
  Write-Output "Creating storage symlink via php artisan storage:link"
  php artisan storage:link
} else {
  Write-Output "public\storage already exists"
}

Write-Output "Storage folders and placeholders created."
