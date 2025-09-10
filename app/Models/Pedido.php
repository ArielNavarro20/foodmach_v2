<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    public $timestamps = false; 

    protected $fillable = [
        'usuario_id','menu_id','fecha',
        'nombre_form','edad_form','direccion_form','envio_form',
        'enfermedad_form','preferencia_form','alimento_form'
    ];

    public function usuario(){ return $this->belongsTo(Usuario::class,'usuario_id','id'); }
    public function menu(){ return $this->belongsTo(Menu::class,'menu_id','id'); }
}