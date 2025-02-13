<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PokemonType extends Model
{
    use HasFactory;

    protected $table = 'pokemon_types'; // Asegúrate de que esté apuntando a la tabla correcta

    protected $fillable = ['type', 'color']; // Los campos que se pueden llenar
}

