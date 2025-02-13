@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Welcome Section -->
        <div class="search-section">
            <h2>Welcome to the Pokédex</h2>
            <p>Discover everything about your favorite Pokémon. The Pokédex is your perfect tool to learn detailed information about each Pokémon, their types, abilities, stats, and much more.</p>
            <p>Here you can explore the details of a wide variety of Pokémon and learn more about them. It's the perfect time to start your adventure!</p>

            <!-- Call to Action Button -->
            <a href="{{ route('login') }}" class="btn-pokedex">Start using the Pokédex</a>

        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Info Section -->
        <div class="card-section">
            <div class="pokemon-card">
                <div class="header" style="background-color: #0039f5;">
                    <img src="{{ asset('images/pokedex.jpeg') }}" alt="Pokédex Logo" class="w-32 mx-auto" style="background-color:#0039f5">
                </div>
                <div class="content text-center">
                    <h3>What is the Pokédex?</h3>
                    <p>The Pokédex is an electronic encyclopedia that contains information about all known Pokémon. By logging in, you can access a complete catalog, from the most famous to the rarest, and learn everything about them!</p>
                    <p>From their types and abilities to detailed stats, our Pokédex will help you get to know each Pokémon inside out. Start your adventure now!</p>
                </div>
            </div>
        </div>
    </div>
@endsection
