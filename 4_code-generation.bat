@echo off
REM Code generation commands

:menu
echo.
echo ===== Code Generation ^(make^) =====
echo 1. php artisan make:model Post -mfs
echo 2. php artisan make:controller PostController --resource
echo 3. php artisan make:request StorePostRequest
echo 4. php artisan make:test PostTest
echo 5. php artisan make:job ProcessOrder
echo 0. Exit
echo.

set /p choice="Select command (0-5): "

if "%choice%"=="1" goto makemodel
if "%choice%"=="2" goto makecontroller
if "%choice%"=="3" goto makerequest
if "%choice%"=="4" goto maketest
if "%choice%"=="5" goto makejob
if "%choice%"=="0" exit /b
goto menu

:makemodel
echo Running: php artisan make:model Post -mfs
set /p modelname="Enter model name (default: Post): "
if "%modelname%"=="" set modelname=Post
php artisan make:model %modelname% -mfs
pause
goto menu

:makecontroller
echo Running: php artisan make:controller PostController --resource
set /p controllername="Enter controller name (default: PostController): "
if "%controllername%"=="" set controllername=PostController
php artisan make:controller %controllername% --resource
pause
goto menu

:makerequest
echo Running: php artisan make:request StorePostRequest
set /p requestname="Enter request name (default: StorePostRequest): "
if "%requestname%"=="" set requestname=StorePostRequest
php artisan make:request %requestname%
pause
goto menu

:maketest
echo Running: php artisan make:test PostTest
set /p testname="Enter test name (default: PostTest): "
if "%testname%"=="" set testname=PostTest
php artisan make:test %testname%
pause
goto menu

:makejob
echo Running: php artisan make:job ProcessOrder
set /p jobname="Enter job name (default: ProcessOrder): "
if "%jobname%"=="" set jobname=ProcessOrder
php artisan make:job %jobname%
pause
goto menu
