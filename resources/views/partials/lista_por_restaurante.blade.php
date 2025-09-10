{{-- resources/views/partials/lista_por_restaurante.blade.php --}}
@php
  /** @var \Illuminate\Support\Collection|array $lista */
  $items = collect($lista ?? []);
  $grouped = $items->groupBy('restaurante_nombre');

  // URLs de imágenes genéricas (cámbialas si subes tus propias fotos)
  $imgRest = asset('images/restaurante.jpg');
  $imgMenu = asset('images/menu.jpg');
@endphp

@if ($grouped->isEmpty())
  <p>No hay menús para esos criterios por ahora.</p>
@else
  <style>
    .rest-card{border:1px solid #e9e9e9;border-radius:12px;margin:16px 0;overflow:hidden;background:#fff}
    .rest-header{display:flex;gap:14px;align-items:center;padding:12px 14px;background:#fafafa;border-bottom:1px solid #eee}
    .rest-photo{width:120px;height:80px;object-fit:cover;border-radius:10px}
    .rest-meta{font-size:.9rem;color:#666}
    .rest-title{margin:.2rem 0 .1rem;font-size:1.1rem}
    .menu-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;padding:12px}
    .menu-item{display:grid;grid-template-columns:90px 1fr;gap:10px;border:1px solid #eee;border-radius:10px;padding:10px}
    .menu-photo{width:90px;height:90px;object-fit:cover;border-radius:8px}
    .menu-title{margin:.1rem 0 .2rem;font-size:1rem}
    .muted{color:#6b7280}
    .maps-link{font-size:.9rem}
    .buy-form{margin-top:6px}
  </style>

  @foreach ($grouped as $restName => $menus)
    @php $first = $menus->first(); @endphp
    <div class="rest-card">
      <div class="rest-header">
        <img class="rest-photo"
             src="{{ $imgRest }}"
             alt="Restaurante"
             onerror="this.src='https://placehold.co/800x260?text=Restaurante'">
        <div>
          <div class="rest-title"><strong>{{ $restName }}</strong></div>
          <div class="rest-meta">
            {{ $first->direccion ?? 'N/A' }}
            @if(!empty($first->direccion) && $first->direccion !== 'N/A')
              · <a class="maps-link" target="_blank"
                   href="https://www.google.com/maps/search/?api=1&query={{ urlencode($first->direccion) }}">
                   Ver en Google Maps
                 </a>
            @endif
          </div>
        </div>
      </div>

      <div class="menu-list">
        @foreach ($menus as $m)
          <div class="menu-item">
            <img class="menu-photo"
                 src="{{ $imgMenu }}"
                 alt="{{ $m->menu_nombre }}"
                 onerror="this.src='https://placehold.co/300x300?text=Men%C3%BA'">
            <div>
              <div class="menu-title"><strong>{{ $m->menu_nombre }}</strong></div>
              <div>{{ $m->descripcion }}</div>
              <div class="muted"><strong>Características:</strong> {{ $m->caracteristicas }}</div>
              <div class="muted">
                <strong>Ingredientes{{ isset($m->n_ings) ? ' ('.(int)$m->n_ings.')' : '' }}:</strong>
                {{ $m->lista_ings ?: '—' }}
              </div>
              <form class="buy-form" action="{{ route('pedido.comprar') }}" method="POST">
                @csrf
                <input type="hidden" name="menu_id" value="{{ (int)$m->id }}">
                <button type="submit">Comprar este menú</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endforeach
@endif
