<?php

namespace App\Http\Controllers;

use App\Models\PokemonType;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function getPokemon(Request $request)
    {
        $pokemonNameOrId = $request->input('pokemon');

        // Si no hay input, selecciona un Pokémon aleatorio
        if (empty($pokemonNameOrId)) {
            $pokemonNameOrId = rand(1, 1025);
        }

        $url = "https://pokeapi.co/api/v2/pokemon/" . strtolower($pokemonNameOrId);

        try {
            $response = file_get_contents($url);
            if ($response === false) {
                throw new \Exception("Error al obtener los datos");
            }

            $pokemonData = json_decode($response, true);

            // Obtener el primer tipo del Pokémon
            $primaryType = $pokemonData['types'][0]['type']['name'] ?? null;

            // Obtener el color correspondiente
            $color = $primaryType ? $this->getColorForType($primaryType) : "#ccc";
            $sprite = $pokemonData['sprites']['front_default'] ?? null;
            return response()->json([
                'success' => true,
                'pokemon' => $pokemonData,
                'color' => $color,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron resultados'
            ]);
        }
    }







    public function index(Request $request)
    {
        $pokemon = null;
        $headerColor = '#ccc';
        $errorMessage = null; // Para manejar el mensaje de error

        // Si el input está vacío, seleccionamos un Pokémon aleatorio
        $pokemonName = $request->input('pokemon');
        if (empty($pokemonName)) {
            $pokemonName = rand(1, 1025);
        }

        // Llamamos a la función getPokemon para obtener los datos del Pokémon
        $requestPokemon = new Request(['pokemon' => $pokemonName]);
        $response = $this->getPokemon($requestPokemon)->getData(true);

        // Comprobamos si la respuesta fue exitosa
        if ($response['success']) {
            // Filtramos los datos que necesitamos
            $pokemon = [
                'name' => $response['pokemon']['name'] ?? null,
                'type' => $response['pokemon']['types'][0]['type']['name'] ?? null, // Primer tipo
                'sprite' => $response['pokemon']['sprites']['front_default'] ?? null, // Sprite front_default
                'stats' => array_map(function($stat) {
                    return [
                        'name' => $stat['stat']['name'] ?? null,
                        'value' => $stat['base_stat'] ?? null
                    ];
                }, $response['pokemon']['stats'] ?? []),
            ];

            // Color del tipo principal
            $headerColor = $response['color'];
        } else {
            $errorMessage = $response['message']; // Guardamos el mensaje de error
        }

        // Pasamos las variables a la vista
        return view('api', compact('pokemon', 'headerColor', 'errorMessage'));
    }





    public function getColorForType($type): string
    {
        $color = PokemonType::where('type', strtolower($type))->value('color');
        return $color ?? "#ccc";  // Retorna el color del tipo, o gris por defecto si no existe
    }


}
