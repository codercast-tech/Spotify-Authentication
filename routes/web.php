<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SpotifyAuthController;

// Rutas públicas de tu aplicación
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Ruta para redirigir al usuario a la página de inicio de sesión de Spotify
Route::get('/auth/spotify', [SpotifyAuthController::class, 'redirectToSpotify'])->name('spotify.login');

// Ruta para manejar la redirección de Spotify después de la autorización
Route::get('/auth/spotify/callback', [SpotifyAuthController::class, 'handleSpotifyCallback'])->name('spotify.callback');

// Ruta para el cierre de sesión de Spotify
Route::post('/auth/spotify/logout', [SpotifyAuthController::class, 'logout'])->name('spotify.logout');

// Rutas protegidas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta protegida del dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Aquí puedes añadir más rutas protegidas...
});
