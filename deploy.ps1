# Manual production deploy.
# Builds the frontend on this machine, pulls main on the server,
# then uploads the ready public/build folder.
#
# Push PHP changes to origin/main first, then run:
#   powershell -ExecutionPolicy Bypass -File .\deploy.ps1

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

$SshHost = "safynyrx@premium119.web-hosting.com"
$SshPort = "21098"
$Identity = Join-Path $env:USERPROFILE ".ssh\id_ed25519"
$RemoteApp = "/home/safynyrx/public_html/qsm"
$RemoteArchive = "/home/safynyrx/qsm-public-build.tar.gz"
$Archive = Join-Path $env:TEMP "qsm-public-build.tar.gz"

$SshArgs = @(
    "-o", "BatchMode=yes",
    "-o", "ConnectTimeout=25",
    "-o", "ServerAliveInterval=15",
    "-o", "ServerAliveCountMax=3",
    "-p", $SshPort,
    "-o", "IdentitiesOnly=yes",
    "-i", $Identity,
    $SshHost
)

function Invoke-Remote([string]$Bash) {
    $bytes = [Text.Encoding]::UTF8.GetBytes($Bash)
    $b64 = [Convert]::ToBase64String($bytes)
    & ssh @SshArgs "echo $b64 | base64 -d | bash"
    if ($LASTEXITCODE -ne 0) {
        throw "Remote command failed with exit code $LASTEXITCODE"
    }
}

if (-not (Test-Path $Identity)) {
    throw "SSH key not found: $Identity"
}

Write-Host "Building frontend locally..."
& npm.cmd run build
if ($LASTEXITCODE -ne 0) {
    throw "npm run build failed"
}
if (-not (Test-Path "public\build\manifest.json")) {
    throw "public\build\manifest.json is missing after the build"
}

Write-Host "Packing public/build..."
if (Test-Path $Archive) {
    Remove-Item $Archive -Force
}
& tar -czf $Archive -C public build
if ($LASTEXITCODE -ne 0) {
    throw "Could not pack public/build"
}

$update = @"
set -euo pipefail
cd $RemoteApp
php artisan down || true
git checkout -- public/build
git clean -fd public/build
git pull origin main
COMPOSER_BIN=""
if command -v composer >/dev/null 2>&1; then
  COMPOSER_BIN="`$(command -v composer)"
else
  COMPOSER_BIN="`$(bash -lc 'command -v composer' 2>/dev/null || true)"
fi
if [ -n "`$COMPOSER_BIN" ]; then
  "`$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction
else
  echo "composer is not installed on the server; vendor/ was left unchanged"
fi
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
"@

$publish = @"
set -euo pipefail
cd $RemoteApp
rm -rf /home/safynyrx/qsm-build-staging
mkdir -p /home/safynyrx/qsm-build-staging
tar -xzf $RemoteArchive -C /home/safynyrx/qsm-build-staging
test -f /home/safynyrx/qsm-build-staging/build/manifest.json
rm -rf public/build
mv /home/safynyrx/qsm-build-staging/build public/build
rm -rf /home/safynyrx/qsm-build-staging $RemoteArchive
chmod -R 755 public/build
php artisan up
"@

$failed = $true
try {
    Write-Host "Updating the server from origin/main..."
    Invoke-Remote $update

    Write-Host "Uploading the local build..."
    & scp @(
        "-o", "BatchMode=yes",
        "-o", "ConnectTimeout=25",
        "-P", $SshPort,
        "-o", "IdentitiesOnly=yes",
        "-i", $Identity,
        $Archive,
        "${SshHost}:${RemoteArchive}"
    )
    if ($LASTEXITCODE -ne 0) {
        throw "Upload of public/build failed"
    }

    Write-Host "Publishing the build..."
    Invoke-Remote $publish
    $failed = $false
    Write-Host "Deploy finished: https://qsmcore.org/"
}
finally {
    if (Test-Path $Archive) {
        Remove-Item $Archive -Force -ErrorAction SilentlyContinue
    }
    if ($failed) {
        Write-Host "Deploy failed. Bringing the site back online..."
        try {
            Invoke-Remote "cd $RemoteApp && php artisan up || true"
        }
        catch {
            Write-Host $_.Exception.Message
        }
    }
}
