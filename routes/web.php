<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParishController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('register');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('parishes', ParishController::class);
    Route::resource('parishes.categories', CategoryController::class);
    Route::resource('entries', EntryController::class);
    Route::get('entries/{entry}/download', [EntryController::class, 'download'])->name('entries.download');
    Route::get('categories/{category}/fields', [EntryController::class, 'fields'])->name('categories.fields');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('payments/{payment}/generate-invoice', [PaymentController::class, 'generateInvoice'])->name('payments.generate-invoice');
    Route::get('payments/{payment}/download-invoice', [PaymentController::class, 'downloadInvoice'])->name('payments.download-invoice');

    // Statistiques détaillées
    Route::get('stats', [App\Http\Controllers\StatsController::class, 'index'])->name('stats.index');

    // Configuration des paroisses
    Route::get('config/invoice', [App\Http\Controllers\ParishConfigController::class, 'invoiceConfig'])->name('config.invoice');
    Route::post('config/invoice', [App\Http\Controllers\ParishConfigController::class, 'updateInvoiceConfig'])->name('config.invoice.update');

    // Route pour valider un chemin de stockage
    Route::post('parishes/validate-storage-path', [ParishController::class, 'validateStoragePath'])->name('parishes.validate-storage-path');

    // Route pour afficher les fichiers d'une paroisse
    Route::get('parishes/{parish}/files', [ParishController::class, 'files'])->name('parishes.files');

    // Outils d'administration (admin seulement)
    Route::get('admin/storage-test', [App\Http\Controllers\AdminToolsController::class, 'storageTest'])->name('admin.storage-test');
    Route::post('admin/test-path', [App\Http\Controllers\AdminToolsController::class, 'testPath'])->name('admin.test-path');
});

require __DIR__.'/auth.php';
