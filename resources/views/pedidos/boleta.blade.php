@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
  <div class="card-body">
    <h2 class="mb-1">Boleta de Pedido #{{ $p->id }}</h2>
    <div class="text-muted mb-3">{{ $p->fecha }}</div>

    {{-- Cliente --}}
    <h5 class="mt-3">Cliente</h5>
    <dl class="row small">
      <dt class="col-sm-3">Nombre</dt>
      <dd class="col-sm-9">{{ $p->cliente_nombre ?? $p->email }}</dd>

      <dt class="col-sm-3">Email</dt>
      <dd class="col-sm-9">{{ $p->email }}</dd>

      <dt class="col-sm-3">Nombre/Edad (form)</dt>
      <dd class="col-sm-9">
        {{ $p->nombre_form ?? '—' }}
        @if(!empty($p->edad_form)) / {{ $p->edad_form }} @endif
      </dd>

      <dt class="col-sm-3">Dirección / Envío</dt>
      <dd class="col-sm-9">
        {{ $p->direccion_form ?? '—' }}
        @if(!empty($p->envio_form)) / {{ $p->envio_form }} @endif
      </dd>

      <dt class="col-sm-3">Perfil</dt>
      <dd class="col-sm-9">
        {{ $p->enfermedad_form ?? 'sin condiciones' }}
        · {{ $p->preferencia_form ?? 'sr' }}
        · {{ $p->alimento_form ?? 'cualquiera' }}
      </dd>
    </dl>

    {{-- Restaurante --}}
    <h5 class="mt-4">Restaurante</h5>
    <dl class="row small">
      <dt class="col-sm-3">Nombre</dt>
      <dd class="col-sm-9">{{ $p->restaurante_nombre ?? '—' }}</dd>

      <dt class="col-sm-3">Dirección</dt>
      <dd class="col-sm-9">{{ $p->restaurante_direccion ?? '—' }}</dd>
    </dl>

    {{-- Menú --}}
    <h5 class="mt-4">Menú</h5>
    <dl class="row small">
      <dt class="col-sm-3">Nombre</dt>
      <dd class="col-sm-9"><strong>{{ $p->menu_nombre }}</strong></dd>

      @if(!empty($p->descripcion))
        <dt class="col-sm-3">Descripción</dt>
        <dd class="col-sm-9">{{ $p->descripcion }}</dd>
      @endif

      @if(!empty($p->caracteristicas))
        <dt class="col-sm-3">Características</dt>
        <dd class="col-sm-9">{{ $p->caracteristicas }}</dd>
      @endif

      {{-- Ingredientes reales del menú --}}
      @if(!empty($p->ing_lista))
        <dt class="col-sm-3">Ingredientes</dt>
        <dd class="col-sm-9">
          <em>({{ (int)($p->ing_count ?? 0) }})</em>
          {{ $p->ing_lista }}
        </dd>
      @endif
    </dl>

    <div class="mt-4">
      <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
        Imprimir Boleta
      </button>
      
    </div>
  </div>
</div>
@endsection

