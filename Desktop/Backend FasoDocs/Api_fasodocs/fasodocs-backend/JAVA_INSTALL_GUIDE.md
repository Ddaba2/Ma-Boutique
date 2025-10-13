# ⚠️ IMPORTANT - Java Version Issue

## Current Problem
Your system has **Java 24** installed, which is **NOT compatible** with Spring Boot 3.2.0 and Maven plugins.

The error you're seeing:
```
java.lang.ExceptionInInitializerError: com.sun.tools.javac.code.TypeTag :: UNKNOWN
```

This happens because Java 24 is too new and not yet supported.

---

## ✅ SOLUTION - Install Java 17 LTS

### Step 1: Download Java 17

**Recommended: Eclipse Adoptium (Temurin) - Java 17 LTS**

🔗 **Download Link:** https://adoptium.net/temurin/releases/?version=17

- Choose: **Windows x64** 
- Download the **.msi installer** (easiest)
- Run the installer with default options

### Step 2: Verify Installation

After installing, open a **NEW PowerShell** window and run:

```powershell
java -version
```

You should see:
```
openjdk version "17.0.XX"
```

### Step 3: Update the Run Script

The default installation path is usually:
```
C:\Program Files\Eclipse Adoptium\jdk-17.0.12.7-hotspot
```

If your path is different, edit `run.cmd` and update this line:
```batch
set JAVA_HOME=C:\Program Files\Eclipse Adoptium\jdk-17.0.12.7-hotspot
```

### Step 4: Run the Application

```cmd
run.cmd
```

OR manually:

```powershell
.\mvnw.cmd spring-boot:run
```

---

## Alternative: Use IntelliJ IDEA

If you have IntelliJ IDEA installed:

1. **Open the project** in IntelliJ IDEA
2. Go to **File → Project Structure → Project**
3. Click **SDK dropdown → Add SDK → Download JDK**
4. Select **Version: 17**, Vendor: **Eclipse Temurin**
5. Click **Download**
6. Click **Apply** and **OK**
7. Run the application: Click the green ▶️ button next to `FasodocsBackendApplication`

---

## Why Java 17?

- ✅ **LTS (Long Term Support)** - Stable and supported until 2029
- ✅ **Fully compatible** with Spring Boot 3.x
- ✅ **Production-ready** - Used by most enterprises
- ✅ **Well-tested** with all Maven plugins and tools

Java 24 is still in early access and not recommended for production projects.

---

## After Installing Java 17

Once you have Java 17 installed and configured:

1. Run `run.cmd` 
2. Wait for the application to start
3. Open your browser: **http://localhost:8080/api/swagger-ui.html**
4. Test the API!

---

## Need Help?

If you encounter any issues after installing Java 17:

1. Make sure you opened a **NEW terminal window** after installation
2. Verify `java -version` shows Java 17
3. Check that the path in `run.cmd` matches your installation directory
4. Clear Maven cache: Delete the folder `C:\Users\Kalandew37\.m2\repository`

---

**Remember:** Do NOT use Java 24 for Spring Boot projects. Always use LTS versions (17 or 21).
