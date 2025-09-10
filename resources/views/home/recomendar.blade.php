@extends('layouts.app')

@section('title', 'Recomendaciones')

@section('content')
<div class="container">
  <h2>Recomendaciones para tu selección </h2>

  @if (!empty($tags))
    <p class="text-muted">Criterios: <strong>{{ implode(' · ', $tags) }}</strong></p>
  @endif

  @if (!empty($recomendados))
    <h3>⭐ Menús recomendados</h3>
    <div class="row row-cols-1 row-cols-md-2 g-3">
      @foreach ($recomendados as $m)
        <div class="col">
          <div class="card h-100 p-3">
            <h5>{{ $m->menu_nombre }}</h5>
            <p>{{ $m->descripcion }}</p>
            <small class="text-muted">Características: {{ $m->caracteristicas }}</small><br>
            <small class="text-muted">Restaurante: {{ $m->restaurante_nombre }} — {{ $m->direccion }}</small>
            <form action="{{ route('pedido.comprar') }}" method="POST" class="mt-2">
              @csrf
              <input type="hidden" name="menu_id" value="{{ (int)($m->id ?? 0) }}">
              <button class="btn btn-primary btn-sm" type="submit">Comprar este menú</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <p>No encontramos recomendaciones exactas. Revisa los menús compatibles abajo.</p>
  @endif

  @if (!empty($segunCompra))
    <hr>
    <h3> Recomendados según tu compra anterior</h3>
    {{-- Listado similar al anterior --}}
  @endif

  <hr>

  <h3>Otros menús compatibles</h3>
  @if (!empty($otros))
    <ul>
      @foreach ($otros as $m)
        <li>{{ $m->menu_nombre }} — {{ $m->restaurante_nombre }}</li>
      @endforeach
    </ul>
  @else
    <p>No hay más menús con esos criterios.</p>
  @endif

  <a class="btn btn-link mt-3" href="{{ route('home') }}">Volver al inicio</a>
</div>
@endsection
