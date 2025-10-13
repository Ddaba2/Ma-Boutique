# FasoDocs Backend

## Description

**FasoDocs** est une application mobile intuitive qui centralise et simplifie l'accès aux informations et services administratifs au Mali. Ce repository contient le backend de l'application développé avec Spring Boot.

## Auteurs

- **Tenen Madyeh SYLLA**
- **Daba DIALLO**

## Version

1.0.0 - Date de création: 01 Octobre 2025

## Technologies Utilisées

- **Java 17**
- **Spring Boot 3.1.5**
- **Spring Security** (JWT Authentication)
- **Spring Data JPA**
- **MySQL** (Base de données)
- **Firebase Cloud Messaging** (Notifications Push)
- **Maven** (Gestion des dépendances)
- **Swagger/OpenAPI** (Documentation API)

## Fonctionnalités Principales

### Pour les Citoyens

- ✅ Création de compte utilisateur (inscription via email/téléphone)
- ✅ Connexion sécurisée avec JWT
- ✅ Consultation des procédures administratives
- ✅ Recherche de procédures
- ✅ Consultation des documents requis et lieux de traitement
- ✅ Géolocalisation des lieux administratifs
- ✅ Notifications push en temps réel
- ✅ Traduction en Bambara (avec synthèse vocale)
- ✅ Chatbot d'assistance
- ✅ Gestion du profil utilisateur
- ✅ Historique des consultations

### Pour les Administrateurs

- ✅ Gestion des procédures (CRUD)
- ✅ Gestion des catégories
- ✅ Gestion des traductions
- ✅ Envoi de notifications

## Prérequis

- **Java 17 LTS** ou **Java 21 LTS** (Java 24 not yet fully supported)
- **Maven 3.8+**
- **MySQL 8.0+**
- **Compte Firebase** (pour les notifications push)

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/votre-repo/fasodocs-backend.git
cd fasodocs-backend
```

### 2. Configurer la base de données MySQL

Créez une base de données MySQL :

```sql
CREATE DATABASE fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configurer le fichier application.properties

Modifiez le fichier `src/main/resources/application.properties` :

```properties
# Base de données
spring.datasource.url=jdbc:mysql://localhost:3306/fasodocs_db?useSSL=false&serverTimezone=UTC
spring.datasource.username=VOTRE_USERNAME
spring.datasource.password=VOTRE_PASSWORD

# Email (Gmail)
spring.mail.username=votre-email@gmail.com
spring.mail.password=votre-mot-de-passe-application
```

### 4. Configurer Firebase (Notifications Push)

1. Créez un projet sur [Firebase Console](https://console.firebase.google.com/)
2. Téléchargez le fichier `firebase-service-account.json`
3. Placez-le dans `src/main/resources/`

### 5. Compiler et exécuter l'application

```bash
mvn clean install
mvn spring-boot:run
```

L'application sera accessible sur : **http://localhost:8080/api**

## Documentation API

Une fois l'application démarrée, accédez à la documentation Swagger :

**http://localhost:8080/api/swagger-ui.html**

## Endpoints Principaux

### Authentification

- `POST /api/auth/inscription` - Inscription d'un nouveau citoyen
- `POST /api/auth/connexion` - Connexion
- `GET /api/auth/verify?code={code}` - Vérification email
- `GET /api/auth/profil` - Récupération du profil
- `PUT /api/auth/profil` - Mise à jour du profil
- `POST /api/auth/deconnexion` - Déconnexion

### Procédures

- `GET /api/procedures` - Liste toutes les procédures
- `GET /api/procedures/{id}` - Détails d'une procédure
- `GET /api/procedures/categorie/{categorieId}` - Procédures par catégorie
- `GET /api/procedures/rechercher?q={terme}` - Recherche
- `POST /api/procedures` - Créer une procédure (Admin)
- `PUT /api/procedures/{id}` - Modifier une procédure (Admin)
- `DELETE /api/procedures/{id}` - Supprimer une procédure (Admin)

### Notifications

- `GET /api/notifications` - Liste des notifications
- `GET /api/notifications/non-lues` - Notifications non lues
- `GET /api/notifications/count-non-lues` - Nombre de notifications non lues
- `PUT /api/notifications/{id}/lire` - Marquer comme lue
- `PUT /api/notifications/lire-tout` - Marquer toutes comme lues

## Structure du Projet

```
fasodocs-backend/
├── src/
│   ├── main/
│   │   ├── java/ml/fasodocs/backend/
│   │   │   ├── config/           # Configurations (Security, OpenAPI, etc.)
│   │   │   ├── controller/       # Contrôleurs REST
│   │   │   ├── dto/              # Data Transfer Objects
│   │   │   ├── entity/           # Entités JPA
│   │   │   ├── repository/       # Repositories
│   │   │   ├── security/         # Configuration JWT et Security
│   │   │   ├── service/          # Services métier
│   │   │   └── FasodocsBackendApplication.java
│   │   └── resources/
│   │       ├── application.properties
│   │       └── firebase-service-account.json
│   └── test/
├── pom.xml
└── README.md
```

## Modèle de Données

### Entités Principales

- **Citoyen** : Utilisateurs de l'application
- **Procedure** : Procédures administratives
- **Categorie** : Catégories de procédures
- **DocumentRequis** : Documents nécessaires pour une procédure
- **LieuDeTraitement** : Lieux où effectuer les démarches
- **Notification** : Notifications envoyées aux citoyens
- **Traduction** : Traductions en Bambara
- **HistoriqueLog** : Historique des actions
- **Chatbot** : Assistant vocal
- **Role** : Rôles utilisateurs (CITOYEN, ADMIN)

## Sécurité

L'application utilise **JWT (JSON Web Tokens)** pour l'authentification :

1. L'utilisateur s'inscrit et se connecte
2. Un token JWT est généré et retourné
3. Ce token doit être inclus dans les requêtes suivantes :
   ```
   Authorization: Bearer {votre-token}
   ```

## Tests

Pour exécuter les tests :

```bash
mvn test
```

## Déploiement

### Production

1. Modifiez `application.properties` pour la production
2. Générez le fichier JAR :
   ```bash
   mvn clean package -DskipTests
   ```
3. Exécutez le JAR :
   ```bash
   java -jar target/fasodocs-backend-1.0.0.jar
   ```

## Roadmap

- [x] Semaine 1 : Cadrage technique et conception
- [x] Semaine 2-3 : Développement backend et frontend
- [ ] Semaine 4 : Tests intensifs, correction de bugs, déploiement

## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créez une branche (`git checkout -b feature/NouvelleFonctionnalite`)
3. Committez vos changements (`git commit -m 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/NouvelleFonctionnalite`)
5. Ouvrez une Pull Request

## Licence

Ce projet est sous licence MIT.

## Contact

Pour toute question ou suggestion :

- **Email** : contact@fasodocs.ml
- **Équipe** : Tenen Madyeh SYLLA, Daba DIALLO

---

**FasoDocs** - Simplifions ensemble les démarches administratives au Mali 🇲🇱
