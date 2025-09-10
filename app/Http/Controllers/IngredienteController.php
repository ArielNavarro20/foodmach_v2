<?php

namespace App\Http\Controllers;

use App\Models\Ingrediente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IngredienteController extends Controller
{
    /** Listado yfiltros simples */
    public function index(Request $r)
    {
        $q = Ingrediente::query();

        if ($r->filled('q')) {
            $q->where('nombre', 'like', '%'.$r->q.'%');
        }
        if ($r->filled('categoria') && $r->categoria !== 'todas') {
            $q->where('categoria', $r->categoria);
        }

        $ings = $q->orderBy('nombre')->paginate(15)->withQueryString();

        // categorías  para el filtro tomando la db
        $categorias = Ingrediente::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');

        return view('admin.ingredientes.index', compact('ings','categorias'));
    }

    /** Form para crear */
    public function create()
    {
        $ing = new Ingrediente();
        return view('admin.ingredientes.create', compact('ing'));
    }

    /** Guardarlo */
    public function store(Request $r)
    {
        $data = $this->validateData($r);

        Ingrediente::create($data);

        return redirect()
            ->route('admin.ingredientes.index')
            ->with('ok', 'Ingrediente creado con éxito.');
    }

    /** Form editar */
    public function edit(Ingrediente $ingrediente)
    {
        $ing = $ingrediente;
        return view('admin.ingredientes.edit', compact('ing'));
    }

    /** Actualizarlo */
    public function update(Request $r, Ingrediente $ingrediente)
    {
        $data = $this->validateData($r, $ingrediente->id);

        $ingrediente->update($data);

        return redirect()
            ->route('admin.ingredientes.index')
            ->with('ok', 'Ingrediente actualizado.');
    }

    /** Eliminarlo */
    public function destroy(Ingrediente $ingrediente)
    {
        $ingrediente->delete();

        return redirect()
            ->route('admin.ingredientes.index')
            ->with('ok', 'Ingrediente eliminado.');
    }

    /** las reglas de validacion */
    private function validateData(Request $r, ?int $id = null): array
    {
        return $r->validate([
            'nombre'    => ['required','string','max:120', Rule::unique('ingredientes','nombre')->ignore($id)],
            'categoria' => ['required','string','max:40'],

            'calorias'  => ['required','integer','min:0'],
            'proteina'  => ['required','numeric','min:0'],
            'grasa'     => ['required','numeric','min:0'],
            'carbo'     => ['required','numeric','min:0'],
            'azucar'    => ['required','numeric','min:0'],
            'sodio_mg'  => ['required','integer','min:0'],

            'es_gluten'  => ['sometimes','boolean'],
            'es_lactosa' => ['sometimes','boolean'],
            'es_animal'  => ['sometimes','boolean'],
        ], [], [
            'sodio_mg' => 'sodio (mg)',
        ]) + [
            // checkboxes no marcados  0
            'es_gluten'  => $r->boolean('es_gluten'),
            'es_lactosa' => $r->boolean('es_lactosa'),
            'es_animal'  => $r->boolean('es_animal'),
        ];
    }
}
