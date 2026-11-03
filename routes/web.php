<?php

use App\Livewire\Actions\Logout;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ExchangeRates;
use App\Livewire\Admin\Products\Form as ProductsForm;
use App\Livewire\Admin\Products\Index as ProductsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

/* PERFIL */
Route::view('profile', 'profile')
    ->middleware('auth')
    ->name('profile');

/* CERRAR SESIÓN */
Route::post('logout', function (Logout $logout) {
    $logout();
    return redirect()->route('home');
})->middleware('auth')->name('logout');

/* ===================== ADMINISTRACIÓN ===================== */
Route::middleware(['auth', 'staff'])->group(function () {

    /* DASHBOARD */
    Route::get('/dashboard', Dashboard::class)->middleware('permission:dashboard.show')->name('dashboard');

    /* TIPO DE CAMBIO */
    Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
            Route::get('/', ExchangeRates::class)->middleware('permission:exchange-rates.show')->name('index');
    });

    /* PRODUCTOS */
    Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', ProductsIndex::class)->middleware('permission:products.show')->name('index');
            Route::get('/create', ProductsForm::class)->middleware('permission:products.create')->name('create');
            Route::get('/{product}/edit', ProductsForm::class)->middleware('permission:products.edit')->name('edit');
    });
});

require __DIR__.'/auth.php';
