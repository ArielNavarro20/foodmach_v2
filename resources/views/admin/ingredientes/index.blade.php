@extends('layouts.app')

@section('content')
<h2>Ingredientes</h2>

@if(session('ok'))
  <div class="alert alert-success">{{ session('ok') }}</div>
@endif

<div class="mb-3 flex-wrap" style="display:flex; gap:.75rem; align-items:center;">
  <form method="GET" action="{{ route('admin.ingredientes.index') }}" class="form-inline" style="display:flex; gap:.5rem; flex-wrap:wrap;">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Buscar por nombre">
    <select name="categoria" class="form-control form-control-sm">
      <option value="todas">— Todas las categorías —</option>
      @foreach($categorias as $cat)
        <option value="{{ $cat }}" @selected(request('categoria')===$cat)>{{ $cat }}</option>
      @endforeach
    </select>
    <button class="btn btn-sm btn-secondary">Filtrar</button>
    <a href="{{ route('admin.ingredientes.index') }}" class="btn btn-sm btn-light">Limpiar</a>
  </form>

  <a href="{{ route('admin.ingredientes.create') }}" class="btn btn-sm btn-primary" style="margin-left:auto;">
    + Crear ingrediente
  </a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped table-sm mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Categoría</th>
          <th class="text-right">kcal</th>
          <th class="text-right">Prot (g)</th>
          <th class="text-right">Grasa (g)</th>
          <th class="text-right">Carbo (g)</th>
          <th class="text-right">Azúcar (g)</th>
          <th class="text-right">Sodio (mg)</th>
          <th>Flags</th>
          <th class="text-center" style="width:150px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($ings as $ing)
          <tr>
            <td>{{ $ing->nombre }}</td>
            <td>{{ $ing->categoria }}</td>
            <td class="text-right">{{ $ing->calorias }}</td>
            <td class="text-right">{{ rtrim(rtrim(number_format($ing->proteina,2,'.',''), '0'),'.') }}</td>
            <td class="text-right">{{ rtrim(rtrim(number_format($ing->grasa,2,'.',''), '0'),'.') }}</td>
            <td class="text-right">{{ rtrim(rtrim(number_format($ing->carbo,2,'.',''), '0'),'.') }}</td>
            <td class="text-right">{{ rtrim(rtrim(number_format($ing->azucar,2,'.',''), '0'),'.') }}</td>
            <td class="text-right">{{ $ing->sodio_mg }}</td>
            <td>
              <span class="badge {{ $ing->es_gluten  ? 'badge-danger'  : 'badge-success' }}">{{ $ing->es_gluten?'gluten':'sin gluten' }}</span>
              <span class="badge {{ $ing->es_lactosa ? 'badge-danger'  : 'badge-success' }}">{{ $ing->es_lactosa?'lactosa':'sin lactosa' }}</span>
              <span class="badge {{ $ing->es_animal  ? 'badge-warning' : 'badge-info'   }}">{{ $ing->es_animal?'animal':'veg-friendly' }}</span>
            </td>
            <td class="text-center">
              <a href="{{ route('admin.ingredientes.edit',$ing) }}" class="btn btn-sm btn-warning">Editar</a>
              <form action="{{ route('admin.ingredientes.destroy',$ing) }}" method="POST" style="display:inline-block"
                    onsubmit="return confirm('¿Eliminar ingrediente {{ $ing->nombre }}?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="10">Sin resultados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>


@endsection
