@extends('layouts.app')

@section('content')
<h2>{{ isset($menu)?'Editar':'Crear' }} menú</h2>
<form method="POST" action="{{ isset($menu)?route('admin.menu.update',$menu->id):route('admin.menu.store') }}">
  @csrf
  @if(isset($menu)) @method('PUT') @endif

  <label>Restaurante</label>
  <select name="restaurante_id" required>
    @foreach($restaurantes as $r)
      <option value="{{ $r->id }}" {{ isset($menu)&&$menu->restaurante_id==$r->id?'selected':'' }}>{{ $r->nombre }}</option>
    @endforeach
  </select>

  <label>Nombre</label><input name="nombre" value="{{ $menu->nombre ?? '' }}" required>
  <label>Descripción</label><textarea name="descripcion">{{ $menu->descripcion ?? '' }}</textarea>
  <label>Características</label><textarea name="caracteristicas">{{ $menu->caracteristicas ?? '' }}</textarea>

  <button type="submit">{{ isset($menu)?'Actualizar':'Crear' }}</button>
  <a href="{{ route('admin.index') }}">Cancelar</a>
</form>
@endsection
