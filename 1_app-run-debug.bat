@echo off
REM App run and debug commands
REM Choose which command to run

:menu
echo.
echo ===== App Run and Debug =====
echo 1. php artisan serve
echo 2. php artisan tinker
echo 3. php artisan route:list
echo 4. php artisan about
echo 5. php artisan pail
echo 6. Run all
echo 0. Exit
echo.

set /p choice="Select command (0-6): "

if "%choice%"=="1" goto serve
if "%choice%"=="2" goto tinker
if "%choice%"=="3" goto route
if "%choice%"=="4" goto about
if "%choice%"=="5" goto pail
if "%choice%"=="6" goto all
if "%choice%"=="0" exit /b
goto menu

:serve
echo Running: php artisan serve
php artisan serve
pause
goto menu

:tinker
echo Running: php artisan tinker
php artisan tinker
pause
goto menu

:route
echo Running: php artisan route:list
php artisan route:list
pause
goto menu

:about
echo Running: php artisan about
php artisan about
pause
goto menu

:pail
echo Running: php artisan pail
php artisan pail
pause
goto menu

:all
echo Running all commands...
echo.
echo === php artisan route:list ===
php artisan route:list
echo.
echo === php artisan about ===
php artisan about
pause
goto menu
