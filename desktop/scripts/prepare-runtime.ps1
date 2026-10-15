# Prepare Laravel runtime for Tauri packaging
$ErrorActionPreference = 'Stop'

$root = Resolve-Path (Join-Path $PSScriptRoot '..\..')
$desktop = Resolve-Path (Join-Path $PSScriptRoot '..')
$resourcesApp = Join-Path $desktop 'src-tauri\resources\app'

Write-Host "Project root: $root"
Write-Host "Preparing runtime at: $resourcesApp"

Push-Location $root
try {
    Write-Host "Building frontend assets..."
    npm run build

    Write-Host "Clearing Laravel caches for packaging..."
    php artisan optimize:clear
}
finally {
    Pop-Location
}

if (Test-Path $resourcesApp) {
    Remove-Item $resourcesApp -Recurse -Force
}
New-Item -ItemType Directory -Path $resourcesApp -Force | Out-Null

$excludeDirs = @(
    'node_modules',
    '.git',
    'desktop',
    'tests',
    'storage\logs',
    'storage\framework\cache\data',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\framework\testing'
)

$robocopyArgs = @(
    $root,
    $resourcesApp,
    '/E',
    '/XD', 'node_modules', '.git', 'desktop', 'tests',
    '/XF', '.env', '.env.backup', 'phpunit.xml', 'phase*.php', '*.md',
    '/NFL', '/NDL', '/NJH', '/NJS', '/nc', '/ns', '/np'
)

Write-Host "Copying application files..."
& robocopy @robocopyArgs | Out-Null
# robocopy exit codes 0-7 are success
if ($LASTEXITCODE -ge 8) {
    throw "robocopy failed with exit code $LASTEXITCODE"
}

# Ensure writable storage skeleton exists in package
$storageDirs = @(
    'storage\app\public',
    'storage\app\backups',
    'storage\framework\cache',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\logs'
)
foreach ($dir in $storageDirs) {
    New-Item -ItemType Directory -Path (Join-Path $resourcesApp $dir) -Force | Out-Null
}

# Include a starter SQLite database if present
$srcDb = Join-Path $root 'database\database.sqlite'
$dstDbDir = Join-Path $resourcesApp 'database'
New-Item -ItemType Directory -Path $dstDbDir -Force | Out-Null
if (Test-Path $srcDb) {
    Copy-Item $srcDb (Join-Path $dstDbDir 'database.sqlite') -Force
} else {
    New-Item -ItemType File -Path (Join-Path $dstDbDir 'database.sqlite') -Force | Out-Null
}

# Production .env for packaged app (DB path overridden at runtime by Tauri)
$appKey = ''
$rootEnv = Join-Path $root '.env'
if (Test-Path $rootEnv) {
    $keyLine = Select-String -Path $rootEnv -Pattern '^APP_KEY=.+' | Select-Object -First 1
    if ($keyLine) {
        $appKey = ($keyLine.Line -replace '^APP_KEY=', '').Trim()
    }
}
if (-not $appKey) {
    Push-Location $root
    try {
        $appKey = (php artisan key:generate --show).Trim()
    } finally {
        Pop-Location
    }
}

$envContent = @"
APP_NAME=`"POS Inventory`"
APP_ENV=production
APP_KEY=$appKey
APP_DEBUG=false
APP_URL=http://127.0.0.1:8765

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
"@

Set-Content -Path (Join-Path $resourcesApp '.env') -Value $envContent -Encoding UTF8

# Remove machine-specific compiled caches from the package
$cacheFiles = Get-ChildItem (Join-Path $resourcesApp 'bootstrap\cache') -Filter '*.php' -ErrorAction SilentlyContinue
foreach ($file in $cacheFiles) {
    Remove-Item $file.FullName -Force
}

Write-Host "Runtime prepared successfully."
Write-Host "Next: npm run download-php (if needed), then npm run package"
