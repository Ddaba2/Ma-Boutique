# Guide de Démarrage Rapide - FasoDocs Backend

## 🚀 Démarrage en 5 minutes

### Étape 1 : Prérequis
Assurez-vous d'avoir installé :
- ✅ Java 17+
- ✅ Maven 3.8+
- ✅ MySQL 8.0+

### Étape 2 : Configuration de la Base de Données

1. **Démarrez MySQL** et créez la base de données :
```sql
CREATE DATABASE fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Modifiez** `src/main/resources/application.properties` :
```properties
spring.datasource.username=root
spring.datasource.password=VOTRE_MOT_DE_PASSE
```

### Étape 3 : Lancement de l'Application

```bash
cd fasodocs-backend
mvn clean install
mvn spring-boot:run
```

✅ **L'application est maintenant accessible sur** : http://localhost:8080/api

### Étape 4 : Tester l'API

#### Option A : Swagger UI (Recommandé)
Ouvrez votre navigateur : **http://localhost:8080/api/swagger-ui.html**

#### Option B : Postman
Importez le fichier `FasoDocs_API.postman_collection.json`

### Étape 5 : Insertion des Données d'Exemple

Exécutez le script SQL :
```bash
mysql -u root -p fasodocs_db < src/main/resources/database-init.sql
```

## 📝 Premier Test

### 1. Créer un compte

**Requête** :
```bash
POST http://localhost:8080/api/auth/inscription
Content-Type: application/json

{
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou@example.com",
  "telephone": "76123456",
  "motDePasse": "Password123",
  "languePreferee": "fr"
}
```

**Réponse attendue** :
```json
{
  "message": "Inscription réussie! Veuillez vérifier votre email.",
  "success": true
}
```

### 2. Se connecter

**Requête** :
```bash
POST http://localhost:8080/api/auth/connexion
Content-Type: application/json

{
  "identifiant": "amadou@example.com",
  "motDePasse": "Password123"
}
```

**Réponse attendue** :
```json
{
  "token": "eyJhbGciOiJIUzUxMiJ9...",
  "type": "Bearer",
  "id": 1,
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou@example.com",
  "telephone": "76123456",
  "languePreferee": "fr"
}
```

### 3. Consulter les procédures

**Requête** :
```bash
GET http://localhost:8080/api/procedures
```

**Réponse attendue** :
```json
[
  {
    "id": 1,
    "nom": "CARTE_NINA",
    "titre": "Carte d'Identité Nationale NINA",
    "cout": 5000,
    "delai": "15 jours",
    "description": "Demande de carte d'identité nationale biométrique NINA",
    ...
  }
]
```

## 🔐 Authentification pour les Routes Protégées

Pour les routes qui nécessitent une authentification, ajoutez le header :

```
Authorization: Bearer VOTRE_TOKEN_JWT
```

**Exemple avec curl** :
```bash
curl -H "Authorization: Bearer eyJhbGciOiJIUzUxMiJ9..." \
     http://localhost:8080/api/auth/profil
```

## 🛠️ Configuration Firebase (Notifications Push)

### Configuration optionnelle (peut être ignorée pour le développement local)

1. Créez un projet sur [Firebase Console](https://console.firebase.google.com/)
2. Téléchargez `firebase-service-account.json`
3. Placez-le dans `src/main/resources/`

**Si vous ne configurez pas Firebase** :
- Les notifications seront créées en base de données
- Les notifications push ne seront pas envoyées (pas d'erreur)

## 📧 Configuration Email (Vérification Email)

### Pour Gmail :

1. **Activez l'authentification à deux facteurs** sur votre compte Gmail
2. **Générez un mot de passe d'application** : [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
3. **Modifiez** `application.properties` :

```properties
spring.mail.username=votre-email@gmail.com
spring.mail.password=xxxx-xxxx-xxxx-xxxx
```

## 🗄️ Structure de la Base de Données

### Tables Principales :
- **citoyens** : Utilisateurs de l'application
- **procedures** : Procédures administratives
- **categories** : Catégories de procédures
- **documents_requis** : Documents nécessaires
- **lieux_traitement** : Lieux administratifs
- **notifications** : Notifications des citoyens
- **traductions** : Traductions en Bambara
- **roles** : Rôles (CITOYEN, ADMIN)

## 🔍 Endpoints Principaux

### Authentification
- `POST /api/auth/inscription` - Inscription
- `POST /api/auth/connexion` - Connexion
- `GET /api/auth/profil` - Profil (authentifié)
- `PUT /api/auth/profil` - Modifier profil (authentifié)

### Procédures
- `GET /api/procedures` - Liste des procédures
- `GET /api/procedures/{id}` - Détails d'une procédure
- `GET /api/procedures/rechercher?q=terme` - Recherche

### Notifications
- `GET /api/notifications` - Mes notifications (authentifié)
- `GET /api/notifications/non-lues` - Non lues (authentifié)

## 🐛 Résolution des Problèmes Courants

### Erreur : "Access denied for user"
➡️ Vérifiez le mot de passe MySQL dans `application.properties`

### Erreur : "Table doesn't exist"
➡️ Vérifiez que `spring.jpa.hibernate.ddl-auto=update` dans `application.properties`
➡️ Redémarrez l'application pour créer les tables

### Erreur : "Port 8080 already in use"
➡️ Changez le port dans `application.properties` :
```properties
server.port=8081
```

### Email de vérification non reçu
➡️ Vérifiez les logs de l'application
➡️ Vérifiez la configuration email dans `application.properties`
➡️ Pour le développement, vous pouvez vérifier manuellement en base :
```sql
UPDATE citoyens SET email_verifie = TRUE WHERE id = 1;
```

## 📊 Créer un Compte Administrateur

1. Créez un compte normal via `/api/auth/inscription`
2. Modifiez le rôle en base de données :

```sql
-- Trouver l'ID du citoyen
SELECT id, nom, prenom, email FROM citoyens;

-- Trouver l'ID du rôle ADMIN
SELECT id, nom FROM roles WHERE nom = 'ROLE_ADMIN';

-- Attribuer le rôle ADMIN (supposons citoyen_id = 1, role_admin_id = 2)
INSERT INTO citoyen_roles (citoyen_id, role_id) VALUES (1, 2);
```

## 🎯 Prochaines Étapes

1. ✅ Testez tous les endpoints avec Swagger
2. ✅ Insérez plus de procédures selon les besoins du Mali
3. ✅ Configurez Firebase pour les notifications push
4. ✅ Développez le frontend mobile (React Native / Flutter)
5. ✅ Déployez l'application en production

## 📞 Support

Pour toute question :
- **Email** : contact@fasodocs.ml
- **Documentation** : http://localhost:8080/api/swagger-ui.html

---

**Bon développement ! 🇲🇱**
