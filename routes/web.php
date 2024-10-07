<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('catalog', 'catalog')
    ->name('catalog');

Route::view('cart', 'cart')
    ->name('cart');

Route::view('payment', 'payment')
    ->middleware(['auth', 'verified'])
    ->name('payment');

require __DIR__ . '/auth.php';
