<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PokemonTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pokemon_types')->insert([
            ['type' => 'normal', 'color' => '#A8A77A'],
            ['type' => 'fire', 'color' => '#EE8130'],
            ['type' => 'water', 'color' => '#6390F0'],
            ['type' => 'electric', 'color' => '#F7D02C'],
            ['type' => 'grass', 'color' => '#7AC74C'],
            ['type' => 'ice', 'color' => '#96D9D6'],
            ['type' => 'fighting', 'color' => '#C22E28'],
            ['type' => 'poison', 'color' => '#A33EA1'],
            ['type' => 'ground', 'color' => '#E2BF65'],
            ['type' => 'flying', 'color' => '#A98FF3'],
            ['type' => 'psychic', 'color' => '#F95587'],
            ['type' => 'bug', 'color' => '#A6B91A'],
            ['type' => 'rock', 'color' => '#B6A136'],
            ['type' => 'ghost', 'color' => '#735797'],
            ['type' => 'dragon', 'color' => '#6F35FC'],
            ['type' => 'dark', 'color' => '#705746'],
            ['type' => 'steel', 'color' => '#B7B7CE'],
            ['type' => 'fairy', 'color' => '#D685AD'],
        ]);
    }
}
