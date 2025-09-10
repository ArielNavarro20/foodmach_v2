@extends('layouts.app')

@section('content')
<h2>{{ isset($rest)?'Editar':'Crear' }} restaurante</h2>
<form method="POST" action="{{ isset($rest)?route('admin.rest.update',$rest->id):route('admin.rest.store') }}">
  @csrf
  @if(isset($rest)) @method('PUT') @endif
  <label>Nombre</label><input name="nombre" value="{{ $rest->nombre ?? '' }}" required>
  <label>Dirección</label><input name="direccion" value="{{ $rest->direccion ?? '' }}" required>
  <label>Tipo</label><input name="tipo" value="{{ $rest->tipo ?? '' }}" required>
  <button type="submit">{{ isset($rest)?'Actualizar':'Crear' }}</button>
  <a href="{{ route('admin.index') }}">Cancelar</a>
</form>
@endsection
