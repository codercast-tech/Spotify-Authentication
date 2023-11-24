<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SpotifyAuthController extends Controller
{
    public function redirectToSpotify()
    {
        $scopes = ['user-read-email', 'user-read-private']; // Define los alcances que necesitas
        $state = bin2hex(random_bytes(16)); // Genera un estado aleatorio para seguridad

        Session::put('spotify_state', $state); // Almacena el estado en la sesión

        $query = http_build_query([
            'client_id' => config('services.spotify.client_id'),
            'redirect_uri' => config('services.spotify.redirect_uri'),
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'state' => $state,
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $query);
    }

    public function handleSpotifyCallback(Request $request)
    {
        if ($request->state !== Session::get('spotify_state')) {
            return redirect('/')->with('error', 'Invalid state');
        }

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'redirect_uri' => config('services.spotify.redirect_uri'),
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            return redirect('/')->with('error', 'Error al obtener el token de acceso: ' . $data['error_description']);
        }

        Session::put('spotify_access_token', $data['access_token']);
        Session::put('spotify_refresh_token', $data['refresh_token']);

        $spotifyProfile = $this->getSpotifyUserProfile($data['access_token']);

        if (isset($spotifyProfile['error'])) {
            return redirect('/')->with('error', 'Error al obtener información del perfil de Spotify: ' . $spotifyProfile['error']['message']);
        }

        Session::put('spotify_user_name', $spotifyProfile['display_name']);

        return redirect('/dashboard')->with('success', 'Autenticación de Spotify exitosa.');
    }

    private function getSpotifyUserProfile($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.spotify.com/v1/me');

        return $response->json();
    }

    public function logout()
    {
        Session::forget(['spotify_access_token', 'spotify_refresh_token', 'spotify_user_name']);

        // Cambia aquí para redirigir al dashboard en lugar de la página principal
        return redirect('/dashboard')->with('success', 'Cierre de sesión de Spotify exitoso.');
    }
}
