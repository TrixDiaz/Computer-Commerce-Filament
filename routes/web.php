<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

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

Route::get('product-profile/{slug}', function ($slug) {
    return view('product-profile', ['slug' => $slug]);
})->name('product-profile');

require __DIR__ . '/auth.php';
