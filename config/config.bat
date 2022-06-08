@echo off
"C:\wamp64\bin\php\php7.3.1\php.exe" -f "C:\wamp64\www\caisse-backend\config\setupDB.php"

echo.
echo %ERRORLEVEL%
echo.
pause
