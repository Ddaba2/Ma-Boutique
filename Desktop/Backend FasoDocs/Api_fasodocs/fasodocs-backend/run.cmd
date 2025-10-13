@echo off
REM FasoDocs Backend - Startup Script
REM This script sets JAVA_HOME and runs the Spring Boot application

echo ========================================
echo    FasoDocs Backend - Starting...
echo ========================================
echo.

REM Set JAVA_HOME to your JDK installation
REM After installing Java 17, update this path
set JAVA_HOME=C:\Program Files\Eclipse Adoptium\jdk-17.0.12.7-hotspot

REM Alternative paths if your installation is different:
REM set JAVA_HOME=C:\Program Files\Java\jdk-17
REM set JAVA_HOME=C:\Program Files\AdoptOpenJDK\jdk-17.0.12.7-hotspot

REM Verify Java is available
echo Checking Java installation...
"%JAVA_HOME%\bin\java.exe" -version
if errorlevel 1 (
    echo.
    echo ERROR: Java not found at %JAVA_HOME%
    echo Please install Java 17 or 21 LTS and update JAVA_HOME in this script
    echo Download from: https://adoptium.net/temurin/releases/
    pause
    exit /b 1
)

echo.
echo Java found! Starting application...
echo.

REM Run the application using Maven Wrapper
mvnw.cmd spring-boot:run

pause
