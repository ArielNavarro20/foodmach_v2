<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;

class PedidoController extends Controller
{
    // crear pedido desde tarjetas Comprar este menú
    public function comprar(Request $r)
    {
        $r->validate(['menu_id' => 'required|integer']);

        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $pref = [
            'nombre_form'      => session('pref.nombre'),
            'edad_form'        => session('pref.edad'),
            'direccion_form'   => session('pref.direccion'),
            'envio_form'       => session('pref.envio'),
            'enfermedad_form'  => session('pref.enfermedad'),
            'preferencia_form' => session('pref.preferencia'),
            'alimento_form'    => session('pref.alimento'),
        ];

        Pedido::create(array_merge([
            'usuario_id' => Auth::id(),
            'menu_id'    => (int) $r->menu_id,
        ], $pref));

        return redirect()->route('mis.pedidos');
    }

    // listado de pedidos para el cliente se ve su ultimo y para el admin se ven todos  con ingredientes
    public function misPedidos()
    {
        $u = Auth::user();

        // evitar que la lista se corte si hay muchos ingredientes
        DB::statement('SET SESSION group_concat_max_len = 4096');

        if ($u && $u->rol === 'admin') {
            // todos los pedidos para el admin
            $sql = "
                SELECT
                    p.id,
                    p.usuario_id,
                    p.menu_id,
                    p.fecha,
                    p.nombre_form,
                    p.edad_form,
                    p.direccion_form,
                    p.envio_form,
                    p.enfermedad_form,
                    p.preferencia_form,
                    p.alimento_form,
                    ANY_VALUE(u.email)  AS usuario_email,
                    ANY_VALUE(m.nombre) AS menu_nombre,
                    ANY_VALUE(m.descripcion) AS descripcion,
                    COUNT(DISTINCT mi.ingrediente_id) AS ing_count,
                    COALESCE(
                        GROUP_CONCAT(DISTINCT ing.nombre ORDER BY ing.nombre SEPARATOR ', '),
                        ''
                    ) AS ing_lista
                FROM pedidos p
                JOIN usuarios u        ON u.id = p.usuario_id
                JOIN menus m           ON m.id = p.menu_id
                LEFT JOIN menu_ingrediente mi ON mi.menu_id = m.id
                LEFT JOIN ingredientes ing    ON ing.id = mi.ingrediente_id
                GROUP BY p.id
                ORDER BY p.fecha DESC
            ";
            $pedidos = DB::select($sql);
        } else {
            // solo el último del usuario 
            $lastId = DB::table('pedidos')
                ->where('usuario_id', Auth::id())
                ->orderByDesc('fecha')
                ->value('id');

            if (!$lastId) {
                $pedidos = [];
            } else {
                $sql = "
                    SELECT
                        p.id,
                        p.usuario_id,
                        p.menu_id,
                        p.fecha,
                        p.nombre_form,
                        p.edad_form,
                        p.direccion_form,
                        p.envio_form,
                        p.enfermedad_form,
                        p.preferencia_form,
                        p.alimento_form,
                        ANY_VALUE(u.email)  AS usuario_email,
                        ANY_VALUE(m.nombre) AS menu_nombre,
                        ANY_VALUE(m.descripcion) AS descripcion,
                        COUNT(DISTINCT mi.ingrediente_id) AS ing_count,
                        COALESCE(
                            GROUP_CONCAT(DISTINCT ing.nombre ORDER BY ing.nombre SEPARATOR ', '),
                            ''
                        ) AS ing_lista
                    FROM pedidos p
                    JOIN usuarios u        ON u.id = p.usuario_id
                    JOIN menus m           ON m.id = p.menu_id
                    LEFT JOIN menu_ingrediente mi ON mi.menu_id = m.id
                    LEFT JOIN ingredientes ing    ON ing.id = mi.ingrediente_id
                    WHERE p.id = ?
                    GROUP BY p.id
                    LIMIT 1
                ";
                $pedidos = DB::select($sql, [$lastId]);
            }
        }

        return view('pedidos.mis', ['pedidos' => $pedidos, 'u' => $u]);
    }

    // para ver boleta de última del cliente o una específica por ID con ingredientes
    public function boleta(Request $request, $id = null)
    {
        $u = Auth::user();
        $isAdmin = $u && $u->rol === 'admin';

        // asegurar longitud suficiente para group concat
        DB::statement('SET SESSION group_concat_max_len = 4096');

        if (!$id) {
            // usar la última boleta del usuario si no se especifica id
            $id = DB::table('pedidos')
                ->where('usuario_id', Auth::id())
                ->orderByDesc('fecha')
                ->value('id');
            if (!$id) {
                abort(404, 'No se encontró la boleta.');
            }
        }

        $sql = "
            SELECT
                p.id,
                p.fecha,
                ANY_VALUE(u.nombre) AS cliente_nombre,
                ANY_VALUE(u.email)  AS email,
                p.nombre_form,
                p.edad_form,
                p.direccion_form,
                p.envio_form,
                p.enfermedad_form,
                p.preferencia_form,
                p.alimento_form,
                ANY_VALUE(m.nombre)         AS menu_nombre,
                ANY_VALUE(m.descripcion)    AS descripcion,
                ANY_VALUE(m.caracteristicas) AS caracteristicas,
                ANY_VALUE(r.nombre)         AS restaurante_nombre,
                ANY_VALUE(r.direccion)      AS restaurante_direccion,
                COUNT(DISTINCT mi.ingrediente_id) AS ing_count,
                COALESCE(
                    GROUP_CONCAT(DISTINCT ing.nombre ORDER BY ing.nombre SEPARATOR ', '),
                    ''
                ) AS ing_lista
            FROM pedidos p
            JOIN usuarios u        ON u.id = p.usuario_id
            JOIN menus m           ON m.id = p.menu_id
            JOIN restaurantes r    ON r.id = m.restaurante_id
            LEFT JOIN menu_ingrediente mi ON mi.menu_id = m.id
            LEFT JOIN ingredientes ing    ON ing.id = mi.ingrediente_id
            WHERE p.id = ?
            " . ($isAdmin ? "" : " AND p.usuario_id = ? ") . "
            GROUP BY p.id
            LIMIT 1
        ";

        $bind = $isAdmin ? [$id] : [$id, Auth::id()];
        $row  = DB::selectOne($sql, $bind);

        if (!$row) {
            abort(404, 'No se encontró la boleta.');
        }

        return view('pedidos.boleta', ['p' => $row]);
    }
}
