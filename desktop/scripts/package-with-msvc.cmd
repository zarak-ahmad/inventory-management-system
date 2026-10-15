@echo off
setlocal
call "F:\vs-buildtools\2022\VC\Auxiliary\Build\vcvars64.bat"
if errorlevel 1 (
  echo Failed to load vcvars64.bat
  exit /b 1
)
set "PATH=C:\Users\zarak\.cargo\bin;%PATH%"
echo === MSVC tools ===
where link
where cl
echo === Rust ===
rustc -vV
cd /d "F:\coding\pos inventory management system\desktop"
if errorlevel 1 exit /b 1
echo === npm run package ===
call npm run package
exit /b %ERRORLEVEL%
