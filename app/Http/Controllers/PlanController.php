<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function ver(Request $request)
    {
        [$plan, $tags, $totales] = $this->armarPlan();
        $nota = 'Se puede hacer envío a tu lugar de trabajo (oficina o donde quieras).';

        return view('plan.semanal', compact('plan','tags','totales','nota'));
    }

    public function imprimir(Request $request)
    {
        [$plan, $tags, $totales] = $this->armarPlan();
        $nota = 'Se puede hacer envío a tu lugar de trabajo (oficina o donde quieras).';
        $print = true;

        return view('plan.semanal', compact('plan','tags','totales','nota','print'));
    }

    /** para el plan semanal de lunes a viernes con los filtros de reglas y la lista q se m,uestra */
    private function armarPlan()
    {
        $pref = session('pref', []);
        $tags = [];

        // Tags informativos q se ven arriba
        if (!empty($pref['preferencia'])) {
            if ($pref['preferencia'] === 'vegano')      $tags[] = 'vegano';
            if ($pref['preferencia'] === 'vegetariano') $tags[] = 'vegetariano';
        }
        if (!empty($pref['enfermedad'])) {
            switch ($pref['enfermedad']) {
                case 'celiaco':             $tags[] = 'sin gluten';    break;
                case 'intolerante_lactosa': $tags[] = 'sin lactosa';   break;
                case 'hipertenso':          $tags[] = 'bajo en sodio'; break;
                case 'diabetico':           $tags[] = 'bajo en azúcar';break;
                case 'cardiaco':            $tags[] = 'bajo en grasa'; break;
            }
        }

        // SELECT base con totales nutricionales y lista contador de ingredientes
        $select = "
            m.id,
            m.nombre      AS menu_nombre,
            m.descripcion AS menu_desc,
            m.caracteristicas,
            r.nombre      AS restaurante_nombre,

            COALESCE(v.kcal,0)     AS kcal,
            COALESCE(v.prot_g,0)   AS prot_g,
            COALESCE(v.grasa_g,0)  AS grasa_g,
            COALESCE(v.carb_g,0)   AS carb_g,
            COALESCE(v.azucar_g,0) AS azucar_g,
            COALESCE(v.sodio_mg,0) AS sodio_mg,

            COALESCE(SUM(mi.gramos),0)                                         AS base_gramos,
            COUNT(DISTINCT mi.ingrediente_id)                                  AS ing_count,
            COALESCE(GROUP_CONCAT(DISTINCT ing.nombre ORDER BY ing.nombre SEPARATOR ', '),'') AS ing_lista
        ";

        $q = DB::table('menus as m')
            ->join('restaurantes as r', 'r.id', '=', 'm.restaurante_id')
            ->leftJoin('vw_menu_nutricion as v', 'v.menu_id', '=', 'm.id')
            ->leftJoin('menu_ingrediente as mi', 'mi.menu_id', '=', 'm.id')
            ->leftJoin('ingredientes as ing', 'ing.id', '=', 'mi.ingrediente_id')   
            ->selectRaw($select);

        // Filtros blandos por etiquetas en m.caracteristicas
        foreach ($tags as $t) {
            $q->where('m.caracteristicas', 'LIKE', "%$t%");
        }

        // Reglas duras según perfil para las inconsistencias
        $preferencia = $pref['preferencia'] ?? null;
        $enfermedad  = $pref['enfermedad']  ?? null;

        // Vegetariano no debe tener ingredientes marcados como de origen animal
        if ($preferencia === 'vegetariano') {
            $q->whereNotExists(function ($sub) {
                $sub->from('menu_ingrediente as mi2')
                    ->join('ingredientes as i2', 'i2.id', '=', 'mi2.ingrediente_id')
                    ->whereColumn('mi2.menu_id', 'm.id')
                    ->where('i2.es_animal', 1);
            });
        }

        // Vegano: no animal ni lactosa
        if ($preferencia === 'vegano') {
            $q->whereNotExists(function ($sub) {
                $sub->from('menu_ingrediente as mi2')
                    ->join('ingredientes as i2', 'i2.id', '=', 'mi2.ingrediente_id')
                    ->whereColumn('mi2.menu_id', 'm.id')
                    ->where(function ($w) {
                        $w->where('i2.es_animal', 1)
                          ->orWhere('i2.es_lactosa', 1);
                    });
            });
        }

        // Celíaco: este no puede tener gluten
        if ($enfermedad === 'celiaco') {
            $q->whereNotExists(function ($sub) {
                $sub->from('menu_ingrediente as mi2')
                    ->join('ingredientes as i2', 'i2.id', '=', 'mi2.ingrediente_id')
                    ->whereColumn('mi2.menu_id', 'm.id')
                    ->where('i2.es_gluten', 1);
            });
        }

        // Candidatos aleatorios
        $candidatos = $q->groupBy('m.id')
                        ->orderByRaw('RAND()')
                        ->limit(15)
                        ->get();

        // Uno por restaurante
        $elegidos = [];
        $seenRest = [];
        foreach ($candidatos as $row) {
            if (count($elegidos) >= 5) break;
            if (isset($seenRest[$row->restaurante_nombre])) continue;
            $seenRest[$row->restaurante_nombre] = true;
            $elegidos[] = $row;
        }

        // Relleno si falta con cualquiera
        if (count($elegidos) < 5) {
            $faltan = 5 - count($elegidos);
            $fallback = DB::table('menus as m')
                ->join('restaurantes as r', 'r.id', '=', 'm.restaurante_id')
                ->leftJoin('vw_menu_nutricion as v', 'v.menu_id', '=', 'm.id')
                ->leftJoin('menu_ingrediente as mi', 'mi.menu_id', '=', 'm.id')
                ->leftJoin('ingredientes as ing', 'ing.id', '=', 'mi.ingrediente_id')
                ->selectRaw($select)
                ->groupBy('m.id')
                ->orderByRaw('RAND()')
                ->limit($faltan)
                ->get();
            foreach ($fallback as $row) $elegidos[] = $row;
        }

        // Armar semana y totales
        $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes'];
        $plan = [];
        $kcal=$p=$g=$c=$az=$na=0;

        for ($i=0; $i<5; $i++) {
            $m = $elegidos[$i] ?? null;
            if ($m) {
                $plan[] = ['dia'=>$dias[$i], 'menu'=>$m];
                $kcal += (float)$m->kcal;
                $p    += (float)$m->prot_g;
                $g    += (float)$m->grasa_g;
                $c    += (float)$m->carb_g;
                $az   += (float)$m->azucar_g;
                $na   += (float)$m->sodio_mg;
            }
        }

        $totales = [
            'kcal' => round($kcal),
            'prot' => round($p, 1),
            'grasa'=> round($g, 1),
            'carb' => round($c, 1),
            'azuc' => round($az,1),
            'sodio'=> round($na),
        ];

        return [$plan, $tags, $totales];
    }
}
