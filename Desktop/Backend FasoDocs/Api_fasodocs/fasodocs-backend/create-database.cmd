@echo off
REM ============================================
REM Script de création de la base de données FasoDocs
REM ============================================

echo.
echo ================================================
echo   Creation de la base de donnees fasodocs_db
echo ================================================
echo.

REM Chemins possibles pour MySQL
set MYSQL_PATHS=^
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" ^
"C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe" ^
"C:\Program Files (x86)\MySQL\MySQL Server 8.0\bin\mysql.exe" ^
"C:\xampp\mysql\bin\mysql.exe" ^
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" ^
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe"

set MYSQL_FOUND=0

REM Rechercher MySQL
for %%P in (%MYSQL_PATHS%) do (
    if exist %%P (
        set MYSQL_EXE=%%P
        set MYSQL_FOUND=1
        goto :found
    )
)

:found
if %MYSQL_FOUND%==0 (
    echo [ERREUR] MySQL n'a pas ete trouve automatiquement.
    echo.
    echo Veuillez executer manuellement cette commande dans MySQL:
    echo.
    echo   CREATE DATABASE IF NOT EXISTS fasodocs_db 
    echo   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    echo.
    echo Options:
    echo 1. Utiliser MySQL Workbench
    echo 2. Utiliser MySQL Command Line Client
    echo 3. Ouvrir create-database.sql dans votre client MySQL
    echo.
    pause
    exit /b 1
)

echo MySQL trouve: %MYSQL_EXE%
echo.

REM Créer la base de données
echo Creation de la base de donnees...
echo.

echo CREATE DATABASE IF NOT EXISTS fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; | %MYSQL_EXE% -u root -proot

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [SUCCES] Base de donnees fasodocs_db creee avec succes!
    echo.
    echo Verification...
    echo SHOW DATABASES LIKE 'fasodocs_db'; | %MYSQL_EXE% -u root -proot
    echo.
    echo ================================================
    echo   Configuration terminee!
    echo ================================================
    echo.
    echo Vous pouvez maintenant lancer l'application Spring Boot.
    echo Les tables seront creees automatiquement par Hibernate.
    echo.
) else (
    echo.
    echo [ERREUR] Impossible de creer la base de donnees.
    echo.
    echo Verifiez:
    echo 1. MySQL est demarree
    echo 2. Le mot de passe root est correct (actuellement: root)
    echo 3. Vous avez les privileges necessaires
    echo.
    echo Si le mot de passe n'est pas "root", editez ce fichier et modifiez:
    echo    -proot  par  -pVOTRE_MOT_DE_PASSE
    echo.
)

echo.
pause
