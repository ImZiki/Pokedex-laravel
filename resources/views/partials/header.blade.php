<header>
    <div class="logo">
        <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="PokéAPI Logo">
        <h1>PokéAPI</h1>
    </div>
    <nav>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav__submit" type="submit">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Sign up</a>
        @endauth
    </nav>
</header>

