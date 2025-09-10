<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    protected $table = 'restaurantes';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['nombre','direccion','tipo'];

    public function menus(){ return $this->hasMany(Menu::class,'restaurante_id','id'); }
}