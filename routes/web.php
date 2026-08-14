<?php

use App\Http\Controllers\Api\ProduitController as ApiProduitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ClotureCaisseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\SauvegardeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\VenteSyncController;
use App\Models\BoutiqueProduit;
use App\Support\BoutiqueContext;
use Illuminate\Support\Facades\Route;

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1')->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', fn () => redirect()->route('dashboard'));

// Routes protégées par l'authentification
Route::middleware(['auth', 'boutique'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API interne (notifications stock bas, autocomplétion produits)
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/api/produits/search', [ApiProduitController::class, 'search'])->name('api.produits.search');
        Route::get('/api/stock/alertes', function () {
            $stocks = BoutiqueProduit::dansBoutique(BoutiqueContext::id())
                ->enAlerte()
                ->whereHas('produit', fn ($q) => $q->where('active', true))
                ->with('produit')
                ->get();

            $produits = $stocks->map(fn ($s) => [
                'id' => $s->produit->id,
                'nom' => $s->produit->nom,
                'reference' => $s->produit->reference,
                'stock_actuel' => $s->stock_actuel,
                'stock_min' => $s->stock_min,
            ]);

            return response()->json($produits);
        })->name('api.stock.alertes');
    });

    // Catégories : réservées au gérant et au gestionnaire
    Route::middleware(['role:gerant,gestionnaire'])->group(function () {
        Route::resource('categories', CategorieController::class)
            ->parameters(['categories' => 'categorie']);
    });

    // Produits + codes-barres. Consultation et création restent accessibles
    // au caissier (voir README des rôles), cohérent avec le fait qu'il peut
    // déjà créer un produit à la volée via une entrée de stock. En revanche,
    // modifier le prix d'un produit existant, le désactiver, ou importer un
    // catalogue en masse par CSV sont des opérations à risque (le premier
    // laisse un caissier changer discrètement un prix, le second peut faire
    // disparaître de l'historique un produit déjà vendu, le troisième touche
    // potentiellement tout le catalogue) : réservées à gestionnaire/gérant.
    Route::resource('produits', ProduitController::class)->except(['edit', 'update', 'destroy']);
    Route::get('/produits/{produit}/barcode', [ProduitController::class, 'barcode'])->name('produits.barcode');
    Route::get('/produits-barcodes', [ProduitController::class, 'barcodeAll'])->name('produits.barcode.all');

    Route::middleware(['role:gerant,gestionnaire'])->group(function () {
        Route::get('/produits/{produit}/edit', [ProduitController::class, 'edit'])->name('produits.edit');
        Route::put('/produits/{produit}', [ProduitController::class, 'update'])->name('produits.update');
        Route::delete('/produits/{produit}', [ProduitController::class, 'destroy'])->name('produits.destroy');
        Route::get('/produits-import', [ProduitController::class, 'importForm'])->name('produits.import');
        Route::post('/produits-import', [ProduitController::class, 'importCsv'])->name('produits.import.csv');
        Route::get('/produits-import/modele', [ProduitController::class, 'exportCsvTemplate'])->name('produits.import.modele');
    });

    // Ventes
    // "destroy" est à part : annuler une vente déjà encaissée (pas la
    // supprimer, voir VenteController::destroy) doit rester réservé à
    // gestionnaire/gérant, pas au caissier qui l'a enregistrée.
    Route::resource('ventes', VenteController::class)->except(['destroy']);
    Route::get('/ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');
    Route::get('/ventes/{vente}/ticket', [VenteController::class, 'ticket'])->name('ventes.ticket');

    Route::middleware(['role:gerant,gestionnaire'])->group(function () {
        Route::delete('/ventes/{vente}', [VenteController::class, 'destroy'])->name('ventes.destroy');
    });

    // Synchronisation des ventes créées hors-ligne (file d'attente IndexedDB)
    Route::post('/api/ventes/sync', [VenteSyncController::class, 'sync'])
        ->middleware('throttle:30,1')
        ->name('api.ventes.sync');
    Route::get('/api/csrf-refresh', [VenteSyncController::class, 'rafraichirCsrf'])->name('api.csrf.refresh');

    // Stocks
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/mouvements', [StockController::class, 'mouvements'])->name('stocks.mouvements');
    Route::get('/stocks/{produit}/historique', [StockController::class, 'historique'])->name('stocks.historique');
    Route::get('/stocks/{produit}/ajuster', [StockController::class, 'ajusterForm'])->name('stocks.ajuster.form');
    Route::post('/stocks/{produit}/ajuster', [StockController::class, 'ajuster'])->name('stocks.ajuster');

    // Rapports (affichage + exports PDF/CSV) : accessibles à tous les rôles authentifiés
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/ventes', [RapportController::class, 'ventes'])->name('rapports.ventes');
    Route::get('/rapports/stocks', [RapportController::class, 'stocks'])->name('rapports.stocks');
    Route::get('/rapports/performances', [RapportController::class, 'performances'])->name('rapports.performances');
    Route::get('/rapports/export/ventes', [RapportController::class, 'exportVentes'])->name('rapports.export.ventes');
    Route::get('/rapports/export/pdf/ventes', [RapportController::class, 'exportPdfVentes'])->name('rapports.export.pdf.ventes');
    Route::get('/rapports/export/pdf/stocks', [RapportController::class, 'exportPdfStocks'])->name('rapports.export.pdf.stocks');
    Route::get('/rapports/export/pdf/complete', [RapportController::class, 'exportPdfComplete'])->name('rapports.export.pdf.complete');

    // Clôture de caisse : réservée au gérant
    Route::middleware(['role:gerant'])->group(function () {
        Route::get('/caisse', [ClotureCaisseController::class, 'index'])->name('caisse.index');
        Route::get('/caisse/create', [ClotureCaisseController::class, 'create'])->name('caisse.create');
        Route::post('/caisse', [ClotureCaisseController::class, 'store'])->name('caisse.store');
        Route::get('/caisse/{caisse}', [ClotureCaisseController::class, 'show'])->name('caisse.show');
    });

    // Paramètres (gérant seulement) : utilisateurs + sauvegarde/restauration,
    // réunis dans une seule page à onglets plutôt que des écrans séparés.
    Route::middleware(['role:gerant'])->group(function () {
        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');

        Route::resource('utilisateurs', UtilisateurController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);

        Route::post('/sauvegardes', [SauvegardeController::class, 'store'])->name('sauvegardes.store');
        Route::post('/sauvegardes/restaurer', [SauvegardeController::class, 'restaurer'])->name('sauvegardes.restaurer');
        Route::get('/sauvegardes/{fichier}/telecharger', [SauvegardeController::class, 'telecharger'])->name('sauvegardes.telecharger');
    });
});
