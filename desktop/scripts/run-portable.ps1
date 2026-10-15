# Portable launcher without Tauri (for verifying PHP + Laravel packaging)
$ErrorActionPreference = 'Stop'
$desktop = Resolve-Path (Join-Path $PSScriptRoot '..')
$app = Join-Path $desktop 'src-tauri\resources\app'
$php = Join-Path $desktop 'src-tauri\resources\php\php.exe'

if (-not (Test-Path $app)) {
    Write-Host 'Runtime missing. Running prepare-runtime.ps1...'
    & powershell -ExecutionPolicy Bypass -File (Join-Path $PSScriptRoot 'prepare-runtime.ps1')
}
if (-not (Test-Path $php)) {
    Write-Host 'PHP missing. Running download-php.ps1...'
    & powershell -ExecutionPolicy Bypass -File (Join-Path $PSScriptRoot 'download-php.ps1')
}

$port = 8765
$url = "http://127.0.0.1:$port"
Write-Host "Starting POS at $url"
Start-Process $url
Set-Location $app
& $php artisan serve --host=127.0.0.1 --port=$port
