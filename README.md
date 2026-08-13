# Gestion Boutique

Application de gestion de boutique développée avec Laravel : produits, stock, ventes (point de vente), clôture de caisse et rapports. Conçue pour fonctionner **en local sans connexion Internet** (service worker, file d'attente hors-ligne pour les ventes).

## Stack technique

- PHP 8.2+ / Laravel 12 (Blade, pas de front séparé)
- Tailwind CSS 4 + Vite (assets servis localement)
- MySQL (ou SQLite pour les tests)
- `barryvdh/laravel-dompdf` pour la génération des factures et rapports PDF

## Fonctionnalités

- Authentification et rôles (`gerant`, `gestionnaire`, `caissier`) avec restrictions d'accès par section
- Produits : CRUD, catégories, codes-barres, import/export CSV
- Stock : entrées de stock, mouvements tracés, alertes de rupture/stock faible
- Ventes (point de vente) : vente multi-lignes, calcul de la monnaie, facture PDF, synchronisation hors-ligne
- Clôture de caisse quotidienne (écart théorique/réel par mode de paiement)
- Rapports ventes/stocks/performances avec export CSV et PDF

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

## Personnalisation logo client

1. Placez le logo dans `public/logo-client.png` (ou `.svg` / `.jpg`)
2. Vérifiez `config/facture.php` → `logo_path` (par défaut `logo-client.svg`)
3. Optionnel : renseignez le nom/adresse dans `config/facture.php` → `entreprise`

Le logo apparaît dans la sidebar, l’aperçu de facture et les PDF.

L'application enregistre les ventes localement si le réseau est indisponible et les synchronise au retour de la connexion. Aucune dépendance à des CDN externes : tout est servi depuis votre machine (`php artisan serve` + MySQL local).

## Tests

```bash
php artisan test
```

## Rôles et permissions

| Rôle | Accès |
|---|---|
| `caissier` | Dashboard, produits, ventes, stock, rapports |
| `gestionnaire` | + modification produits, import CSV, annulation ventes, catégories |
| `gerant` | + clôture de caisse |
