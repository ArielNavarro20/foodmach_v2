<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanSemanalController extends Controller
{
    /**
     * Genera 5 menús compatibles conn LIKE en m.caracteristicas con la seison pref guardada
     */
    public function ver(Request $r)
    {
        
        $enf   = session('pref.enfermedad');   // celiaco, hipertenso, diabetico, intolerante_lactosa, cardiaco, sin condiciones
        $pref  = session('pref.preferencia');  // vegano, vegetariano, sin restricciones
        $alim  = session('pref.alimento');     // alto en proteínas, bajo en grasa/sodio/azúcar..., cualquiera

        [$where, $bind] = $this->buildWhere($enf, $pref, $alim);

        $sql = "
            SELECT m.id, m.nombre, m.descripcion, m.caracteristicas,
                   r.nombre AS restaurante
            FROM menus m
            JOIN restaurantes r ON r.id = m.restaurante_id
            WHERE 1=1 $where
            ORDER BY RAND()
            LIMIT 5
        ";
        $menus = DB::select($sql, $bind);

        // esto para rellenar si noalcanza los 5
        if (count($menus) < 5) {
            $faltan = 5 - count($menus);
            $extra = DB::select("
                SELECT m.id, m.nombre, m.descripcion, m.caracteristicas,
                       r.nombre AS restaurante
                FROM menus m
                JOIN restaurantes r ON r.id = m.restaurante_id
                ORDER BY RAND()
                LIMIT $faltan
            ");
            $menus = array_merge($menus, $extra);
        }

        $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes'];
        $plan = [];
        foreach ($dias as $i => $d) {
            $menu = $menus[$i] ?? null;
            if ($menu) {
                $plan[] = [
                    'dia'          => $d,
                    'nombre'       => $menu->nombre,
                    'restaurante'  => $menu->restaurante,
                    'caracts'      => $menu->caracteristicas,
                    'descripcion'  => $menu->descripcion,
                ];
            }
        }

        return view('plan.semanal', [
            'plan' => $plan,
            'nota_envio' => 'Podemos coordinar envíos a tu lugar de trabajo u otra dirección que indiques.',
        ]);
    }

    /** Versión para imprimir como PDF  */
    public function pdf(Request $r)
    {
        // Reusar la lógica
        return $this->ver($r)->with(['for_print' => true]);
    }

    private function buildWhere(?string $enf, ?string $pref, ?string $alim): array
    {
        $w = [];
        $b = [];

        // Enfermedad y condición
        if ($enf) {
            $enf = strtolower($enf);
            if (str_contains($enf, 'celiac')) {
                $w[] = "(m.caracteristicas LIKE ? OR m.caracteristicas LIKE ?)";
                $b[] = '%sin gluten%';
                $b[] = '%celiac%';
            } elseif (str_contains($enf, 'lactosa')) {
                $w[] = "m.caracteristicas LIKE ?";
                $b[] = '%sin lactosa%';
            } elseif (str_contains($enf, 'hipertenso')) {
                $w[] = "m.caracteristicas LIKE ?";
                $b[] = '%bajo en sodio%';
            } elseif (str_contains($enf, 'diabet')) {
                $w[] = "m.caracteristicas LIKE ?";
                $b[] = '%bajo en azúcar%';
            } elseif (str_contains($enf, 'cardiaco')) {
                $w[] = "m.caracteristicas LIKE ?";
                $b[] = '%bajo en grasa%';
            }
        }

        // Preferencia
        if ($pref) {
            $pref = strtolower($pref);
            if (str_contains($pref, 'vegano')) {
                $w[] = "m.caracteristicas LIKE ?";
                $b[] = '%vegano%';
            } elseif (str_contains($pref, 'vegetar')) {
                $w[] = "m.caracteristicas LIKE ?";
                $b[] = '%vegetar%';
            }
        }

        // Tipo de alimento deseado
        if ($alim && !str_contains(strtolower($alim), 'cualquier') && !str_contains(strtolower($alim), 'no espec')) {
            $w[] = "m.caracteristicas LIKE ?";
            $b[] = '%'.strtolower($alim).'%';
        }

        $where = $w ? ' AND '.implode(' AND ', $w) : '';
        return [$where, $b];
    }
}
