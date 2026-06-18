@echo off
REM Scheduler and cron commands

:menu
echo.
echo ===== Scheduler and Cron =====
echo 1. php artisan schedule:list
echo 2. php artisan schedule:run
echo 3. php artisan schedule:work
echo 0. Exit
echo.

set /p choice="Select command (0-3): "

if "%choice%"=="1" goto list
if "%choice%"=="2" goto run
if "%choice%"=="3" goto work
if "%choice%"=="0" exit /b
goto menu

:list
echo Running: php artisan schedule:list
php artisan schedule:list
pause
goto menu

:run
echo Running: php artisan schedule:run
echo This executes one pass of the scheduler
php artisan schedule:run
pause
goto menu

:work
echo Running: php artisan schedule:work
echo This runs the scheduler continuously (press Ctrl+C to stop^)
php artisan schedule:work
pause
goto menu
