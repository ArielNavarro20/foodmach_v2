<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['nombre','email','password','rol','creado_en'];
    protected $hidden   = ['password','remember_token'];

    public function isAdmin(): bool { return $this->rol === 'admin'; }
    public function pedidos() { return $this->hasMany(Pedido::class,'usuario_id','id'); }
}
