@echo off
REM Main menu - Quick access to all command groups

:menu
echo.
echo.
echo     ╔════════════════════════════════════════════════════════════╗
echo     ║     Laravel Artisan Command Suite - Learning Project      ║
echo     ╚════════════════════════════════════════════════════════════╝
echo.
echo Available Batch Scripts:
echo.
echo 1. 1_app-run-debug.bat         - App run and debug commands
echo 2. 2_cache-performance.bat     - Cache and performance optimization
echo 3. 3_database-workflow.bat     - Database migrations and seeding
echo 4. 4_code-generation.bat       - Generate models, controllers, tests
echo 5. 5_queues-jobs.bat           - Queue and async job management
echo 6. 6_scheduler-cron.bat        - Scheduler and cron configuration
echo 7. 7_testing-quality.bat       - Testing and quality assurance
echo 8. 8_utilities.bat             - Development utilities
echo.
echo 0. Exit
echo.
set /p choice="Select a batch file (0-8): "

if "%choice%"=="1" call 1_app-run-debug.bat
if "%choice%"=="2" call 2_cache-performance.bat
if "%choice%"=="3" call 3_database-workflow.bat
if "%choice%"=="4" call 4_code-generation.bat
if "%choice%"=="5" call 5_queues-jobs.bat
if "%choice%"=="6" call 6_scheduler-cron.bat
if "%choice%"=="7" call 7_testing-quality.bat
if "%choice%"=="8" call 8_utilities.bat
if "%choice%"=="0" exit /b

goto menu
