# 🚀 Guide IntelliJ IDEA - FasoDocs Backend

## Étape 1: Ouvrir le Projet

1. **Lancez IntelliJ IDEA**

2. **Ouvrir le projet:**
   - Cliquez sur **"Open"**
   - Naviguez vers: `C:\Users\Kalandew37\Documents\projet1\fasodocs-backend`
   - Sélectionnez le dossier et cliquez **"OK"**

3. **IntelliJ va détecter Maven:**
   - Un popup apparaîtra: **"Maven projects need to be imported"**
   - Cliquez sur **"Import Changes"** ou **"Enable Auto-Import"**

## Étape 2: Configurer le JDK

### Option A: Télécharger Java 17 via IntelliJ (Recommandé)

1. **Aller dans Project Structure:**
   - Menu: **File → Project Structure** (ou `Ctrl+Alt+Shift+S`)

2. **Dans la section "Project":**
   - Cliquez sur **SDK** dropdown
   - Sélectionnez **"Add SDK" → "Download JDK..."**

3. **Télécharger Java 17:**
   - **Version:** 17
   - **Vendor:** Eclipse Temurin (AdoptOpenJDK)
   - **Location:** Laisser par défaut
   - Cliquez **"Download"**

4. **Configurer le niveau de langage:**
   - **Language Level:** 17 - Sealed types, always-strict floating-point semantics
   - Cliquez **"Apply"**

### Option B: Utiliser un JDK déjà installé

Si vous avez déjà installé Java 17:

1. **File → Project Structure → Project**
2. **SDK:** Cliquez sur le dropdown
3. Sélectionnez **"Add SDK" → "JDK..."**
4. Naviguez vers votre installation Java 17
5. Cliquez **"OK"**

## Étape 3: Activer Lombok

1. **Installer le plugin Lombok:**
   - Menu: **File → Settings** (ou `Ctrl+Alt+S`)
   - Allez dans **Plugins**
   - Recherchez **"Lombok"**
   - Cliquez **"Install"**
   - Redémarrez IntelliJ si demandé

2. **Activer le processing des annotations:**
   - **File → Settings → Build, Execution, Deployment → Compiler → Annotation Processors**
   - ✅ Cochez **"Enable annotation processing"**
   - Cliquez **"Apply"** puis **"OK"**

## Étape 4: Recharger le Projet Maven

1. **Ouvrir la fenêtre Maven:**
   - Menu: **View → Tool Windows → Maven**
   - Ou cliquez sur l'icône Maven à droite

2. **Recharger les dépendances:**
   - Cliquez sur l'icône 🔄 **"Reload All Maven Projects"**
   - Attendez que toutes les dépendances soient téléchargées

## Étape 5: Configurer MySQL

Avant de lancer l'application, assurez-vous que MySQL est configuré:

1. **Démarrer MySQL** (si pas déjà fait)

2. **Créer la base de données:**
   ```sql
   CREATE DATABASE fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Vérifier application.properties:**
   - Ouvrir: `src/main/resources/application.properties`
   - Vérifier les paramètres MySQL:
   ```properties
   spring.datasource.url=jdbc:mysql://localhost:3306/fasodocs_db?useSSL=false&serverTimezone=UTC
   spring.datasource.username=root
   spring.datasource.password=VOTRE_MOT_DE_PASSE
   ```

## Étape 6: Lancer l'Application

### Méthode 1: Via la classe principale (Recommandé)

1. **Ouvrir la classe principale:**
   - Naviguez vers: `src/main/java/ml/fasodocs/backend/FasodocsBackendApplication.java`

2. **Lancer l'application:**
   - Cliquez sur le **▶️ bouton vert** à gauche de `public static void main`
   - Sélectionnez **"Run 'FasodocsBackendApplication'"**

### Méthode 2: Via Maven

1. **Ouvrir le panneau Maven** (à droite)

2. **Naviguer vers:**
   - `fasodocs-backend → Plugins → spring-boot → spring-boot:run`

3. **Double-cliquer** sur `spring-boot:run`

### Méthode 3: Via Terminal IntelliJ

1. **Ouvrir le terminal:**
   - Menu: **View → Tool Windows → Terminal**
   - Ou `Alt+F12`

2. **Lancer la commande:**
   ```bash
   .\mvnw.cmd spring-boot:run
   ```

## Étape 7: Vérifier que l'Application Fonctionne

### Dans la Console IntelliJ

Vous devriez voir:
```
========================================
   FasoDocs Backend démarré avec succès!
   API Documentation: http://localhost:8080/api/swagger-ui.html
========================================

Started FasodocsBackendApplication in X.XXX seconds
```

### Tester l'API

1. **Ouvrir votre navigateur**

2. **Swagger UI:**
   ```
   http://localhost:8080/api/swagger-ui.html
   ```

3. **Test rapide:**
   ```
   http://localhost:8080/api/procedures
   ```

## 🔧 Résolution des Problèmes

### Erreur: "Cannot resolve symbol"

**Solution:**
1. **File → Invalidate Caches → Invalidate and Restart**
2. Attendez que IntelliJ réindexe le projet

### Erreur: "Lombok not working"

**Solution:**
1. Vérifier que le plugin Lombok est installé
2. **File → Settings → Build → Compiler → Annotation Processors**
3. ✅ Activer "Enable annotation processing"
4. Redémarrer IntelliJ

### Erreur: "Port 8080 already in use"

**Solution 1:** Changer le port dans `application.properties`:
```properties
server.port=8081
```

**Solution 2:** Arrêter l'application qui utilise le port 8080

### Erreur: "Access denied for user 'root'@'localhost'"

**Solution:**
1. Vérifier que MySQL est démarré
2. Mettre à jour `application.properties` avec le bon mot de passe:
   ```properties
   spring.datasource.password=VOTRE_MOT_DE_PASSE
   ```

### Erreur: "Table doesn't exist"

**Solution:**
- L'application créera automatiquement les tables au premier démarrage
- Vérifier que `spring.jpa.hibernate.ddl-auto=update` dans `application.properties`

## 🎯 Configuration de Debug

Pour déboguer l'application:

1. **Mettre un breakpoint:**
   - Cliquez dans la marge gauche d'une ligne de code

2. **Lancer en mode debug:**
   - Cliquez sur le **🐛 bouton debug** à côté du bouton run
   - Ou cliquez droit sur `FasodocsBackendApplication` → **"Debug..."**

## 📝 Raccourcis Utiles

| Action | Raccourci |
|--------|-----------|
| Run Application | `Shift+F10` |
| Debug Application | `Shift+F9` |
| Stop Application | `Ctrl+F2` |
| Rerun | `Ctrl+F5` |
| Open Terminal | `Alt+F12` |
| Maven Tool Window | `Ctrl+Shift+A` → "Maven" |
| Project Structure | `Ctrl+Alt+Shift+S` |

## ✅ Checklist de Vérification

Avant de lancer l'application, assurez-vous que:

- [x] Java 17 est configuré dans Project Structure
- [x] Plugin Lombok est installé
- [x] Annotation Processing est activé
- [x] Maven a rechargé toutes les dépendances
- [x] MySQL est démarré
- [x] Base de données `fasodocs_db` existe
- [x] Credentials MySQL sont corrects dans `application.properties`

## 🎉 Succès!

Si tout fonctionne, vous verrez:

```
2025-10-13 XX:XX:XX - 
========================================
   FasoDocs Backend démarré avec succès!
   API Documentation: http://localhost:8080/api/swagger-ui.html
========================================
```

**Vous pouvez maintenant:**
- Tester l'API via Swagger: http://localhost:8080/api/swagger-ui.html
- Utiliser Postman avec la collection fournie
- Développer le frontend mobile

---

**Bon développement avec IntelliJ IDEA! 🚀**
