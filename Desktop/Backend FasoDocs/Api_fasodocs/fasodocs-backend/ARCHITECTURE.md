# Architecture du Backend FasoDocs

## Vue d'Ensemble

FasoDocs Backend est une application Spring Boot RESTful qui suit l'architecture en couches (Layered Architecture) avec une séparation claire des responsabilités.

## Architecture en Couches

```
┌─────────────────────────────────────────────────────┐
│              CLIENTS (Mobile App)                   │
│        Android / iOS / Web Application              │
└─────────────────────────────────────────────────────┘
                        ↓ HTTP/HTTPS
┌─────────────────────────────────────────────────────┐
│           COUCHE PRÉSENTATION (Controllers)         │
│  AuthController | ProcedureController | ...         │
│           - Validation des requêtes                 │
│           - Gestion des réponses HTTP               │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│         COUCHE SÉCURITÉ (Security Layer)            │
│   JWT Authentication | Authorization | CORS         │
│           - Vérification des tokens                 │
│           - Gestion des permissions                 │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│           COUCHE MÉTIER (Services)                  │
│   AuthService | ProcedureService | ...              │
│           - Logique métier                          │
│           - Orchestration des opérations            │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│        COUCHE PERSISTANCE (Repositories)            │
│   CitoyenRepo | ProcedureRepo | ...                 │
│           - Accès aux données                       │
│           - Requêtes personnalisées                 │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│              BASE DE DONNÉES MySQL                  │
│           Tables JPA / Hibernate                    │
└─────────────────────────────────────────────────────┘

        ┌───────────────────────────────┐
        │  SERVICES EXTERNES            │
        │  - Firebase (Push)            │
        │  - Email (SMTP)               │
        └───────────────────────────────┘
```

## Structure des Packages

```
ml.fasodocs.backend/
│
├── config/                          # Configurations
│   ├── SecurityConfig.java          # Configuration Spring Security
│   ├── OpenApiConfig.java           # Configuration Swagger
│   └── DataInitializer.java         # Initialisation des données
│
├── controller/                      # Contrôleurs REST
│   ├── AuthController.java          # Authentification
│   ├── ProcedureController.java     # Gestion des procédures
│   └── NotificationController.java  # Notifications
│
├── dto/                             # Data Transfer Objects
│   ├── request/                     # DTOs pour les requêtes
│   │   ├── InscriptionRequest.java
│   │   ├── ConnexionRequest.java
│   │   └── ProcedureRequest.java
│   └── response/                    # DTOs pour les réponses
│       ├── JwtResponse.java
│       ├── MessageResponse.java
│       └── ProcedureResponse.java
│
├── entity/                          # Entités JPA
│   ├── Citoyen.java                 # Utilisateur
│   ├── Procedure.java               # Procédure administrative
│   ├── Categorie.java               # Catégorie de procédure
│   ├── DocumentRequis.java          # Document requis
│   ├── LieuDeTraitement.java        # Lieu administratif
│   ├── Notification.java            # Notification
│   ├── Traduction.java              # Traduction Bambara
│   ├── HistoriqueLog.java           # Historique
│   ├── LoiArticle.java              # Loi/Article
│   ├── Parametre.java               # Paramètres système
│   ├── Chatbot.java                 # Chatbot
│   └── Role.java                    # Rôle utilisateur
│
├── repository/                      # Repositories JPA
│   ├── CitoyenRepository.java
│   ├── ProcedureRepository.java
│   ├── CategorieRepository.java
│   └── ... (un repository par entité)
│
├── security/                        # Sécurité JWT
│   ├── JwtUtils.java                # Génération/validation JWT
│   ├── AuthTokenFilter.java         # Filtre d'authentification
│   ├── AuthEntryPointJwt.java       # Gestion des erreurs auth
│   ├── UserDetailsImpl.java         # Implémentation UserDetails
│   └── UserDetailsServiceImpl.java  # Service de chargement utilisateur
│
├── service/                         # Services métier
│   ├── AuthService.java             # Authentification
│   ├── ProcedureService.java        # Gestion des procédures
│   ├── NotificationService.java     # Notifications
│   ├── FirebaseService.java         # Notifications push
│   ├── EmailService.java            # Envoi d'emails
│   └── HistoriqueService.java       # Historique des actions
│
└── FasodocsBackendApplication.java  # Classe principale
```

## Flux de Données

### Exemple : Inscription d'un Citoyen

```
1. CLIENT
   └─> POST /api/auth/inscription { nom, prenom, email, password }
        │
2. CONTROLLER (AuthController)
   └─> Validation des données (@Valid)
        │
3. SERVICE (AuthService)
   └─> Vérification : email existe ?
   └─> Hashage du mot de passe (BCrypt)
   └─> Création de l'entité Citoyen
   └─> Attribution du rôle ROLE_CITOYEN
   └─> Génération du code de vérification
        │
4. REPOSITORY (CitoyenRepository)
   └─> Sauvegarde en base de données
        │
5. SERVICE EXTERNE (EmailService)
   └─> Envoi de l'email de vérification
        │
6. RÉPONSE au CLIENT
   └─> { message: "Inscription réussie!", success: true }
```

### Exemple : Consultation d'une Procédure avec Authentification

```
1. CLIENT
   └─> GET /api/procedures/1
       Header: Authorization: Bearer {token}
        │
2. SECURITY FILTER (AuthTokenFilter)
   └─> Extraction du token JWT
   └─> Validation du token (JwtUtils)
   └─> Chargement de l'utilisateur (UserDetailsService)
   └─> Mise en contexte de sécurité
        │
3. CONTROLLER (ProcedureController)
   └─> Appel du service
        │
4. SERVICE (ProcedureService)
   └─> Récupération de la procédure
   └─> Enregistrement de l'historique (HistoriqueService)
   └─> Conversion en DTO
        │
5. REPOSITORY (ProcedureRepository)
   └─> Requête JPA avec jointures (categorie, documents, lieux)
        │
6. RÉPONSE au CLIENT
   └─> { id, nom, titre, cout, delai, categorie, documentsRequis, lieux }
```

## Patterns Utilisés

### 1. Repository Pattern
- Abstraction de la couche de persistance
- Utilisation de Spring Data JPA
- Requêtes personnalisées avec @Query

### 2. DTO Pattern
- Séparation entre entités et objets transférés
- Validation avec Bean Validation (@Valid)
- Conversion manuelle (pas de MapStruct pour simplifier)

### 3. Service Layer Pattern
- Logique métier centralisée dans les services
- Transactions gérées avec @Transactional
- Réutilisabilité du code

### 4. Dependency Injection
- Utilisation d'@Autowired
- Couplage faible entre les composants
- Facilite les tests unitaires

### 5. JWT Authentication
- Authentification stateless
- Token portant les informations utilisateur
- Validation à chaque requête

## Modèle de Données

### Relations Principales

```
Citoyen (1) ──────< (N) Notification
Citoyen (1) ──────< (N) HistoriqueLog
Citoyen (N) ──────< (N) Role

Categorie (1) ────< (N) Procedure
Procedure (1) ────< (N) DocumentRequis
Procedure (1) ────< (N) Traduction
Procedure (1) ────< (N) LoiArticle
Procedure (N) ────< (N) LieuDeTraitement
```

### Diagramme Entité-Relation Simplifié

```
┌─────────────┐         ┌──────────────┐
│   Citoyen   │────┬───<│ Notification │
└─────────────┘    │    └──────────────┘
       │           │    ┌──────────────┐
       │           └───<│ HistoriqueLog│
       │                └──────────────┘
       ↓
   ┌──────┐
   │ Role │
   └──────┘

┌─────────────┐         ┌──────────────┐
│  Categorie  │────────<│  Procedure   │
└─────────────┘         └──────────────┘
                              │
                    ┌─────────┼─────────┬──────────────┐
                    ↓         ↓         ↓              ↓
            ┌─────────┐ ┌───────────┐ ┌──────────┐ ┌────────┐
            │Document │ │Traduction │ │LoiArticle│ │  Lieu  │
            │ Requis  │ │           │ │          │ │  Trait.│
            └─────────┘ └───────────┘ └──────────┘ └────────┘
```

## Sécurité

### Stratégies de Sécurité

1. **Authentification JWT**
   - Token signé avec HS512
   - Expiration : 24 heures
   - Secret stocké dans configuration

2. **Hashage des Mots de Passe**
   - BCrypt avec salt automatique
   - Force de hashage : 10 rounds

3. **CORS**
   - Configuré pour accepter les origines spécifiées
   - Headers autorisés : *
   - Méthodes : GET, POST, PUT, DELETE, OPTIONS

4. **Validation des Entrées**
   - Bean Validation (@NotBlank, @Email, @Size)
   - Validation au niveau contrôleur

5. **Autorisation Basée sur les Rôles**
   - ROLE_CITOYEN : accès standard
   - ROLE_ADMIN : gestion des procédures

### Endpoints Sécurisés

- ✅ **Publics** : /auth/inscription, /auth/connexion, /procedures (lecture)
- 🔒 **Authentifiés** : /auth/profil, /notifications
- 🔐 **Admin uniquement** : /procedures (écriture)

## Technologies et Versions

| Technologie           | Version | Utilisation                    |
|-----------------------|---------|--------------------------------|
| Java                  | 17      | Langage de programmation       |
| Spring Boot           | 3.1.5   | Framework principal            |
| Spring Security       | 6.1.5   | Sécurité et authentification   |
| Spring Data JPA       | 3.1.5   | Persistance des données        |
| Hibernate             | 6.2.13  | ORM                            |
| MySQL                 | 8.0+    | Base de données                |
| JWT (jjwt)            | 0.11.5  | Génération de tokens           |
| Firebase Admin SDK    | 9.2.0   | Notifications push             |
| Springdoc OpenAPI     | 2.2.0   | Documentation Swagger          |
| Lombok                | 1.18.30 | Réduction du code boilerplate  |
| Maven                 | 3.8+    | Gestion des dépendances        |

## Performance et Optimisation

### Stratégies d'Optimisation

1. **Lazy Loading**
   - Relations @ManyToOne et @OneToMany en LAZY par défaut
   - Fetch EAGER uniquement pour les rôles

2. **Indexation**
   - Index unique sur email, telephone
   - Index sur les clés étrangères

3. **Caching** (À venir)
   - Cache des procédures fréquemment consultées
   - Cache Redis pour les sessions

4. **Pagination** (À venir)
   - Limitation des résultats avec Pageable
   - Navigation par page

## Tests

### Types de Tests

1. **Tests Unitaires**
   - Services avec Mockito
   - Couverture > 80%

2. **Tests d'Intégration** (À venir)
   - Tests des contrôleurs avec MockMvc
   - Tests des repositories avec H2

3. **Tests de Sécurité** (À venir)
   - Tests d'authentification
   - Tests d'autorisation

## Déploiement

### Environnements

1. **Développement**
   - Base de données locale
   - Firebase désactivé (optionnel)
   - Logs DEBUG

2. **Production** (À venir)
   - Base de données distante
   - Firebase configuré
   - Logs INFO/ERROR
   - HTTPS obligatoire

### Conteneurisation Docker (À venir)

```dockerfile
FROM openjdk:17-jdk-slim
COPY target/fasodocs-backend-1.0.0.jar app.jar
ENTRYPOINT ["java","-jar","/app.jar"]
```

## Évolutions Futures

### Fonctionnalités Prévues

- ✅ Chatbot intelligent avec IA
- ✅ Synthèse vocale en Bambara
- ✅ Géolocalisation avancée avec itinéraires
- ✅ Système de favoris
- ✅ Partage de procédures
- ✅ Statistiques et analytics
- ✅ API GraphQL (alternative REST)
- ✅ WebSocket pour notifications temps réel

### Améliorations Techniques

- ✅ Cache distribué (Redis)
- ✅ Load balancing
- ✅ CI/CD avec GitHub Actions
- ✅ Monitoring avec Prometheus/Grafana
- ✅ Logs centralisés avec ELK

## Contribution

Pour contribuer au projet :

1. Respecter l'architecture en couches
2. Suivre les conventions de nommage
3. Ajouter des tests pour chaque nouvelle fonctionnalité
4. Documenter les endpoints dans Swagger
5. Utiliser les DTOs pour les échanges
6. Gérer les erreurs avec des exceptions personnalisées

## Contact Technique

- **Lead Développeur Backend** : Tenen Madyeh SYLLA
- **Architecte** : Daba DIALLO
- **Email** : tech@fasodocs.ml

---

**FasoDocs Backend** - Architecture robuste pour une application au service du Mali 🇲🇱
