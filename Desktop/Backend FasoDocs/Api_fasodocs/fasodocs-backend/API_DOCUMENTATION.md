# Documentation API FasoDocs

## Base URL
```
http://localhost:8080/api
```

## Format des Réponses

Toutes les réponses sont au format JSON.

### Réponse de Succès Standard
```json
{
  "message": "Opération réussie",
  "success": true
}
```

### Réponse d'Erreur Standard
```json
{
  "message": "Description de l'erreur",
  "success": false
}
```

---

## Authentification

### POST /auth/inscription
Inscription d'un nouveau citoyen.

**Corps de la Requête :**
```json
{
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou.traore@example.com",
  "telephone": "76123456",
  "motDePasse": "Password123",
  "languePreferee": "fr"
}
```

**Validation :**
- `nom` : requis, 2-100 caractères
- `prenom` : requis, 2-100 caractères
- `email` : optionnel mais doit être valide
- `telephone` : optionnel, 8-20 caractères
- `motDePasse` : requis, 6-40 caractères
- `languePreferee` : optionnel, par défaut "fr"
- Au moins email OU téléphone requis

**Réponse (200 OK) :**
```json
{
  "message": "Inscription réussie! Veuillez vérifier votre email.",
  "success": true
}
```

**Erreurs Possibles :**
- 400 : Email déjà utilisé
- 400 : Téléphone déjà utilisé
- 400 : Validation échouée

---

### POST /auth/connexion
Connexion d'un citoyen existant.

**Corps de la Requête :**
```json
{
  "identifiant": "amadou.traore@example.com",
  "motDePasse": "Password123",
  "tokenFcm": "firebase-token-optionnel"
}
```

**Paramètres :**
- `identifiant` : email OU téléphone
- `motDePasse` : requis
- `tokenFcm` : optionnel, pour les notifications push

**Réponse (200 OK) :**
```json
{
  "token": "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhbWFkb3UudHJhb...",
  "type": "Bearer",
  "id": 1,
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou.traore@example.com",
  "telephone": "76123456",
  "languePreferee": "fr"
}
```

**Erreurs Possibles :**
- 400 : Identifiants incorrects
- 400 : Compte non vérifié

---

### GET /auth/verify?code={code}
Vérification de l'email via le code reçu.

**Paramètres de Requête :**
- `code` : Code de vérification (string)

**Réponse (200 OK) :**
```json
{
  "message": "Email vérifié avec succès!",
  "success": true
}
```

**Erreurs Possibles :**
- 400 : Code invalide ou expiré

---

### GET /auth/profil
Récupération du profil du citoyen connecté.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Réponse (200 OK) :**
```json
{
  "id": 1,
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou.traore@example.com",
  "telephone": "76123456",
  "languePreferee": "fr",
  "estActif": true,
  "emailVerifie": true,
  "dateCreation": "2025-10-13T10:30:00",
  "dateModification": "2025-10-13T10:30:00"
}
```

---

### PUT /auth/profil
Mise à jour du profil.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Corps de la Requête :**
```json
{
  "nom": "Traoré",
  "prenom": "Amadou",
  "telephone": "76654321",
  "languePreferee": "bm"
}
```

**Réponse (200 OK) :**
```json
{
  "message": "Profil mis à jour avec succès!",
  "success": true
}
```

---

## Procédures

### GET /procedures
Liste toutes les procédures administratives.

**Réponse (200 OK) :**
```json
[
  {
    "id": 1,
    "nom": "CARTE_NINA",
    "titre": "Carte d'Identité Nationale NINA",
    "urlVersFormulaire": "https://anpe.gov.ml/formulaires/nina",
    "cout": 5000,
    "delai": "15 jours",
    "description": "Demande de carte d'identité nationale biométrique NINA",
    "etapes": [
      "Se rendre au centre d'enrôlement",
      "Présenter les documents requis",
      "Effectuer l'enrôlement biométrique",
      "Payer les frais",
      "Retirer la carte"
    ],
    "dateCreation": "2025-10-13T10:00:00",
    "dateModification": "2025-10-13T10:00:00",
    "categorie": {
      "id": 2,
      "titre": "Documents d'Identité",
      "description": "Cartes d'identité, passeports, permis de conduire",
      "iconeUrl": "https://example.com/icons/identite.png"
    },
    "documentsRequis": [
      {
        "id": 1,
        "description": "Acte de naissance ou jugement supplétif",
        "estObligatoire": true,
        "modeleUrl": null
      }
    ],
    "lieux": [
      {
        "id": 4,
        "nom": "Centre d'Enrôlement NINA - Bamako Centre",
        "adresse": "ACI 2000, Bamako",
        "horaires": "Lundi-Samedi: 8h-17h",
        "coordonneesGPS": "12.6550,-7.9900",
        "latitude": 12.6550,
        "longitude": -7.9900,
        "telephone": "+223 76 12 34 56",
        "email": "nina.bamako@anpe.ml"
      }
    ]
  }
]
```

---

### GET /procedures/{id}
Détails d'une procédure spécifique.

**Paramètres de Chemin :**
- `id` : ID de la procédure (Long)

**Réponse (200 OK) :**
Même structure qu'une procédure dans la liste ci-dessus.

**Erreurs Possibles :**
- 400 : Procédure non trouvée

---

### GET /procedures/categorie/{categorieId}
Procédures d'une catégorie spécifique.

**Paramètres de Chemin :**
- `categorieId` : ID de la catégorie (Long)

**Réponse (200 OK) :**
Tableau de procédures (même structure que GET /procedures)

---

### GET /procedures/rechercher?q={terme}
Recherche de procédures par nom ou titre.

**Paramètres de Requête :**
- `q` : Terme de recherche (string)

**Exemple :**
```
GET /procedures/rechercher?q=carte
```

**Réponse (200 OK) :**
Tableau de procédures correspondant au terme de recherche

---

### POST /procedures (ADMIN)
Création d'une nouvelle procédure.

**Headers Requis :**
```
Authorization: Bearer {admin-token}
```

**Corps de la Requête :**
```json
{
  "nom": "NOUVELLE_PROCEDURE",
  "titre": "Titre de la procédure",
  "urlVersFormulaire": "https://example.com/formulaire",
  "cout": 10000,
  "delai": "30 jours",
  "description": "Description détaillée",
  "etapes": [
    "Étape 1",
    "Étape 2"
  ],
  "categorieId": 1
}
```

**Réponse (200 OK) :**
Procédure créée (même structure que GET /procedures/{id})

**Erreurs Possibles :**
- 403 : Accès refusé (non admin)
- 400 : Catégorie non trouvée

---

### PUT /procedures/{id} (ADMIN)
Mise à jour d'une procédure.

**Headers Requis :**
```
Authorization: Bearer {admin-token}
```

**Corps de la Requête :**
Même structure que POST /procedures

**Réponse (200 OK) :**
Procédure mise à jour

**Note :** Si le coût ou le délai change, des notifications seront envoyées automatiquement.

---

### DELETE /procedures/{id} (ADMIN)
Suppression d'une procédure.

**Headers Requis :**
```
Authorization: Bearer {admin-token}
```

**Réponse (200 OK) :**
```json
{
  "message": "Procédure supprimée avec succès!",
  "success": true
}
```

---

## Notifications

### GET /notifications
Liste toutes les notifications du citoyen connecté.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Réponse (200 OK) :**
```json
[
  {
    "id": 1,
    "contenu": "La procédure 'Carte NINA' a été mise à jour. Nouveau coût: 5000 FCFA",
    "dateEnvoi": "2025-10-13T14:30:00",
    "estLue": false,
    "type": "MISE_A_JOUR",
    "procedureId": 1,
    "procedureNom": "CARTE_NINA"
  }
]
```

---

### GET /notifications/non-lues
Notifications non lues uniquement.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Réponse (200 OK) :**
Tableau de notifications non lues

---

### GET /notifications/count-non-lues
Nombre de notifications non lues.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Réponse (200 OK) :**
```json
5
```

---

### PUT /notifications/{id}/lire
Marquer une notification comme lue.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Réponse (200 OK) :**
```json
{
  "id": 1,
  "contenu": "...",
  "estLue": true,
  ...
}
```

---

### PUT /notifications/lire-tout
Marquer toutes les notifications comme lues.

**Headers Requis :**
```
Authorization: Bearer {token}
```

**Réponse (200 OK) :**
```
200 OK (pas de corps)
```

---

## Codes d'État HTTP

- **200 OK** : Requête réussie
- **400 Bad Request** : Erreur de validation ou données invalides
- **401 Unauthorized** : Token manquant ou invalide
- **403 Forbidden** : Accès refusé (permissions insuffisantes)
- **404 Not Found** : Ressource non trouvée
- **500 Internal Server Error** : Erreur serveur

---

## Authentification JWT

### Format du Token

Les tokens JWT doivent être inclus dans le header `Authorization` :

```
Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhbWFkb3UudHJh...
```

### Durée de Validité

- Token JWT : **24 heures**
- Après expiration, l'utilisateur doit se reconnecter

---

## Langues Supportées

- **fr** : Français
- **bm** : Bambara

La langue est stockée dans le profil utilisateur et utilisée pour personnaliser le contenu.

---

## Pagination (À venir)

Les endpoints qui retournent des listes supporteront la pagination :

```
GET /procedures?page=0&size=10&sort=nom,asc
```

---

## Taux de Limitation (Rate Limiting)

Actuellement non implémenté. Sera ajouté dans les versions futures.

---

## Support

Pour toute question sur l'API :
- **Documentation Swagger** : http://localhost:8080/api/swagger-ui.html
- **Email** : support@fasodocs.ml
