<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ProduitController as ApiProduitController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route d'accueil redirigée vers le dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes protégées (nécessitent l'authentification)
Route::middleware(['auth'])->group(function () {
    // Page d'accueil - Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes pour les catégories
    Route::resource('categories', CategorieController::class);

    // Routes pour les produits
    Route::resource('produits', ProduitController::class);

    // Routes pour les ventes
    Route::resource('ventes', VenteController::class);
    Route::get('/ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');

    // Routes pour les stocks
    Route::get('/stocks', [\App\Http\Controllers\StockControllerSimple::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create', [\App\Http\Controllers\StockControllerSimple::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [\App\Http\Controllers\StockControllerSimple::class, 'store'])->name('stocks.store');

    // Routes pour les rapports
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/ventes', [RapportController::class, 'ventes'])->name('rapports.ventes');
    Route::get('/rapports/stocks', [RapportController::class, 'stocks'])->name('rapports.stocks');
    Route::get('/rapports/performances', [RapportController::class, 'performances'])->name('rapports.performances');
    Route::get('/rapports/export/ventes', [RapportController::class, 'exportVentes'])->name('rapports.export.ventes');
    
    // Routes pour les exports PDF uniquement
    Route::get('/rapports/export/pdf/ventes', [RapportController::class, 'exportPdfVentes'])->name('rapports.export.pdf.ventes');
    Route::get('/rapports/export/pdf/stocks', [RapportController::class, 'exportPdfStocks'])->name('rapports.export.pdf.stocks');
    Route::get('/rapports/export/pdf/complete', [RapportController::class, 'exportPdfComplete'])->name('rapports.export.pdf.complete');
    
    // Routes API
    Route::get('/api/produits/search', [ApiProduitController::class, 'search'])->name('api.produits.search');
});
