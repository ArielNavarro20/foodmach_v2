<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Opinion;

class OpinionController extends Controller
{
    // listar opiniones
    //  admin todas las opiniones
    //  cliente solo su última q el pidio
    public function index()
    {
        $u = Auth::user();

        if ($u && $u->rol === 'admin') {
            $opiniones = DB::table('opiniones AS o')
                ->leftJoin('usuarios AS u', 'u.id', '=', 'o.usuario_id')
                ->select('o.*', 'u.email AS usuario_email')
                ->orderByDesc('o.fecha')
                ->get();
        } else {
            $opiniones = DB::table('opiniones AS o')
                ->leftJoin('usuarios AS u', 'u.id', '=', 'o.usuario_id')
                ->select('o.*', 'u.email AS usuario_email')
                ->where('o.usuario_id', Auth::id())
                ->orderByDesc('o.fecha')
                ->limit(1)
                ->get();
        }

        return view('opiniones.index', compact('opiniones', 'u'));
    }

    // formulario para crear opinión 
    public function form()
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }
        return view('opiniones.form');
    }

    // Guardar la opinión
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $data = $request->validate([
            'mensaje' => ['required','string','max:5000'],
        ]);

        Opinion::create([
            'usuario_id' => Auth::id(),
            'mensaje'    => $data['mensaje'],
        ]);

        return redirect()
            ->route('opiniones.index')
            ->with('msg', '¡Gracias por tu opinión!');
    }
}
