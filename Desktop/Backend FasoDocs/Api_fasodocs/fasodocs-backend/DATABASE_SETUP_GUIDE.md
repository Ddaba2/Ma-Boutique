# Guide de Configuration de la Base de Données MySQL

## Problème Actuel

L'application Spring Boot ne peut pas démarrer car la base de données `fasodocs_db` n'existe pas encore dans MySQL.

**Erreur rencontrée:**
```
java.sql.SQLSyntaxErrorException: Unknown database 'fasodocs_db'
```

## Solution: Créer la Base de Données

### Option 1: Utiliser MySQL Command Line (Recommandé)

#### Étape 1: Démarrer MySQL
1. Ouvrez **MySQL Command Line Client** ou **PowerShell**
2. Connectez-vous à MySQL:
```bash
mysql -u root -p
```
3. Entrez votre mot de passe MySQL (si vous en avez un)

#### Étape 2: Créer la Base de Données
Exécutez cette commande SQL:
```sql
CREATE DATABASE IF NOT EXISTS fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Étape 3: Vérifier la Création
```sql
SHOW DATABASES;
USE fasodocs_db;
```

#### Étape 4: Quitter MySQL
```sql
EXIT;
```

### Option 2: Utiliser le Script SQL Fourni

Exécutez le fichier `create-database.sql` depuis la ligne de commande:

```bash
mysql -u root -p < create-database.sql
```

Ou depuis MySQL Workbench:
1. Ouvrez **MySQL Workbench**
2. Connectez-vous à votre serveur MySQL
3. Allez dans **File > Open SQL Script**
4. Sélectionnez `create-database.sql`
5. Cliquez sur l'icône éclair (⚡) pour exécuter

### Option 3: Utiliser MySQL Workbench GUI

1. Ouvrez **MySQL Workbench**
2. Connectez-vous à votre serveur MySQL local
3. Dans la barre latérale gauche, faites un clic droit sur l'espace vide sous "Schemas"
4. Sélectionnez **"Create Schema..."**
5. Nom de la base: `fasodocs_db`
6. Charset: `utf8mb4`
7. Collation: `utf8mb4_unicode_ci`
8. Cliquez sur **Apply** puis **Apply** à nouveau

## Configuration de l'Application

### Vérifier les Paramètres de Connexion

Ouvrez le fichier `src/main/resources/application.properties` et vérifiez:

```properties
# Configuration Base de Données MySQL
spring.datasource.url=jdbc:mysql://localhost:3306/fasodocs_db
spring.datasource.username=root
spring.datasource.password=

# IMPORTANT: Si vous avez un mot de passe MySQL, ajoutez-le ici
# spring.datasource.password=votre_mot_de_passe
```

### Ajuster le Mot de Passe MySQL (Si Nécessaire)

Si votre installation MySQL a un mot de passe pour l'utilisateur `root`, vous DEVEZ le spécifier dans `application.properties`:

```properties
spring.datasource.password=VOTRE_MOT_DE_PASSE_ICI
```

## Que Fait Hibernate Automatiquement?

Une fois la base de données créée, **Hibernate créera automatiquement toutes les tables** au démarrage de l'application grâce à cette configuration:

```properties
spring.jpa.hibernate.ddl-auto=update
```

Cela signifie:
- ✅ Les tables seront créées automatiquement
- ✅ Les colonnes seront ajoutées si nécessaire
- ✅ Les relations seront établies
- ⚠️ Les données existantes ne seront PAS supprimées

### Tables qui Seront Créées Automatiquement:

1. `citoyens` - Utilisateurs de l'application
2. `roles` - Rôles des utilisateurs
3. `procedures` - Procédures administratives
4. `categories` - Catégories de procédures
5. `documents_requis` - Documents nécessaires
6. `lieux_traitement` - Bureaux administratifs
7. `traductions` - Traductions en Bambara
8. `notifications` - Notifications push
9. `historique_logs` - Journaux d'activité
10. `lois_articles` - Articles de loi
11. `parametres` - Paramètres système
12. `chatbot` - Messages du chatbot

## Vérification Après Configuration

### 1. Vérifier que MySQL est en Cours d'Exécution

**Windows - Services:**
1. Appuyez sur `Win + R`
2. Tapez `services.msc`
3. Cherchez "MySQL" ou "MySQL80"
4. Vérifiez que le service est **Démarré**
5. Si non, faites un clic droit > **Démarrer**

**Ou via PowerShell:**
```powershell
Get-Service -Name "*mysql*"
```

### 2. Tester la Connexion MySQL

```bash
mysql -u root -p -e "SHOW DATABASES;"
```

Vous devriez voir `fasodocs_db` dans la liste.

### 3. Relancer l'Application Spring Boot

Une fois la base de données créée, relancez l'application:

```bash
.\mvnw.cmd spring-boot:run
```

**Ou depuis IntelliJ IDEA:**
- Cliquez sur le bouton ▶️ (Run) dans la classe `FasodocsBackendApplication`

## Messages de Succès Attendus

Si tout est configuré correctement, vous verrez:

```
✅ Tomcat initialized with port 8080 (http)
✅ HikariPool-1 - Starting...
✅ HikariPool-1 - Start completed.
✅ HHH000400: Using dialect: org.hibernate.dialect.MySQLDialect
✅ Hibernate: create table citoyens ...
✅ Hibernate: create table procedures ...
✅ Started FasodocsBackendApplication in X.XXX seconds
```

L'application sera accessible sur: **http://localhost:8080**

## Dépannage

### Erreur: "Access denied for user 'root'@'localhost'"

**Cause:** Mot de passe MySQL incorrect

**Solution:**
1. Vérifiez votre mot de passe MySQL
2. Mettez-le à jour dans `application.properties`

### Erreur: "Communications link failure"

**Cause:** MySQL n'est pas démarré

**Solution:**
1. Démarrez le service MySQL (voir section Vérification)
2. Vérifiez que MySQL écoute sur le port 3306

### Erreur: "Unknown database 'fasodocs_db'" (Encore)

**Cause:** La base de données n'a pas été créée

**Solution:**
1. Reconnectez-vous à MySQL
2. Exécutez à nouveau la commande CREATE DATABASE
3. Vérifiez avec `SHOW DATABASES;`

## Initialisation des Données de Test (Optionnel)

Pour ajouter des données de test, un script `init-data.sql` est disponible dans le projet.

Vous pouvez l'exécuter après que l'application ait créé les tables:

```bash
mysql -u root -p fasodocs_db < src/main/resources/init-data.sql
```

## Résumé des Étapes

1. ✅ **Vérifier que MySQL est démarré**
2. ✅ **Créer la base de données `fasodocs_db`**
3. ✅ **Vérifier les identifiants dans `application.properties`**
4. ✅ **Relancer l'application Spring Boot**
5. ✅ **Vérifier que l'application démarre sans erreur**
6. ✅ **Accéder à http://localhost:8080**

## Support

Si vous rencontrez d'autres problèmes:
1. Vérifiez les logs de l'application
2. Vérifiez les logs MySQL
3. Assurez-vous que le port 3306 n'est pas bloqué par le pare-feu

---

**Note:** Hibernate créera automatiquement toutes les tables nécessaires. Vous n'avez pas besoin d'exécuter de script SQL supplémentaire pour créer les tables.
