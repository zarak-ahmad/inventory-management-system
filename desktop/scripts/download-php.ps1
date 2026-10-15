# Download portable PHP 8.3 NTS for Windows x64 into src-tauri/resources/php
$ErrorActionPreference = 'Stop'

$desktop = Resolve-Path (Join-Path $PSScriptRoot '..')
$phpDir = Join-Path $desktop 'src-tauri\resources\php'
$tmpZip = Join-Path $env:TEMP 'php-pos-portable.zip'

# Official Windows PHP builds (NTS x64 Thread Safe alternative also works; NTS preferred for CLI)
$phpUrl = 'https://windows.php.net/downloads/releases/php-8.3.33-nts-Win32-vs16-x64.zip'

Write-Host "Downloading PHP from:"
Write-Host $phpUrl

New-Item -ItemType Directory -Path $phpDir -Force | Out-Null

try {
    if (Get-Command curl.exe -ErrorAction SilentlyContinue) {
        & curl.exe -L --retry 3 --retry-delay 2 -o $tmpZip $phpUrl
        if ($LASTEXITCODE -ne 0) { throw "curl exited with code $LASTEXITCODE" }
    } else {
        Invoke-WebRequest -Uri $phpUrl -OutFile $tmpZip -UseBasicParsing
    }
} catch {
    Write-Warning "Primary URL failed. Please download PHP 8.3 NTS x64 zip manually and extract into:"
    Write-Warning $phpDir
    Write-Warning "Current release example: php-8.3.33-nts-Win32-vs16-x64.zip from https://windows.php.net/downloads/releases/"
    throw
}

if (Test-Path (Join-Path $phpDir 'php.exe')) {
    Remove-Item (Join-Path $phpDir '*') -Recurse -Force
}

Expand-Archive -Path $tmpZip -DestinationPath $phpDir -Force
Remove-Item $tmpZip -Force -ErrorAction SilentlyContinue

$phpExe = Join-Path $phpDir 'php.exe'
if (-not (Test-Path $phpExe)) {
    throw "php.exe not found after extract in $phpDir"
}

# Enable common extensions used by Laravel
$iniProduction = Join-Path $phpDir 'php.ini-production'
$ini = Join-Path $phpDir 'php.ini'
if ((Test-Path $iniProduction) -and -not (Test-Path $ini)) {
    Copy-Item $iniProduction $ini
}

if (Test-Path $ini) {
    $content = Get-Content $ini -Raw
    # Portable PHP must load DLLs from its own ext folder (not a global C:\php\ext).
    $content = $content -replace ';?\s*extension_dir\s*=\s*"[^"]*"', 'extension_dir = "ext"'
    if ($content -notmatch '(?m)^\s*extension_dir\s*=') {
        $content += "`r`nextension_dir = `"ext`""
    }
    $extensions = @(
        'extension=curl',
        'extension=fileinfo',
        'extension=mbstring',
        'extension=openssl',
        'extension=pdo_sqlite',
        'extension=sqlite3'
    )
    foreach ($ext in $extensions) {
        $content = $content -replace (";$ext"), $ext
        if ($content -notmatch [regex]::Escape($ext)) {
            $content += "`r`n$ext"
        }
    }
    Set-Content -Path $ini -Value $content -Encoding ASCII
}

Write-Host "PHP runtime ready:"
& $phpExe -v
Write-Host "Location: $phpDir"
