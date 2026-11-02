<?php

use App\Livewire\Actions\Logout;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ExchangeRates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('dashboard', function (Request $request) {
    return $request->user()->can('dashboard.show')
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

Route::view('profile', 'profile')
    ->middleware('auth')
    ->name('profile');

Route::post('logout', function (Logout $logout) {
    $logout();
    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'permission:dashboard.show'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        /* DASHBOARD */
        Route::get('/', Dashboard::class)->name('dashboard');
        /* EXCHANGE RATES */
        Route::get('/exchange-rates', ExchangeRates::class)
            ->middleware('permission:exchange-rates.show')
            ->name('exchange-rates.index');
    });

require __DIR__ . '/auth.php';
