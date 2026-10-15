# Desktop Packaging (Tauri)

This folder packages the Laravel POS app as a Windows desktop application.

## What the installer includes

- Tauri native window (WebView2)
- Bundled portable PHP runtime
- Packaged Laravel application + vendor
- Starter SQLite database (copied into AppData on first run)

End users do **not** need PHP, Composer, Apache, or MySQL installed.

## Prerequisites (developer machine)

1. Node.js 20+
2. PHP 8.3 + Composer (already used for this project)
3. Rust toolchain (`rustup`) with the **MSVC** host (`stable-x86_64-pc-windows-msvc`)
4. **Visual Studio 2022 Build Tools** with the **Desktop development with C++** workload (provides real `link.exe` + Windows SDK)
5. WebView2 (preinstalled on modern Windows 10/11)

Install Rust if needed:

```powershell
winget install --id Rustlang.Rustup -e
# restart terminal, then:
rustup default stable-x86_64-pc-windows-msvc
```

Install MSVC Build Tools (required for the `.exe`):

```powershell
winget install --id Microsoft.VisualStudio.2022.BuildTools -e --override "--wait --passive --add Microsoft.VisualStudio.Workload.VCTools --includeRecommended"
```

If the installer fails with exit code `5008`, download the Build Tools bootstrapper from Microsoft, run it interactively, and select **Desktop development with C++**.

**Important:** Git for Windows ships a Unix `link.exe` that breaks Rust linking (`extra operand`). Ensure the MSVC `link.exe` comes first on `PATH`, or open a **“x64 Native Tools Command Prompt for VS”** before `npm run build`.

## Build the Windows installer

From the `desktop` folder:

```powershell
cd desktop
npm install
npm run package
```

Or step by step:

```powershell
npm run prepare-runtime   # build assets + copy Laravel into src-tauri/resources/app
npm run download-php      # download portable PHP 8.3 into src-tauri/resources/php
npm run build             # tauri build -> NSIS/MSI installers
```

Installers are written to:

- `desktop/src-tauri/target/release/bundle/nsis/`
- `desktop/src-tauri/target/release/bundle/msi/`

## Development

```powershell
cd desktop
npm install
npm run prepare-runtime
npm run download-php
npm run dev
```

## Runtime behavior

1. App starts and invokes `start_laravel_server`
2. Laravel files are ensured under `%AppData%/com.posinventory.desktop/app`
3. SQLite DB lives under `%AppData%/com.posinventory.desktop/database/database.sqlite`
4. Bundled `php.exe` runs `artisan serve` on `127.0.0.1:8765`
5. Window navigates to the local POS UI

## Smoke test without Tauri

After `prepare-runtime` + `download-php`:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\run-portable.ps1
```

This starts bundled PHP + Laravel on `http://127.0.0.1:8765` (no Rust/MSVC required).

## Notes

- First packaged build can take several minutes (Rust compile).
- Keep `src-tauri/resources/php` and `src-tauri/resources/app` out of source control; regenerate with the scripts.
- Portable PHP script targets current **PHP 8.3 NTS x64** from windows.php.net (update the version in `scripts/download-php.ps1` when releases move).
- End-user machines still do **not** need PHP/Composer/Apache/MySQL when the Tauri installer is built with bundled resources.
