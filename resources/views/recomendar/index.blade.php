@extends('layouts.app')

@section('title', 'Recomendaciones')

@section('content')
  <h2>Recomendaciones para tu selección </h2>

  @if (!empty($tags))
    <p class="muted">Criterios:
      <strong>{{ implode(' · ', $tags) }}</strong>
    </p>
  @endif

  {{-- Sugeridos por tu última compra (agrupados por restaurante) --}}
  @if(isset($recomendados) && $recomendados->count())
    <hr>
    <div class="recs-verdoso">
    <h3>Sugeridos por tu última compra</h3>
    @include('partials.lista_por_restaurante', ['lista' => $recomendados])
    </div>
  @endif

  {{-- Otros compatibles (agrupados por restaurante) --}}
  <hr>
  <div class="recs-amarillo">
  <h3>Otros menús compatibles</h3>
  @include('partials.lista_por_restaurante', ['lista' => $otros])
  </div>

  <p style="margin-top:12px">
    <a href="{{ route('home') }}">Volver al inicio</a>
  </p>
@endsection
