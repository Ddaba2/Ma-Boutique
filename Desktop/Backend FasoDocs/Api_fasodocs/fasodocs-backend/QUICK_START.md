# 🚀 FasoDocs API - Quick Start Guide

## ⚡ Démarrage Rapide en 3 Minutes

### 1️⃣ Démarrer l'Application

```bash
cd fasodocs-backend
.\mvnw.cmd spring-boot:run
```

✅ L'application démarre sur: **http://localhost:8080**

---

### 2️⃣ Tester avec le Navigateur

Ouvrez le fichier `test-auth.html` dans votre navigateur:
- Double-cliquez sur le fichier
- Ou ouvrez: `file:///C:/Users/.../fasodocs-backend/test-auth.html`

**Test rapide:**
1. Remplissez le formulaire d'inscription
2. Cliquez "S'inscrire"
3. Allez dans l'onglet "Connexion"
4. Connectez-vous
5. Allez dans "Profil" et cliquez "Obtenir Mon Profil"

---

### 3️⃣ Tester avec Postman

**Importer la collection:**
1. Ouvrez Postman
2. File > Import
3. Sélectionnez `FasoDocs_API_Collection.postman_collection.json`
4. Créez un environnement avec:
   - Variable: `baseUrl` = `http://localhost:8080/api`
   - Variable: `token` = (vide)

**Tester:**
1. Exécutez "1. Inscription"
2. Exécutez "2. Connexion" (le token sera sauvegardé automatiquement)
3. Exécutez "5. Obtenir Profil"

---

## 📌 Endpoints Principaux

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| POST | `/api/auth/inscription` | Créer un compte | ❌ |
| POST | `/api/auth/connexion` | Se connecter | ❌ |
| GET | `/api/auth/profil` | Voir son profil | ✅ |
| PUT | `/api/auth/profil` | Modifier son profil | ✅ |
| POST | `/api/auth/deconnexion` | Se déconnecter | ✅ |

---

## 🔑 Exemples CURL Rapides

### Inscription
```bash
curl -X POST http://localhost:8080/api/auth/inscription \
  -H "Content-Type: application/json" \
  -d '{"nom":"Traoré","prenom":"Amadou","email":"amadou@example.com","telephone":"76543210","motDePasse":"password123","languePreferee":"fr"}'
```

### Connexion
```bash
curl -X POST http://localhost:8080/api/auth/connexion \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"amadou@example.com","motDePasse":"password123"}'
```

### Profil (avec token)
```bash
curl -X GET http://localhost:8080/api/auth/profil \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI"
```

---

## 📖 Documentation Complète

- **Exemples détaillés**: `API_EXAMPLES.md`
- **Swagger UI**: http://localhost:8080/api/swagger-ui.html
- **API Docs JSON**: http://localhost:8080/api/v3/api-docs

---

## ⚠️ Prérequis

✅ Java 17 installé  
✅ MySQL démarré  
✅ Base de données `fasodocs_db` créée  

---

## 🆘 Problèmes Courants

### Erreur: "Unknown database 'fasodocs_db'"
**Solution:**
```sql
CREATE DATABASE fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Erreur: "Port 8080 already in use"
**Solution:** Changez le port dans `application.properties`:
```properties
server.port=8081
```

### Erreur 401 Unauthorized
**Solution:** Le token JWT a expiré ou est invalide. Reconnectez-vous.

---

## 📞 Support

Pour plus d'aide, consultez:
- `DATABASE_SETUP_GUIDE.md` - Configuration de la base de données
- `API_EXAMPLES.md` - Exemples complets
- `ARCHITECTURE.md` - Architecture du projet

Bon développement! 🎉
