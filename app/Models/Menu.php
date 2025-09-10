<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['restaurante_id','nombre','descripcion','caracteristicas'];

    public function restaurante(){ return $this->belongsTo(Restaurante::class,'restaurante_id','id'); }
    public function pedidos(){ return $this->hasMany(Pedido::class,'menu_id','id'); }
}