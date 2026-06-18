@echo off
REM Database workflow commands

:menu
echo.
echo ===== Database Workflow =====
echo 1. php artisan migrate
echo 2. php artisan migrate:rollback
echo 3. php artisan migrate:fresh --seed
echo 4. php artisan db:seed
echo 5. php artisan db:show
echo 6. php artisan db:table users
echo 0. Exit
echo.

set /p choice="Select command (0-6): "

if "%choice%"=="1" goto migrate
if "%choice%"=="2" goto rollback
if "%choice%"=="3" goto fresh_seed
if "%choice%"=="4" goto seed
if "%choice%"=="5" goto show
if "%choice%"=="6" goto table_users
if "%choice%"=="0" exit /b
goto menu

:migrate
echo Running: php artisan migrate
php artisan migrate
pause
goto menu

:rollback
echo Running: php artisan migrate:rollback
php artisan migrate:rollback
pause
goto menu

:fresh_seed
echo Running: php artisan migrate:fresh --seed
echo This will DROP and recreate all tables with seeded data!
set /p confirm="Are you sure? (y/n): "
if "%confirm%"=="y" (
    php artisan migrate:fresh --seed
) else (
    echo Cancelled.
)
pause
goto menu

:seed
echo Running: php artisan db:seed
php artisan db:seed
pause
goto menu

:show
echo Running: php artisan db:show
php artisan db:show
pause
goto menu

:table_users
echo Running: php artisan db:table users
php artisan db:table users
pause
goto menu
