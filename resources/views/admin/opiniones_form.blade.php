@extends('layouts.app')

@section('content')
<h2>Editar opinión #{{ $op->id }}</h2>
<form method="POST" action="{{ route('admin.opinion.update',$op->id) }}">
  @csrf @method('PUT')
  <label>Mensaje</label>
  <textarea name="mensaje" required>{{ $op->mensaje }}</textarea>
  <button type="submit">Guardar</button>
  <a href="{{ route('admin.index') }}">Cancelar</a>
</form>
@endsection
