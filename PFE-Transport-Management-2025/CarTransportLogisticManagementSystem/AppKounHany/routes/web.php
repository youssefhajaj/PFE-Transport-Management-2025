<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\SecretaireController;
use App\Http\Controllers\MetteurAuMainController;
use Illuminate\Support\Facades\Auth;

// use App\Exports\UsersExport;
// use Maatwebsite\Excel\Facades\Excel;

// Route::get('/export-users', function () {
//     return Excel::download(new UsersExport, 'users.xlsx');
// });


// 🔒 Redirect root '/' to role-specific dashboard after login
Route::get('/', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->isChef) {
        return redirect()->route('chef.dashboard');
    } elseif ($user->isSecretaire) {
        return redirect()->route('secretaire.dashboard');
    } elseif ($user->isResponsable) {
        return redirect()->route('responsable.dashboard');
    } elseif ($user->isLogistic || $user->isAssistantLogistic) {
        return redirect()->route('logistic.dashboard');
    }elseif ($user->isMetteurAuMain) { 
        return redirect()->route('metteur_au_main.dashboard');
    }

    return redirect()->route('dashboard');
})->middleware('auth');

// 📦 Transport routes
Route::middleware('auth')->group(function () {
    Route::post('/transports', [TransportController::class, 'store'])->name('transports.store');
    Route::put('/transports/prestataire/{id}', [LogisticController::class, 'updatePrestataire'])->name('transports.updatePrestataire');
    Route::put('/transports/{id}/chassis', [TransportController::class, 'updateChassis'])->name('transports.updateChassis');
    Route::put('/transports/{id}/update-bl', [TransportController::class, 'updateBL'])->name('transports.updateBL');
    Route::put('/transports/{id}/fill-bl', [TransportController::class, 'fillBLForm'])->name('transports.fillBL');
    Route::get('/transports/{id}/generate-bl', [TransportController::class, 'generateBL'])->name('transports.generateBL');
    Route::get('/transports/{id}/download-bl', [TransportController::class, 'downloadBL'])->name('transports.downloadBL');
    Route::post('/transports/{id}/sendBL', [TransportController::class, 'sendBL'])->name('transports.sendBL');
    Route::put('/transports/{id}/update-etatavancement', [TransportController::class, 'updateEtatavancement'])->name('transports.updateEtatavancement');
    Route::put('/transports/{id}/update-field', [TransportController::class, 'updateField'])->name('transports.updateField');
    Route::post('/transports/{id}/upload-file', [TransportController::class, 'uploadFile'])->name('transports.uploadFile');
    Route::get('/transports/{id}/download-file', [TransportController::class, 'downloadFile'])->name('transports.downloadFile');
    Route::put('/transports/{id}/update-numero-mission', [TransportController::class, 'updateNumeroMission'])->name('transports.updateNumeroMission');
    Route::get('/transports/view-file/{id}', [TransportController::class, 'viewFile'])->name('transports.viewFile');
    Route::put('/transports/{transport}/update-cachet', [TransportController::class, 'updateCachet'])
     ->name('transports.update-cachet');
    Route::get('/transports/{transport}/cachet', [TransportController::class, 'downloadCachet'])
      ->name('transports.cachet');

});

// 📜 Secretaire
Route::middleware('auth')->group(function () {
    Route::get('/secretaire/historie', [TransportController::class, 'historie'])->name('secretaire.historie');
    Route::get('/secretaire', [SecretaireController::class, 'dashboard'])->name('secretaire.dashboard');
    Route::get('/secretaire/demandes-disponibilite', [SecretaireController::class, 'demandesDisponibilite'])->name('secretaire.demandesDisponibilite');
    Route::delete('/secretaire/transport/{transport}', [SecretaireController::class, 'destroyTransport'])->name('secretaire.transport.destroy');
    Route::get('/secretaire/check-new-transports', [SecretaireController::class, 'checkNewTransports'])->name('secretaire.checkNewTransports');
});

// 👨‍🍳 Chef
Route::middleware('auth')->group(function () {
    Route::get('/chef/historie', [ChefController::class, 'historie'])->name('chef.historie');
    Route::get('/chef/dashboard', [ChefController::class, 'dashboard'])->name('chef.dashboard');
    Route::put('/chef/validate/{id}', [ChefController::class, 'validateRequest'])->name('chef.validate');
    Route::put('/chef/refuse/{id}', [ChefController::class, 'refuseRequest'])->name('chef.refuse');
});

// 👨‍💼 Responsable
Route::middleware('auth')->group(function () {
    Route::get('/responsable/historie', [ResponsableController::class, 'historie'])->name('responsable.historie');
    Route::get('/responsable/dashboard', [ResponsableController::class, 'dashboard'])->name('responsable.dashboard');
    Route::put('/responsable/validate/{id}', [ResponsableController::class, 'validateRequest'])->name('responsable.validate');
    Route::put('/responsable/refuse/{id}', [ResponsableController::class, 'refuseRequest'])->name('responsable.refuse');
});

// 🚚 Logistic
Route::middleware('auth')->group(function () {
    Route::get('/logistic/dashboard', [LogisticController::class, 'dashboard'])->name('logistic.dashboard');
    Route::get('/logistic/transport/new', [LogisticController::class, 'createTransport'])->name('logistic.transport.new');
    Route::post('/logistic/transport/store', [LogisticController::class, 'storeTransport'])->name('logistic.transport.store');
    Route::put('/transports/{transport}/etat-comment', [LogisticController::class, 'updateEtatComment'])->name('transports.updateEtatComment');
    Route::post('/logistic/export', [LogisticController::class, 'export'])->name('logistic.export');
    Route::put('/transports/{transport}/update-retard', [LogisticController::class, 'updateRetard'])->name('transports.updateRetard');
    Route::post('/transport/{transport}/update-roullete', [LogisticController::class, 'updateRoullete'])->name('transport.update-roullete');
    Route::post('/transport/update-zone', [LogisticController::class, 'updateZone'])->name('transport.update-zone');
    Route::post('/transport/update-kilometrage', [LogisticController::class, 'updateKilometrage'])->name('transport.update-kilometrage');

    // 👤 Users
    Route::get('/logistic/user-management', [LogisticController::class, 'userManagement'])->name('logistic.user.management');
    Route::get('/logistic/user/create', [LogisticController::class, 'createUser'])->name('logistic.user.create');
    Route::post('/logistic/user/store', [LogisticController::class, 'storeUser'])->name('logistic.user.store');
    Route::put('/logistic/user/{user}', [LogisticController::class, 'updateUser'])->name('logistic.user.update'); // ✨ New
    Route::delete('/logistic/user/{user}', [LogisticController::class, 'destroyUser'])->name('logistic.user.destroy');

    // ❌ Transport
    Route::delete('/logistic/transport/{transport}', [LogisticController::class, 'destroyTransport'])->name('logistic.transport.destroy');
    
    // 💰 Prix
    Route::post('/transport/update-prix', [LogisticController::class, 'updatePrix'])->name('transport.updatePrix');
    Route::post('/transport/update-prix-comment', [LogisticController::class, 'updatePrixComment'])->name('transport.updatePrixComment');

    // Statistiques
    Route::get('/logistic/statistics', [LogisticController::class, 'showStatistics'])->name('logistic.statistics');
});


Route::get('/logistics/new-transports', [LogisticController::class, 'checkNewTransports'])
    ->name('logistics.new-transports');

// 🛠️ MetteurAuMain routes
Route::middleware('auth')->group(function () {
    Route::get('/metteur_au_main/dashboard', [MetteurAuMainController::class, 'dashboard'])->name('metteur_au_main.dashboard');
    //Route::get('/metteur_au_main/historie', [MetteurAuMainController::class, 'historie'])->name('metteur_au_main.historie');
    // Add any other specific routes for this role
});

// 📊 General dashboard that redirects to role-specific dashboards
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isChef) {
        return redirect()->route('chef.dashboard');
    } elseif ($user->isSecretaire) {
        return redirect()->route('secretaire.dashboard');
    } elseif ($user->isResponsable) {
        return redirect()->route('responsable.dashboard');
    } elseif ($user->isLogistic || $user->isAssistantLogistic) {
        return redirect()->route('logistic.dashboard');
    }elseif ($user->isMetteurAuMain) { 
        return redirect()->route('metteur_au_main.dashboard');
    }

    // Fallback for users without specific roles (if needed)
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// 👤 Profile management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔐 Auth routes
require __DIR__.'/auth.php';
