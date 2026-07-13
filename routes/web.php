<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\ClotureCaisseController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\Api\ProduitController as ApiProduitController;

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', fn() => redirect()->route('dashboard'));

// Routes protégées par l'authentification
Route::middleware(['auth', 'boutique'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Boutiques (gérant uniquement)
    Route::middleware(['role:gerant'])->group(function () {
        Route::resource('boutiques', BoutiqueController::class)->except(['show', 'destroy']);
        Route::get('/boutiques-selectionner', [BoutiqueController::class, 'selectionner'])->name('boutiques.selectionner');
        Route::post('/boutique/switch', [BoutiqueController::class, 'switch'])->name('boutiques.switch');
    });

    // API interne (notifications stock bas)
    Route::get('/api/produits/search', [ApiProduitController::class, 'search'])->name('api.produits.search');
    Route::get('/api/stock/alertes', function () {
        $stocks = \App\Models\BoutiqueProduit::dansBoutique(\App\Support\BoutiqueContext::id())
            ->enAlerte()
            ->whereHas('produit', fn($q) => $q->where('active', true))
            ->with('produit')
            ->get();

        $produits = $stocks->map(fn($s) => [
            'id' => $s->produit->id,
            'nom' => $s->produit->nom,
            'reference' => $s->produit->reference,
            'stock_actuel' => $s->stock_actuel,
            'stock_min' => $s->stock_min,
        ]);

        return response()->json($produits);
    })->name('api.stock.alertes');

    // Catégories
    // Paramètre nommé explicitement : Str::singular('categories') donnerait "category" (règle
    // d'inflexion anglaise -ies/-y), qui ne correspond pas à "$categorie" et casserait le
    // model-binding implicite (résolution silencieuse d'un modèle vide au lieu d'une 404/injection réelle).
    Route::resource('categories', CategorieController::class)
        ->parameters(['categories' => 'categorie']);

    // Produits + codes-barres + import CSV
    Route::resource('produits', ProduitController::class);
    Route::get('/produits/{produit}/barcode', [ProduitController::class, 'barcode'])->name('produits.barcode');
    Route::get('/produits-barcodes', [ProduitController::class, 'barcodeAll'])->name('produits.barcode.all');
    Route::get('/produits-import', [ProduitController::class, 'importForm'])->name('produits.import');
    Route::post('/produits-import', [ProduitController::class, 'importCsv'])->name('produits.import.csv');
    Route::get('/produits-import/modele', [ProduitController::class, 'exportCsvTemplate'])->name('produits.import.modele');

    // Ventes
    Route::resource('ventes', VenteController::class);
    Route::get('/ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');
    Route::get('/ventes/{vente}/ticket', [VenteController::class, 'ticket'])->name('ventes.ticket');

    // Stocks
    Route::get('/stocks', [\App\Http\Controllers\StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create', [\App\Http\Controllers\StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [\App\Http\Controllers\StockController::class, 'store'])->name('stocks.store');

    // Rapports
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/ventes', [RapportController::class, 'ventes'])->name('rapports.ventes');
    Route::get('/rapports/stocks', [RapportController::class, 'stocks'])->name('rapports.stocks');
    Route::get('/rapports/performances', [RapportController::class, 'performances'])->name('rapports.performances');
    Route::get('/rapports/export/ventes', [RapportController::class, 'exportVentes'])->name('rapports.export.ventes');
    Route::get('/rapports/export/pdf/ventes', [RapportController::class, 'exportPdfVentes'])->name('rapports.export.pdf.ventes');
    Route::get('/rapports/export/pdf/stocks', [RapportController::class, 'exportPdfStocks'])->name('rapports.export.pdf.stocks');
    Route::get('/rapports/export/pdf/complete', [RapportController::class, 'exportPdfComplete'])->name('rapports.export.pdf.complete');

    // Clients
    Route::resource('clients', ClientController::class);

    // Fournisseurs (gestionnaire + gérant)
    Route::middleware(['role:gerant,gestionnaire'])->group(function () {
        Route::resource('fournisseurs', FournisseurController::class);
        Route::resource('commandes', CommandeController::class);
        Route::patch('/commandes/{commande}/statut', [CommandeController::class, 'update'])->name('commandes.statut');
        Route::get('/commandes/{commande}/pdf', [CommandeController::class, 'pdf'])->name('commandes.pdf');
    });

    // Clôture de caisse
    Route::get('/caisse', [ClotureCaisseController::class, 'index'])->name('caisse.index');
    Route::get('/caisse/create', [ClotureCaisseController::class, 'create'])->name('caisse.create');
    Route::post('/caisse', [ClotureCaisseController::class, 'store'])->name('caisse.store');
    Route::get('/caisse/{caisse}', [ClotureCaisseController::class, 'show'])->name('caisse.show');

    // Utilisateurs et sauvegardes (gérant seulement)
    Route::middleware(['role:gerant'])->group(function () {
        Route::resource('utilisateurs', UtilisateurController::class)->except(['show']);
        Route::get('/sauvegardes', [BackupController::class, 'index'])->name('sauvegardes.index');
        Route::post('/sauvegardes', [BackupController::class, 'store'])->name('sauvegardes.store');
        Route::get('/sauvegardes/download', [BackupController::class, 'download'])->name('sauvegardes.download');
        Route::delete('/sauvegardes', [BackupController::class, 'destroy'])->name('sauvegardes.destroy');
    });
});
