# Full desktop package pipeline for POS Inventory (Tauri + PHP + Laravel + SQLite)
$ErrorActionPreference = 'Stop'

$desktop = Resolve-Path (Join-Path $PSScriptRoot '..')
Set-Location $desktop

Write-Host "=== 1) Prepare Laravel runtime ==="
& powershell -ExecutionPolicy Bypass -File (Join-Path $PSScriptRoot 'prepare-runtime.ps1')

$phpExe = Join-Path $desktop 'src-tauri\resources\php\php.exe'
if (-not (Test-Path $phpExe)) {
    Write-Host "=== 2) Download portable PHP ==="
    & powershell -ExecutionPolicy Bypass -File (Join-Path $PSScriptRoot 'download-php.ps1')
} else {
    Write-Host "=== 2) Portable PHP already present ==="
}

Write-Host "=== 3) Install desktop npm dependencies ==="
if (-not (Test-Path (Join-Path $desktop 'node_modules'))) {
    npm install
}

Write-Host "=== 4) Build Tauri installer ==="
npm run build

Write-Host ""
Write-Host "Build complete. Installers (if successful) are under:"
Write-Host "  desktop\src-tauri\target\release\bundle\nsis\"
Write-Host "  desktop\src-tauri\target\release\bundle\msi\"
