# 🗄️ CONFIGURATION MYSQL POUR GESBOUITE

## ÉTAPES DE CONFIGURATION

### 1. Démarrer XAMPP et MySQL
1. Ouvrir XAMPP Control Panel
2. Démarrer le service Apache
3. Démarrer le service MySQL
4. Vérifier que les deux services sont en vert

### 2. Créer la base de données via phpMyAdmin
1. Ouvrir votre navigateur
2. Aller à : http://localhost/phpmyadmin
3. Cliquer sur "Nouvelle base de données"
4. Nom : `gesbou`
5. Cliquer sur "Créer"

### 3. Configuration Laravel (.env)
Le fichier .env a été mis à jour avec :
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gesbou
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Recréer les tables (car changement de BDD)
Exécuter les commandes suivantes dans le terminal :
```bash
php artisan migrate:fresh
php artisan db:seed
```

### 5. Avantages de MySQL vs SQLite
- ✅ **Performance** : Plus rapide pour les grosses données
- ✅ **Concurrence** : Gère plusieurs utilisateurs simultanés
- ✅ **Scalabilité** : Supporte des millions d'enregistrements
- ✅ **Backup** : Outils de sauvegarde professionnels
- ✅ **Sécurité** : Gestion fine des permissions
- ✅ **Maintenance** : Outils d'optimisation intégrés

### 6. Vérification
Après configuration, l'application utilisera MySQL avec :
- Toutes les fonctionnalités identiques
- Performance améliorée
- Base de données plus robuste
- Sauvegarde facile via phpMyAdmin

## 🚀 PRÊT À UTILISER

Une fois ces étapes terminées, votre application GesBoutique utilisera MySQL comme base de données principale !
