@echo off
REM Cache and performance commands

:menu
echo.
echo ===== Cache and Performance =====
echo 1. php artisan optimize
echo 2. php artisan optimize:clear
echo 3. php artisan config:cache
echo 4. php artisan route:cache
echo 5. php artisan view:cache
echo 6. Clear all cache
echo 0. Exit
echo.

set /p choice="Select command (0-6): "

if "%choice%"=="1" goto optimize
if "%choice%"=="2" goto opt_clear
if "%choice%"=="3" goto config_cache
if "%choice%"=="4" goto route_cache
if "%choice%"=="5" goto view_cache
if "%choice%"=="6" goto clear_all
if "%choice%"=="0" exit /b
goto menu

:optimize
echo Running: php artisan optimize
php artisan optimize
pause
goto menu

:opt_clear
echo Running: php artisan optimize:clear
php artisan optimize:clear
pause
goto menu

:config_cache
echo Running: php artisan config:cache
php artisan config:cache
pause
goto menu

:route_cache
echo Running: php artisan route:cache
php artisan route:cache
pause
goto menu

:view_cache
echo Running: php artisan view:cache
php artisan view:cache
pause
goto menu

:clear_all
echo Clearing all cache...
echo.
php artisan optimize:clear
echo.
php artisan view:clear
echo.
php artisan cache:clear
echo.
echo All caches cleared!
pause
goto menu
