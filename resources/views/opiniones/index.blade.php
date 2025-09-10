@extends('layouts.app')

@section('content')
<h2>Opiniones</h2>

@if(session('msg'))
  <div class="flash">{{ session('msg') }}</div>
@endif

@if($opiniones->count() === 0)
  <p>No hay opiniones para mostrar.</p>
@else
  <div class="grid" style="display:grid;gap:12px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
    @foreach($opiniones as $op)
      <div class="card" style="background:#fff;border:1px solid #ddd;padding:12px;border-radius:8px;">
        <p style="margin:0 0 6px 0"><strong>{{ $op->usuario_email ?? '—' }}</strong></p>
        <p style="white-space:pre-wrap">{{ $op->mensaje }}</p>
        <p class="muted" style="margin-top:6px"><small>{{ $op->fecha }}</small></p>
      </div>
    @endforeach
  </div>
@endif

@auth
  <p style="margin-top:14px">
    <a href="{{ route('opiniones.form') }}">Dejar una opinión</a>
  </p>
@endauth
@endsection
