<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /* home principal del admin*/
    public function index()
    {
        // Pedidos usuario  menú  restaurante
        $ped = DB::table('pedidos as p')
            ->join('usuarios as u', 'u.id', '=', 'p.usuario_id')
            ->join('menus as m', 'm.id', '=', 'p.menu_id')
            ->join('restaurantes as r', 'r.id', '=', 'm.restaurante_id')
            ->select(
                'p.id','p.fecha',
                'u.email as usuario_email',
                'p.nombre_form','p.edad_form','p.direccion_form','p.envio_form',
                'p.enfermedad_form','p.preferencia_form','p.alimento_form',
                'm.nombre as menu_nombre','m.descripcion',
                'r.nombre as restaurante_nombre','r.direccion as restaurante_direccion'
            )
            ->orderBy('p.fecha', 'desc')
            ->get();

        // Restaurantes
        $rest = DB::table('restaurantes')->orderBy('id', 'desc')->get();

        // Menús
        $men = DB::table('menus as m')
            ->join('restaurantes as r', 'r.id', '=', 'm.restaurante_id')
            ->select('m.id','m.nombre','m.descripcion','m.caracteristicas','r.nombre as rest_nombre','m.restaurante_id')
            ->orderBy('m.id', 'desc')
            ->get();

        // Opiniones
        $ops = DB::table('opiniones as o')
            ->leftJoin('usuarios as u', 'u.id', '=', 'o.usuario_id')
            ->select('o.id','o.mensaje','o.fecha','u.email as usuario_email','o.usuario_id')
            ->orderBy('o.fecha', 'desc')
            ->get();

        // nuevo Ingredientes  los últimos 10
        $ingrs = DB::table('ingredientes')
            ->select(
                'id','nombre','categoria',
                'calorias','proteina','grasa','carbo','azucar','sodio_mg',
                'es_gluten','es_lactosa','es_animal'
            )
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.index', compact('ped','rest','men','ops','ingrs')); 
    }

    /* 
       pedidoEditar y Eliminar
    */
    public function pedidoEdit($id)
    {
        $p = DB::table('pedidos')->where('id', $id)->first();
        if (!$p) abort(404);

        $menus = DB::table('menus')->orderBy('nombre')->get();

        return view('admin.pedido_edit', compact('p','menus'));
    }

    public function pedidoUpdate(Request $r, $id)
    {
        $r->validate([
            'menu_id'          => 'required|integer|exists:menus,id',
            'nombre_form'      => 'nullable|string|max:120',
            'edad_form'        => 'nullable|integer|min:1|max:120',
            'direccion_form'   => 'nullable|string|max:255',
            'envio_form'       => 'nullable|in:sí,no',
            'enfermedad_form'  => 'nullable|string|max:60',
            'preferencia_form' => 'nullable|string|max:60',
            'alimento_form'    => 'nullable|string|max:100',
        ]);

        DB::table('pedidos')->where('id', $id)->update([
            'menu_id'          => (int) $r->menu_id,
            'nombre_form'      => $r->nombre_form,
            'edad_form'        => $r->edad_form,
            'direccion_form'   => $r->direccion_form,
            'envio_form'       => $r->envio_form,
            'enfermedad_form'  => $r->enfermedad_form,
            'preferencia_form' => $r->preferencia_form,
            'alimento_form'    => $r->alimento_form,
        ]);

        return redirect()->route('admin.index')->with('ok', 'Pedido actualizado.');
    }

    public function eliminarPedido($id)
    {
        DB::table('pedidos')->where('id', $id)->delete();
        return back()->with('ok', 'Pedido eliminado');
    }

    /* 
       restaurant crear editar eliminar
    */
    public function restaurantesCreate()
    {
        return view('admin.restaurantes_form');
    }

    public function restaurantesStore(Request $r)
    {
        $r->validate([
            'nombre'    => 'required|string|max:100',
            'direccion' => 'required|string',
            'tipo'      => 'required|string|max:120',
        ]);

        DB::table('restaurantes')->insert([
            'nombre'    => $r->nombre,
            'direccion' => $r->direccion,
            'tipo'      => $r->tipo,
        ]);

        return redirect()->route('admin.index')->with('ok', 'Restaurante creado.');
    }

    public function restaurantesEdit($id)
    {
        $rest = DB::table('restaurantes')->where('id', $id)->first();
        if (!$rest) abort(404);
        return view('admin.restaurantes_form', compact('rest'));
    }

    public function restaurantesUpdate(Request $r, $id)
    {
        $r->validate([
            'nombre'    => 'required|string|max:100',
            'direccion' => 'required|string',
            'tipo'      => 'required|string|max:120',
        ]);

        DB::table('restaurantes')
            ->where('id', $id)
            ->update([
                'nombre'    => $r->nombre,
                'direccion' => $r->direccion,
                'tipo'      => $r->tipo,
            ]);

        return redirect()->route('admin.index')->with('ok', 'Restaurante actualizado.');
    }

    public function eliminarRestaurante($id)
    {
        DB::table('restaurantes')->where('id', $id)->delete();
        return back()->with('ok', 'Restaurante eliminado');
    }

    /*
       menuCrear editar eliminar
    */
    public function menusCreate()
    {
        $restaurantes = DB::table('restaurantes')->orderBy('nombre')->get();
        return view('admin.menus_form', compact('restaurantes'));
    }

    public function menusStore(Request $r)
    {
        $r->validate([
            'restaurante_id' => 'required|integer|exists:restaurantes,id',
            'nombre'         => 'required|string|max:100',
            'descripcion'    => 'nullable|string',
            'caracteristicas'=> 'nullable|string',
        ]);

        DB::table('menus')->insert([
            'restaurante_id' => (int) $r->restaurante_id,
            'nombre'         => $r->nombre,
            'descripcion'    => $r->descripcion,
            'caracteristicas'=> $r->caracteristicas,
        ]);

        return redirect()->route('admin.index')->with('ok', 'Menú creado.');
    }

    public function menusEdit($id)
    {
        $menu = DB::table('menus')->where('id', $id)->first();
        if (!$menu) abort(404);

        $restaurantes = DB::table('restaurantes')->orderBy('nombre')->get();
        return view('admin.menus_form', compact('menu','restaurantes'));
    }

    public function menusUpdate(Request $r, $id)
    {
        $r->validate([
            'restaurante_id' => 'required|integer|exists:restaurantes,id',
            'nombre'         => 'required|string|max:100',
            'descripcion'    => 'nullable|string',
            'caracteristicas'=> 'nullable|string',
        ]);

        DB::table('menus')->where('id', $id)->update([
            'restaurante_id' => (int) $r->restaurante_id,
            'nombre'         => $r->nombre,
            'descripcion'    => $r->descripcion,
            'caracteristicas'=> $r->caracteristicas,
        ]);

        return redirect()->route('admin.index')->with('ok', 'Menú actualizado.');
    }

    public function eliminarMenu($id)
    {
        DB::table('menus')->where('id', $id)->delete();
        return back()->with('ok', 'Menú eliminado');
    }

    /* 
       Editar Eliminar opiniones
    */
    public function opinionesEdit($id)
    {
        $op = DB::table('opiniones')->where('id', $id)->first();
        if (!$op) abort(404);
        return view('admin.opiniones_form', compact('op'));
    }

    public function opinionesUpdate(Request $r, $id)
    {
        $r->validate([
            'mensaje' => 'required|string',
        ]);

        DB::table('opiniones')->where('id', $id)->update([
            'mensaje' => $r->mensaje,
        ]);

        return redirect()->route('admin.index')->with('ok', 'Opinión actualizada.');
    }

    public function eliminarOpinion($id)
    {
        DB::table('opiniones')->where('id', $id)->delete();
        return back()->with('ok', 'Opinión eliminada');
    }
}
