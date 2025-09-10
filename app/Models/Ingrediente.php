<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $table = 'ingredientes';

    
    public $timestamps = false;

    protected $fillable = [
        'nombre','categoria',
        'calorias','proteina','grasa','carbo','azucar','sodio_mg',
        'es_gluten','es_lactosa','es_animal',
    ];

    protected $casts = [
        'es_gluten'  => 'boolean',
        'es_lactosa' => 'boolean',
        'es_animal'  => 'boolean',
        'calorias'   => 'integer',
        'sodio_mg'   => 'integer',
    ];
}
