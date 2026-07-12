# Gestion Boutique

Application de gestion de boutique développée avec Laravel : produits, stock, ventes (point de vente), clients, fournisseurs, commandes, clôture de caisse et rapports.

## Stack technique

- PHP 8.2+ / Laravel 12 (Blade, pas de front séparé)
- Tailwind CSS 4 + Vite
- MySQL (ou SQLite pour les tests)
- `barryvdh/laravel-dompdf` pour la génération des factures et rapports PDF

## Fonctionnalités

- Authentification et rôles (`gerant`, `gestionnaire`, `caissier`) avec restrictions d'accès par section
- Produits : CRUD, catégories, codes-barres, import/export CSV
- Stock : entrées de stock, mouvements tracés, alertes de rupture/stock faible
- Ventes (point de vente) : vente multi-lignes, calcul de la monnaie, facture PDF
- Fournisseurs et commandes : cycle de statuts avec réception qui réalimente le stock
- Clôture de caisse quotidienne (écart théorique/réel par mode de paiement)
- Rapports ventes/stocks/performances avec export CSV et PDF
- Sauvegarde de la base de données

## Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configurer la base de données dans `.env` (MySQL recommandé en local, SQLite fonctionne aussi) :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gesbou
DB_USERNAME=root
DB_PASSWORD=
```

Puis :

```bash
php artisan migrate --seed
npm run build   # ou `npm run dev` en développement
php artisan serve
```

Le seeder crée un compte gérant (`admin@gesbou.com`) avec un mot de passe généré aléatoirement et affiché dans la console — notez-le et changez-le dès la première connexion. Un compte de démonstration (`test@gesbou.com` / `test123`, rôle caissier) n'est créé qu'en environnement `local`/`testing`.

## Tests

```bash
php artisan test
```

## Rôles et permissions

| Rôle | Accès |
|---|---|
| `caissier` | Dashboard, produits, ventes, stock, clients |
| `gestionnaire` | + fournisseurs, commandes |
| `gerant` | + gestion des utilisateurs, sauvegardes |
