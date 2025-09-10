@extends('layouts.app')

@section('content')
<div class="container-narrow">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        {{ ($u && $u->rol==='admin') ? 'Pedidos Realizados' : 'Mis Pedidos' }}
      </h2>
      <span class="card-sub">{{ count($pedidos) }} pedido(s)</span>
    </div>

    <div class="card-body">
      @if(count($pedidos))
        <div class="table-wrap">
          <table class="table-lined">
            <thead>
              <tr>
                <th>Cliente (email)</th>
                <th>Nombre / Edad</th>
                <th>Dirección / Envío</th>
                <th>Perfil</th>
                <th>Menú</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pedidos as $p)
                <tr>
                  <td>{{ $p->usuario_email }}</td>

                  <td>
                    {{ $p->nombre_form ?? '—' }}
                    @if(!empty($p->edad_form))
                      / {{ $p->edad_form }}
                    @endif
                  </td>

                  <td>
                    {{ $p->direccion_form ?? '—' }}
                    @if(!empty($p->envio_form))
                      / {{ $p->envio_form }}
                    @endif
                  </td>

                  <td>
                    {{ $p->enfermedad_form ?? 'sin condiciones' }}
                    · {{ $p->preferencia_form ?? 'sr' }}
                    · {{ $p->alimento_form ?? 'cualquiera' }}
                  </td>

                  <td>
                    <strong>{{ $p->menu_nombre }}</strong>
                    @if(!empty($p->descripcion))
                      <small class="muted">{{ $p->descripcion }}</small>
                    @endif

                    {{-- Ingredientes reales del menú --}}
                    @if(!empty($p->ing_lista))
                      <div class="small" style="margin-top:4px">
                        <em>Ingredientes ({{ (int)($p->ing_count ?? 0) }}):</em>
                        {{ $p->ing_lista }}
                      </div>
                    @endif
                  </td>

                  <td>{{ $p->fecha }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="muted">No hay pedidos.</p>
      @endif
    </div>
  </div>
</div>
@endsection
