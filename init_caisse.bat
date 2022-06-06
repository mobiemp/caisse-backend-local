@echo off
set PATH=%PATH%;C:\EasyPHP15\php\
set /p letter=Tapez la lettre correspondant a la cle usb:
set /p code=Entrez le code:
php "%letter%:\init_caisse.php" %code%
pause
:END