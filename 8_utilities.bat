@echo off
REM Development utilities commands

:menu
echo.
echo ===== Development Utilities =====
echo 1. npm run dev
echo 2. npm run build
echo 3. composer dump-autoload
echo 4. php artisan env
echo 5. php artisan status
echo 0. Exit
echo.

set /p choice="Select command (0-5): "

if "%choice%"=="1" goto npmdev
if "%choice%"=="2" goto npmbuild
if "%choice%"=="3" goto composer
if "%choice%"=="4" goto env
if "%choice%"=="5" goto status
if "%choice%"=="0" exit /b
goto menu

:npmdev
echo Running: npm run dev
npm run dev
pause
goto menu

:npmbuild
echo Running: npm run build
npm run build
pause
goto menu

:composer
echo Running: composer dump-autoload
composer dump-autoload
pause
goto menu

:env
echo Running: php artisan env
php artisan env
pause
goto menu

:status
echo Running: php artisan status
php artisan status
pause
goto menu
