<#
run.ps1 - Portable one-click runner for Tickety-Twig

What it does:
- Ensures PHP is available (uses system php if on PATH, otherwise downloads a portable PHP into $env:USERPROFILE\php-portable)
- Downloads Composer PHAR (if missing) using PowerShell
- Runs `php composer.phar install` to create `vendor/`
- Verifies `vendor/autoload.php` exists
- Starts the PHP built-in server: php -S localhost:8000 -t public

Notes:
- This script tries to be safe and idempotent. It does not modify system PATH permanently.
- If the PHP binary lacks the openssl extension Composer will fail to download packages. The script will detect and print guidance.
#>

Param(
  [int]$Port = 8000
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Write-Log($msg) { Write-Host "[run.ps1] $msg" }

# Determine repository root (script folder)
$RepoRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
Set-Location $RepoRoot

Write-Log "Repo root: $RepoRoot"

function Get-PHPCommand {
    # Prefer system php if available
    try {
        $ver = & php -v 2>$null
        if ($LASTEXITCODE -eq 0) { return 'php' }
    } catch { }

    # Otherwise use portable PHP under user profile
    $phpPortable = Join-Path $env:USERPROFILE 'php-portable'
    $phpExe = Get-ChildItem -Path $phpPortable -Filter php.exe -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($phpExe) { return $phpExe.FullName }

    return $null
}

function Install-PortablePHP {
    $phpPortable = Join-Path $env:USERPROFILE 'php-portable'
    if (Test-Path $phpPortable) {
        Write-Log "Cleaning existing portable PHP folder: $phpPortable"
        Remove-Item -Recurse -Force $phpPortable
    }

    New-Item -ItemType Directory -Path $phpPortable | Out-Null

    # NOTE: If this URL becomes outdated, replace with a supported PHP x64 zip from windows.php.net
    $phpZipUrl = 'https://windows.php.net/downloads/releases/archives/php-8.1.28-Win32-vs16-x64.zip'
    $zipPath = Join-Path $phpPortable 'php.zip'

    Write-Log "Downloading portable PHP (this may take a minute)..."
    Invoke-WebRequest -Uri $phpZipUrl -OutFile $zipPath -UseBasicParsing

    Write-Log "Extracting PHP..."
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::ExtractToDirectory($zipPath, $phpPortable)

    Remove-Item $zipPath -Force
    Write-Log "Portable PHP installed to $phpPortable"
}

# Ensure we have a php command available
$phpCmd = Get-PHPCommand
if (-not $phpCmd) {
    Write-Log "No PHP found on PATH. Installing portable PHP into user profile..."
    Install-PortablePHP
    $phpCmd = Get-PHPCommand
    if (-not $phpCmd) {
        Write-Error "Failed to locate php.exe after portable installation. Aborting."
        exit 1
    }
}

Write-Log "Using PHP: $phpCmd"

# Check openssl availability
try {
    $opensslLoaded = (& $phpCmd -r "echo extension_loaded('openssl')?1:0;" 2>$null).Trim()
} catch {
    $opensslLoaded = '0'
}
if ($opensslLoaded -ne '1') {
    Write-Host "" -ForegroundColor Yellow
    Write-Host "WARNING: the PHP binary does not have the 'openssl' extension enabled." -ForegroundColor Yellow
    Write-Host "Composer requires openssl for HTTPS downloads. Without it, 'composer install' will fail." -ForegroundColor Yellow
    Write-Host "Options:" -ForegroundColor Yellow
    Write-Host "  - Install a system PHP with openssl and re-run this script," -ForegroundColor Yellow
    Write-Host "  - Or enable openssl in the PHP used by this script (edit php.ini)," -ForegroundColor Yellow
    Write-Host "  - Or run Composer via Docker: docker run --rm -v \"${PWD}:/app\" -w /app composer install" -ForegroundColor Yellow
    Write-Host "" -ForegroundColor Yellow
    # Continue but composer will likely fail; user can cancel with Ctrl+C
}

# Download composer PHAR if missing
$composerPhar = Join-Path $RepoRoot 'composer.phar'
if (-not (Test-Path $composerPhar)) {
    Write-Log "Downloading composer.phar..."
    # Use direct stable phar download via PowerShell
    Invoke-WebRequest -Uri 'https://getcomposer.org/composer-stable.phar' -OutFile $composerPhar -UseBasicParsing
    Write-Log "composer.phar downloaded"
} else {
    Write-Log "composer.phar already exists, skipping download"
}

# Run composer install
Write-Log "Running: php composer.phar install"
Push-Location $RepoRoot
try {
    & $phpCmd $composerPhar install
} catch {
    Write-Error "Composer install failed. See output above. Exiting."
    Pop-Location
    exit 1
}
Pop-Location

# Verify vendor/autoload.php
if (-not (Test-Path (Join-Path $RepoRoot 'vendor\autoload.php'))) {
    Write-Error "vendor/autoload.php not found after composer install. Composer likely failed."
    exit 1
}

Write-Log "Dependencies installed. Starting dev server on http://localhost:$Port"

# Start PHP built-in server (this will keep running and block the script)
Write-Log "Press Ctrl+C to stop the server"
& $phpCmd -S "localhost:$Port" -t (Join-Path $RepoRoot 'public')
