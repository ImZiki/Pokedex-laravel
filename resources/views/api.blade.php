@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="search-section">
            <!-- Formulario que envía por POST -->
            <form method="POST" action="{{ route('api.index') }}">
                @csrf
                <label for="pokemon">Nombre o ID del Pokémon:</label>
                <input type="text" id="pokemon" name="pokemon" placeholder="Ej: Pikachu">
                <input type="submit" value="Buscar">
            </form>
        </div>

        <div class="divider"></div>

        <div class="card-section">
            @if ($pokemon)
                <div class="pokemon-card">
                    <div class="header" style="background-color: {{ $headerColor }};">
                        <img src="{{ $pokemon['sprite'] ?? 'default-image-url.jpg' }}" alt="{{ ucfirst($pokemon['name']) }}">
                    </div>
                    <div class="content">
                        <h3>{{ ucfirst($pokemon['name']) }}</h3>
                        <p><strong>Type:</strong> {{ ucfirst($pokemon['type']) }}</p>
                        @foreach ($pokemon['stats'] as $stat)
                            <p><strong>{{ ucfirst($stat['name']) }}:</strong> {{ $stat['value'] }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($errorMessage)
                <div class="pokemon-card empty-card">
                    <div class="content">
                        <h3>No se encontraron resultados</h3>
                        <p>Inserta el ID o nombre de otro Pokemon</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
