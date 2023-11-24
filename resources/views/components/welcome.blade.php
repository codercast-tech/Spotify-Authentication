<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    @if (Session::has('spotify_user_name'))
        <!-- Usuario autenticado y tiene una sesión de Spotify: Mostrar información de Spotify y botón de Logout -->
        <p class="mt-4 text-gray-800 leading-relaxed">
            Welcome, {{ Session::get('spotify_user_name') }}!
        </p>
        <form method="POST" action="{{ route('spotify.logout') }}">
            @csrf
            <button type="submit" class="btn-logout mt-4">
                Logout from Spotify
            </button>
        </form>
    @else
        <!-- Usuario no autenticado: Mostrar botón de Login with Spotify -->
        <a href="{{ route('spotify.login') }}" class="btn-spotify mt-2">
            Login with Spotify
        </a>
    @endif
</div>

