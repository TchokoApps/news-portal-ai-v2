@echo off
REM Testing and quality commands

:menu
echo.
echo ===== Testing and Quality =====
echo 1. php artisan test
echo 2. php artisan test --filter=FeatureName
echo 3. php artisan test --parallel
echo 0. Exit
echo.

set /p choice="Select command (0-3): "

if "%choice%"=="1" goto test
if "%choice%"=="2" goto filter
if "%choice%"=="3" goto parallel
if "%choice%"=="0" exit /b
goto menu

:test
echo Running: php artisan test
echo This runs all tests...
php artisan test
pause
goto menu

:filter
echo Running: php artisan test --filter=FeatureName
set /p feature="Enter feature name to test: "
if "%feature%"=="" (
    echo No feature name provided.
    pause
    goto menu
)
php artisan test --filter=%feature%
pause
goto menu

:parallel
echo Running: php artisan test --parallel
echo This runs tests in parallel for faster execution...
php artisan test --parallel
pause
goto menu
