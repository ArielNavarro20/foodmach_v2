@extends('layouts.app')

@section('content')
<h2>Editar pedido #{{ $p->id }}</h2>
<form method="POST" action="{{ route('admin.pedido.update',$p->id) }}">
  @csrf @method('PUT')
  <label>Menú</label>
  <select name="menu_id" required>
    @foreach($menus as $m)
      <option value="{{ $m->id }}" {{ $m->id==$p->menu_id?'selected':'' }}>{{ $m->nombre }}</option>
    @endforeach
  </select>

  <label>Nombre</label><input name="nombre_form" value="{{ $p->nombre_form }}">
  <label>Edad</label><input type="number" name="edad_form" value="{{ $p->edad_form }}">
  <label>Dirección</label><input name="direccion_form" value="{{ $p->direccion_form }}">
  <label>Envío</label>
  <select name="envio_form">
    <option value="sí" {{ $p->envio_form==='sí'?'selected':'' }}>Sí</option>
    <option value="no" {{ $p->envio_form==='no'?'selected':'' }}>No</option>
  </select>
  <label>Enfermedad</label><input name="enfermedad_form" value="{{ $p->enfermedad_form }}">
  <label>Preferencia</label><input name="preferencia_form" value="{{ $p->preferencia_form }}">
  <label>Alimento</label><input name="alimento_form" value="{{ $p->alimento_form }}">

  <button type="submit">Guardar cambios</button>
  <a href="{{ route('admin.index') }}">Cancelar</a>
</form>
@endsection
