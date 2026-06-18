@echo off
REM Queues and async jobs commands

:menu
echo.
echo ===== Queues and Async Jobs =====
echo 1. php artisan queue:work
echo 2. php artisan queue:restart
echo 3. php artisan queue:failed
echo 4. php artisan queue:retry all
echo 0. Exit
echo.

set /p choice="Select command (0-4): "

if "%choice%"=="1" goto work
if "%choice%"=="2" goto restart
if "%choice%"=="3" goto failed
if "%choice%"=="4" goto retry
if "%choice%"=="0" exit /b
goto menu

:work
echo Running: php artisan queue:work
php artisan queue:work
pause
goto menu

:restart
echo Running: php artisan queue:restart
php artisan queue:restart
pause
goto menu

:failed
echo Running: php artisan queue:failed
php artisan queue:failed
pause
goto menu

:retry
echo Running: php artisan queue:retry all
php artisan queue:retry all
pause
goto menu
