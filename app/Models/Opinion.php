<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opinion extends Model
{
    protected $table = 'opiniones';
    public $timestamps = false; 

    protected $fillable = ['usuario_id', 'mensaje', 'fecha'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }
}
