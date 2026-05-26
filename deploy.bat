@echo off
REM Deploy Script untuk Railway - BUMDes Putra Samudra Patimban (Windows)
REM Usage: deploy.bat [environment]
REM Example: deploy.bat production

setlocal enabledelayedexpansion

set ENVIRONMENT=%1
if "%ENVIRONMENT%"=="" set ENVIRONMENT=production

echo.
echo ========================================
echo   Railway Deployment Script
echo   Environment: %ENVIRONMENT%
echo ========================================
echo.

REM Check if railway CLI is installed
where railway >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Railway CLI not found!
    echo Install it with: npm i -g @railway/cli
    pause
    exit /b 1
)

echo [OK] Railway CLI found

REM Check if logged in
railway whoami >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] Not logged in to Railway
    echo Logging in...
    railway login
)

echo [OK] Logged in to Railway
echo.

REM Confirm deployment
echo [WARNING] You are about to deploy to %ENVIRONMENT%
echo This will:
echo   1. Push code to Railway
echo   2. Build the application
echo   3. Run migrations
echo   4. Cache configurations
echo.
set /p CONFIRM="Continue? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo Deployment cancelled.
    pause
    exit /b 0
)

echo.
echo ========================================
echo   Pre-deployment Checks
echo ========================================
echo.

REM Check if .env exists
if not exist .env (
    echo [ERROR] .env file not found!
    pause
    exit /b 1
)

REM Check if APP_DEBUG is false
findstr /C:"APP_DEBUG=true" .env >nul
if %ERRORLEVEL% EQU 0 (
    echo [ERROR] APP_DEBUG is true! Must be false for production.
    pause
    exit /b 1
)

REM Check if APP_ENV is production
findstr /C:"APP_ENV=production" .env >nul
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] APP_ENV is not production
)

echo [OK] Pre-deployment checks passed
echo.

REM Git checks
echo ========================================
echo   Git Status
echo ========================================
echo.

git status -s >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    for /f %%i in ('git status -s ^| find /c /v ""') do set CHANGES=%%i
    if !CHANGES! GTR 0 (
        echo [WARNING] You have uncommitted changes:
        git status -s
        echo.
        set /p COMMIT="Commit and push changes? (y/n): "
        if /i "!COMMIT!"=="y" (
            set /p MESSAGE="Enter commit message: "
            git add .
            git commit -m "!MESSAGE!"
            git push origin main
            echo [OK] Changes committed and pushed
        ) else (
            echo [WARNING] Deploying with uncommitted changes
        )
    ) else (
        echo [OK] No uncommitted changes
        echo Pushing to remote...
        git push origin main
    )
)

echo.
echo ========================================
echo   Deploying to Railway
echo ========================================
echo.

railway up

if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Deployment failed!
    pause
    exit /b 1
)

echo [OK] Code deployed
echo.

REM Wait for deployment
echo Waiting for deployment to complete...
timeout /t 10 /nobreak >nul

echo.
echo ========================================
echo   Running Migrations
echo ========================================
echo.

railway run php artisan migrate --force

if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Migration failed!
    pause
    exit /b 1
)

echo [OK] Migrations completed
echo.

echo ========================================
echo   Caching Configurations
echo ========================================
echo.

railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache

echo [OK] Configurations cached
echo.

echo ========================================
echo   Generating Storage Link
echo ========================================
echo.

railway run php artisan storage:link

echo [OK] Storage link generated
echo.

REM Clear old caches (optional)
set /p CLEAR="Clear old caches? (y/n): "
if /i "%CLEAR%"=="y" (
    railway run php artisan cache:clear
    railway run php artisan view:clear
    echo [OK] Caches cleared
)

echo.
echo ========================================
echo   Deployment Summary
echo ========================================
echo.

REM Get deployment URL
for /f "delims=" %%i in ('railway domain') do set DEPLOY_URL=%%i

echo [SUCCESS] Deployment completed successfully!
echo.
echo Application URL: %DEPLOY_URL%
echo.
echo Next steps:
echo   1. Test the application: %DEPLOY_URL%
echo   2. Check logs: railway logs
echo   3. Monitor metrics: railway status
echo.
echo Troubleshooting:
echo   - View logs: railway logs --follow
echo   - Restart app: railway restart
echo   - Rollback: git revert HEAD ^&^& deploy.bat
echo.

REM Open in browser
set /p OPEN="Open application in browser? (y/n): "
if /i "%OPEN%"=="y" (
    start "" "%DEPLOY_URL%"
)

echo.
echo Happy deploying!
echo.
pause
