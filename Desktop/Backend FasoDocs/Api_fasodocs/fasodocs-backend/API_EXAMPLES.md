# 📚 FasoDocs API - Examples d'Utilisation

Guide complet avec des exemples pratiques pour l'inscription et l'authentification.

---

## 🔐 1. INSCRIPTION (Registration)

### Endpoint
```
POST http://localhost:8080/api/auth/inscription
```

### Headers
```
Content-Type: application/json
```

### Request Body (JSON)

**Exemple avec Email:**
```json
{
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou.traore@example.com",
  "telephone": "76543210",
  "motDePasse": "motdepasse123",
  "languePreferee": "fr"
}
```

**Exemple avec Téléphone uniquement:**
```json
{
  "nom": "Diarra",
  "prenom": "Fatoumata",
  "email": null,
  "telephone": "70123456",
  "motDePasse": "secure123",
  "languePreferee": "bm"
}
```

**Exemple Bambara:**
```json
{
  "nom": "Coulibaly",
  "prenom": "Sekou",
  "email": "sekou@gmail.com",
  "telephone": "79876543",
  "motDePasse": "password456",
  "languePreferee": "bm"
}
```

### Response Success (200 OK)
```json
{
  "success": true,
  "message": "Inscription réussie! Un email de vérification a été envoyé.",
  "data": null
}
```

### Response Error (400 Bad Request)
```json
{
  "success": false,
  "message": "Email déjà utilisé",
  "data": null
}
```

### Validation Errors
```json
{
  "timestamp": "2025-10-13T15:30:00",
  "status": 400,
  "errors": {
    "nom": "Le nom est obligatoire",
    "motDePasse": "Le mot de passe doit contenir entre 6 et 40 caractères"
  }
}
```

---

## 🔑 2. CONNEXION (Login)

### Endpoint
```
POST http://localhost:8080/api/auth/connexion
```

### Headers
```
Content-Type: application/json
```

### Request Body (JSON)

**Connexion avec Email:**
```json
{
  "identifiant": "amadou.traore@example.com",
  "motDePasse": "motdepasse123"
}
```

**Connexion avec Téléphone:**
```json
{
  "identifiant": "76543210",
  "motDePasse": "motdepasse123"
}
```

**Connexion avec Token FCM (pour notifications push):**
```json
{
  "identifiant": "amadou.traore@example.com",
  "motDePasse": "motdepasse123",
  "tokenFcm": "fDqJ8X9_RZG:APA91bH..."
}
```

### Response Success (200 OK)
```json
{
  "token": "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhbWFkb3UudHJhb3JlQGV4YW1wbGUuY29tIiwiaWF0IjoxNjk3MjA4MDAwLCJleHAiOjE2OTcyOTQ0MDB9.XYZ...",
  "type": "Bearer",
  "id": 1,
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou.traore@example.com",
  "telephone": "76543210",
  "roles": ["ROLE_USER"],
  "languePreferee": "fr"
}
```

### Response Error (400 Bad Request)
```json
{
  "success": false,
  "message": "Erreur de connexion: Identifiants incorrects",
  "data": null
}
```

---

## ✅ 3. VÉRIFICATION EMAIL

### Endpoint
```
GET http://localhost:8080/api/auth/verify?code=abc123-xyz789
```

### Response Success
```json
{
  "success": true,
  "message": "Email vérifié avec succès!",
  "data": null
}
```

---

## 👤 4. OBTENIR LE PROFIL (Authentifié)

### Endpoint
```
GET http://localhost:8080/api/auth/profil
```

### Headers
```
Authorization: Bearer eyJhbGciOiJIUzUxMiJ9...
```

### Response Success
```json
{
  "id": 1,
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou.traore@example.com",
  "telephone": "76543210",
  "estActif": true,
  "emailVerifie": true,
  "languePreferee": "fr",
  "dateCreation": "2025-10-13T15:30:00",
  "roles": [
    {
      "id": 1,
      "nom": "ROLE_USER"
    }
  ]
}
```

---

## ✏️ 5. METTRE À JOUR LE PROFIL (Authentifié)

### Endpoint
```
PUT http://localhost:8080/api/auth/profil
```

### Headers
```
Authorization: Bearer eyJhbGciOiJIUzUxMiJ9...
Content-Type: application/json
```

### Request Body
```json
{
  "nom": "Traoré",
  "prenom": "Amadou Bakary",
  "email": "amadou.traore@newmail.com",
  "telephone": "76543210",
  "languePreferee": "bm",
  "ancienMotDePasse": "motdepasse123",
  "nouveauMotDePasse": "nouveaumotdepasse456"
}
```

### Response Success
```json
{
  "success": true,
  "message": "Profil mis à jour avec succès",
  "data": null
}
```

---

## 🚪 6. DÉCONNEXION (Logout)

### Endpoint
```
POST http://localhost:8080/api/auth/deconnexion
```

### Headers
```
Authorization: Bearer eyJhbGciOiJIUzUxMiJ9...
```

### Response Success
```json
{
  "success": true,
  "message": "Déconnexion réussie",
  "data": null
}
```

---

## 📱 EXEMPLES AVEC CURL

### 1. Inscription
```bash
curl -X POST http://localhost:8080/api/auth/inscription \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Traoré",
    "prenom": "Amadou",
    "email": "amadou.traore@example.com",
    "telephone": "76543210",
    "motDePasse": "motdepasse123",
    "languePreferee": "fr"
  }'
```

### 2. Connexion
```bash
curl -X POST http://localhost:8080/api/auth/connexion \
  -H "Content-Type: application/json" \
  -d '{
    "identifiant": "amadou.traore@example.com",
    "motDePasse": "motdepasse123"
  }'
```

### 3. Obtenir le Profil (avec token)
```bash
curl -X GET http://localhost:8080/api/auth/profil \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

---

## 🌐 EXEMPLES AVEC POSTMAN

### Configuration de l'Environnement

1. **Créer un environnement "FasoDocs Local"**
   - Variable: `baseUrl` = `http://localhost:8080/api`
   - Variable: `token` = (vide au début)

### Collection: Authentification

#### 1. POST - Inscription
- **URL**: `{{baseUrl}}/auth/inscription`
- **Method**: POST
- **Headers**: 
  - `Content-Type: application/json`
- **Body (raw JSON)**:
```json
{
  "nom": "Traoré",
  "prenom": "Amadou",
  "email": "amadou@example.com",
  "telephone": "76543210",
  "motDePasse": "password123",
  "languePreferee": "fr"
}
```

#### 2. POST - Connexion
- **URL**: `{{baseUrl}}/auth/connexion`
- **Method**: POST
- **Headers**: 
  - `Content-Type: application/json`
- **Body (raw JSON)**:
```json
{
  "identifiant": "amadou@example.com",
  "motDePasse": "password123"
}
```
- **Tests (pour sauvegarder le token)**:
```javascript
var jsonData = pm.response.json();
if (jsonData.token) {
    pm.environment.set("token", jsonData.token);
}
```

#### 3. GET - Profil
- **URL**: `{{baseUrl}}/auth/profil`
- **Method**: GET
- **Headers**: 
  - `Authorization: Bearer {{token}}`

---

## 💻 EXEMPLES AVEC JAVASCRIPT (Fetch API)

### 1. Inscription
```javascript
async function inscription() {
  try {
    const response = await fetch('http://localhost:8080/api/auth/inscription', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        nom: 'Traoré',
        prenom: 'Amadou',
        email: 'amadou@example.com',
        telephone: '76543210',
        motDePasse: 'password123',
        languePreferee: 'fr'
      })
    });
    
    const data = await response.json();
    console.log('Inscription:', data);
    
    if (data.success) {
      alert('Inscription réussie!');
    } else {
      alert('Erreur: ' + data.message);
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}
```

### 2. Connexion
```javascript
async function connexion() {
  try {
    const response = await fetch('http://localhost:8080/api/auth/connexion', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        identifiant: 'amadou@example.com',
        motDePasse: 'password123'
      })
    });
    
    const data = await response.json();
    console.log('Connexion:', data);
    
    if (data.token) {
      // Sauvegarder le token
      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data));
      alert('Connexion réussie!');
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}
```

### 3. Obtenir le Profil
```javascript
async function obtenirProfil() {
  try {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://localhost:8080/api/auth/profil', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const data = await response.json();
    console.log('Profil:', data);
    return data;
  } catch (error) {
    console.error('Erreur:', error);
  }
}
```

---

## 📱 EXEMPLES AVEC AXIOS (React/Vue/Angular)

### Installation
```bash
npm install axios
```

### Configuration Axios
```javascript
// api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8080/api',
  headers: {
    'Content-Type': 'application/json'
  }
});

// Intercepteur pour ajouter le token JWT
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => Promise.reject(error)
);

export default api;
```

### Service d'Authentification
```javascript
// authService.js
import api from './api';

export const authService = {
  async inscription(userData) {
    try {
      const response = await api.post('/auth/inscription', userData);
      return response.data;
    } catch (error) {
      throw error.response?.data || error.message;
    }
  },

  async connexion(credentials) {
    try {
      const response = await api.post('/auth/connexion', credentials);
      if (response.data.token) {
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data));
      }
      return response.data;
    } catch (error) {
      throw error.response?.data || error.message;
    }
  },

  async obtenirProfil() {
    try {
      const response = await api.get('/auth/profil');
      return response.data;
    } catch (error) {
      throw error.response?.data || error.message;
    }
  },

  deconnexion() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
};
```

### Utilisation dans un Composant React
```javascript
import React, { useState } from 'react';
import { authService } from './services/authService';

function InscriptionForm() {
  const [formData, setFormData] = useState({
    nom: '',
    prenom: '',
    email: '',
    telephone: '',
    motDePasse: '',
    languePreferee: 'fr'
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await authService.inscription(formData);
      if (response.success) {
        alert('Inscription réussie!');
      }
    } catch (error) {
      alert('Erreur: ' + error.message);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="text"
        placeholder="Nom"
        value={formData.nom}
        onChange={(e) => setFormData({...formData, nom: e.target.value})}
      />
      <input
        type="text"
        placeholder="Prénom"
        value={formData.prenom}
        onChange={(e) => setFormData({...formData, prenom: e.target.value})}
      />
      <input
        type="email"
        placeholder="Email"
        value={formData.email}
        onChange={(e) => setFormData({...formData, email: e.target.value})}
      />
      <input
        type="tel"
        placeholder="Téléphone"
        value={formData.telephone}
        onChange={(e) => setFormData({...formData, telephone: e.target.value})}
      />
      <input
        type="password"
        placeholder="Mot de passe"
        value={formData.motDePasse}
        onChange={(e) => setFormData({...formData, motDePasse: e.target.value})}
      />
      <button type="submit">S'inscrire</button>
    </form>
  );
}

export default InscriptionForm;
```

---

## 🧪 TESTS AVEC JEST/VITEST

```javascript
import { authService } from './authService';

describe('AuthService', () => {
  test('Inscription réussie', async () => {
    const userData = {
      nom: 'Traoré',
      prenom: 'Amadou',
      email: 'test@example.com',
      telephone: '76543210',
      motDePasse: 'password123',
      languePreferee: 'fr'
    };

    const response = await authService.inscription(userData);
    expect(response.success).toBe(true);
  });

  test('Connexion réussie', async () => {
    const credentials = {
      identifiant: 'test@example.com',
      motDePasse: 'password123'
    };

    const response = await authService.connexion(credentials);
    expect(response.token).toBeDefined();
    expect(response.type).toBe('Bearer');
  });
});
```

---

## ⚠️ Gestion des Erreurs

### Erreurs Communes

| Code | Signification | Solution |
|------|---------------|----------|
| 400 | Bad Request | Vérifier les données envoyées |
| 401 | Unauthorized | Token JWT invalide ou expiré |
| 403 | Forbidden | Pas les permissions nécessaires |
| 404 | Not Found | Endpoint incorrect |
| 500 | Server Error | Problème côté serveur |

### Exemple de Gestion d'Erreurs
```javascript
try {
  const response = await authService.connexion(credentials);
  // Succès
} catch (error) {
  if (error.status === 401) {
    alert('Identifiants incorrects');
  } else if (error.status === 500) {
    alert('Erreur serveur, veuillez réessayer');
  } else {
    alert('Erreur: ' + error.message);
  }
}
```

---

## 🔒 Sécurité - Bonnes Pratiques

1. **Stockage du Token JWT**
   - ✅ Utiliser `localStorage` ou `sessionStorage`
   - ✅ Supprimer le token à la déconnexion
   - ❌ Ne jamais stocker le mot de passe

2. **Envoi du Token**
   - Toujours utiliser le header `Authorization: Bearer {token}`
   - Ne jamais envoyer le token dans l'URL

3. **Validation côté Client**
   - Valider les champs avant l'envoi
   - Afficher des messages d'erreur clairs

4. **Expiration du Token**
   - Le token JWT expire après 24h (configurable)
   - Rediriger vers la page de connexion si le token expire

---

## 📞 Support

Pour plus d'informations:
- Documentation Swagger: http://localhost:8080/api/swagger-ui.html
- API Docs JSON: http://localhost:8080/api/v3/api-docs

Bon développement! 🚀
