@extends('layouts.app')
@section('body_class','bg-admin') {{-- para admin --}}

@section('content')
<div class="topbar" style="background:#333;color:#fff;padding:10px">
  <a href="{{ route('home') }}" style="color:#fff;margin-right:12px;text-decoration:none">Inicio</a>
  <a href="{{ route('admin.index') }}" style="color:#fff;margin-right:12px;text-decoration:none"> Panel Admin</a>
  <form method="POST" action="{{ route('logout') }}" style="display:inline">
    @csrf
    <button type="submit" style="background:transparent;border:0;color:#fff;cursor:pointer"> Cerrar sesión</button>
  </form>
</div>

<div class="container" style="max-width:1200px;margin:18px auto">
  @if (session('ok'))
    <div style="background:#e8fff1;border:1px solid #0a0;padding:8px;border-radius:6px;margin-bottom:10px">
      {{ session('ok') }}
    </div>
  @endif

  {{-- 
       PEDIDOS 
        --}}
  <div class="section" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:12px;margin:18px 0">
    <h2>Pedidos Realizados</h2>

    <div class="table-responsive">
      <table style="width:100%;border-collapse:collapse">
        <thead>
          <tr style="background:#444;color:#fff">
            <th style="padding:8px;border:1px solid #ccc">Cliente (email)</th>
            <th style="padding:8px;border:1px solid #ccc">Nombre / Edad</th>
            <th style="padding:8px;border:1px solid #ccc">Dirección / Envío</th>
            <th style="padding:8px;border:1px solid #ccc">Perfil</th>
            <th style="padding:8px;border:1px solid #ccc">Menú</th>
            <th style="padding:8px;border:1px solid #ccc">Restaurante</th>
            <th style="padding:8px;border:1px solid #ccc">Fecha</th>
            <th style="padding:8px;border:1px solid #ccc">Acciones</th>
          </tr>
        </thead>
        <tbody>
        @forelse ($ped as $p)
          <tr>
            <td style="padding:8px;border:1px solid #ccc">{{ $p->usuario_email }}</td>
            <td style="padding:8px;border:1px solid #ccc">
              {{ $p->nombre_form ?: '—' }}{{ $p->edad_form ? ' / '.$p->edad_form : '' }}
            </td>
            <td style="padding:8px;border:1px solid #ccc">
              {{ $p->direccion_form ?: '—' }}{{ $p->envio_form ? ' / '.$p->envio_form : '' }}
            </td>
            <td style="padding:8px;border:1px solid #ccc">
              {{ trim($p->enfermedad_form ?: 'sin condiciones') }} ·
              {{ $p->preferencia_form ?: 'sin restr.' }} ·
              {{ $p->alimento_form ?: 'cualquiera' }}
            </td>
            <td style="padding:8px;border:1px solid #ccc">
              <strong>{{ $p->menu_nombre }}</strong><br>
              <small>{{ $p->descripcion }}</small>
            </td>
            <td style="padding:8px;border:1px solid #ccc">
              {{ $p->restaurante_nombre }}<br>
              <small>{{ $p->restaurante_direccion }}</small>
            </td>
            <td style="padding:8px;border:1px solid #ccc">{{ $p->fecha }}</td>
            <td style="padding:8px;border:1px solid #ccc;white-space:nowrap">
              <a href="{{ route('boleta', $p->id) }}" style="background:#8D5AF2;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">Boleta</a>
              <a href="{{ route('admin.pedido.edit',$p->id) }}" style="background:#78F25A;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none"> Editar</a>
              <form method="POST" action="{{ route('admin.pedido.eliminar',$p->id) }}" style="display:inline" onsubmit="return confirm('¿Eliminar pedido?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:red;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Eliminar</button>

              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="padding:8px;border:1px solid #ccc">Sin pedidos</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- 
       parte de crud para restaurantes
       --}}
  <div class="section" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:12px;margin:18px 0">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h2 style="margin:0"> Restaurantes</h2>
      <a href="{{ route('admin.rest.create') }}" style="background:#0a7bff;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">Crear restaurante</a>
    </div>

    <table style="width:100%;border-collapse:collapse;margin-top:8px">
      <thead>
        <tr style="background:#444;color:#fff">
          <th style="padding:8px;border:1px solid #ccc">#</th>
          <th style="padding:8px;border:1px solid #ccc">Nombre</th>
          <th style="padding:8px;border:1px solid #ccc">Dirección</th>
          <th style="padding:8px;border:1px solid #ccc">Tipo</th>
          <th style="padding:8px;border:1px solid #ccc">Acciones</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($rest as $r)
        <tr>
          <td style="padding:8px;border:1px solid #ccc">{{ $r->id }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $r->nombre }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $r->direccion }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $r->tipo }}</td>
          <td style="padding:8px;border:1px solid #ccc;white-space:nowrap">
            <a href="{{ route('admin.rest.edit',$r->id) }}" style="background:#78F25A;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none"> Editar</a>
            <form method="POST" action="{{ route('admin.rest.eliminar',$r->id) }}" style="display:inline" onsubmit="return confirm('¿Eliminar restaurante?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:red;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Eliminar</button>

            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{-- 
       MENÚS (CRUD)
        --}}
  <div class="section" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:12px;margin:18px 0">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h2 style="margin:0">Menús</h2>
      <a href="{{ route('admin.menu.create') }}" style="background:#0a7bff;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">Crear menú</a>
    </div>

    <table style="width:100%;border-collapse:collapse;margin-top:8px">
      <thead>
        <tr style="background:#444;color:#fff">
          <th style="padding:8px;border:1px solid #ccc">#</th>
          <th style="padding:8px;border:1px solid #ccc">Nombre</th>
          <th style="padding:8px;border:1px solid #ccc">Restaurante</th>
          <th style="padding:8px;border:1px solid #ccc">Características</th>
          <th style="padding:8px;border:1px solid #ccc">Acciones</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($men as $m)
        <tr>
          <td style="padding:8px;border:1px solid #ccc">{{ $m->id }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $m->nombre }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $m->rest_nombre }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $m->caracteristicas }}</td>
          <td style="padding:8px;border:1px solid #ccc;white-space:nowrap">
            <a href="{{ route('admin.menu.edit',$m->id) }}" style="background:#78F25A;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">Editar</a>
            <form method="POST" action="{{ route('admin.menu.eliminar',$m->id) }}" style="display:inline" onsubmit="return confirm('¿Eliminar menú?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:red;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Eliminar</button>


            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>


{{-- INGREDIENTES (CRUD) --}}
<div class="section" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:12px;margin:18px 0">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2 style="margin:0">Ingredientes</h2>
    <div>
      <a href="{{ route('admin.ingredientes.index') }}" style="background:#6c757d;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;margin-right:6px">
        Ver todos
      </a>
      <a href="{{ route('admin.ingredientes.create') }}" style="background:#0a7bff;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">
        Crear ingrediente
      </a>
    </div>
  </div>

  <table style="width:100%;border-collapse:collapse;margin-top:8px">
    <thead>
      <tr style="background:#444;color:#fff">
        <th style="padding:8px;border:1px solid #ccc">#</th>
        <th style="padding:8px;border:1px solid #ccc">Nombre</th>
        <th style="padding:8px;border:1px solid #ccc">Categoría</th>
        <th style="padding:8px;border:1px solid #ccc" class="text-right">kcal</th>
        <th style="padding:8px;border:1px solid #ccc" class="text-right">Prot (g)</th>
        <th style="padding:8px;border:1px solid #ccc" class="text-right">Grasa (g)</th>
        <th style="padding:8px;border:1px solid #ccc" class="text-right">Carbo (g)</th>
        <th style="padding:8px;border:1px solid #ccc" class="text-right">Azúcar (g)</th>
        <th style="padding:8px;border:1px solid #ccc" class="text-right">Sodio (mg)</th>
        {{-- <th>Flags</th>  <-- quitado --}}
        <th style="padding:8px;border:1px solid #ccc">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($ingrs as $ing)
        <tr>
          <td style="padding:8px;border:1px solid #ccc">{{ $ing->id }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $ing->nombre }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $ing->categoria }}</td>
          <td style="padding:8px;border:1px solid #ccc;text-align:right">{{ $ing->calorias }}</td>
          <td style="padding:8px;border:1px solid #ccc;text-align:right">{{ rtrim(rtrim(number_format($ing->proteina,2,'.',''),'0'),'.') }}</td>
          <td style="padding:8px;border:1px solid #ccc;text-align:right">{{ rtrim(rtrim(number_format($ing->grasa,2,'.',''),'0'),'.') }}</td>
          <td style="padding:8px;border:1px solid #ccc;text-align:right">{{ rtrim(rtrim(number_format($ing->carbo,2,'.',''),'0'),'.') }}</td>
          <td style="padding:8px;border:1px solid #ccc;text-align:right">{{ rtrim(rtrim(number_format($ing->azucar,2,'.',''),'0'),'.') }}</td>
          <td style="padding:8px;border:1px solid #ccc;text-align:right">{{ $ing->sodio_mg }}</td>

          {{-- Flags eliminados --}}

          <td style="padding:8px;border:1px solid #ccc;white-space:nowrap">
            <a href="{{ route('admin.ingredientes.edit',$ing->id) }}" style="background:#78F25A;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">Editar</a>
            <form method="POST" action="{{ route('admin.ingredientes.destroy',$ing->id) }}" style="display:inline" onsubmit="return confirm('¿Eliminar ingrediente?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:red;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Eliminar</button>
            </form>
          </td>
        </tr>
      @empty
        {{-- Ajusta el colspan a 10 porque quitamos una columna --}}
        <tr><td colspan="10" style="padding:8px;border:1px solid #ccc">No hay ingredientes.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>



  
  {{-- 
       opiniones(CRUD)
       --}}
  <div class="section" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:12px;margin:18px 0">
    <h2>💬 Opiniones</h2>
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="background:#444;color:#fff">
          <th style="padding:8px;border:1px solid #ccc">#</th>
          <th style="padding:8px;border:1px solid #ccc">Usuario</th>
          <th style="padding:8px;border:1px solid #ccc">Mensaje</th>
          <th style="padding:8px;border:1px solid #ccc">Fecha</th>
          <th style="padding:8px;border:1px solid #ccc">Acciones</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($ops as $o)
        <tr>
          <td style="padding:8px;border:1px solid #ccc">{{ $o->id }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $o->usuario_email ?? '—' }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $o->mensaje }}</td>
          <td style="padding:8px;border:1px solid #ccc">{{ $o->fecha }}</td>
          <td style="padding:8px;border:1px solid #ccc;white-space:nowrap">
            <a href="{{ route('admin.opinion.edit',$o->id) }}" style="background:#78F25A;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none">Editar</a>
            <form method="POST" action="{{ route('admin.opinion.eliminar',$o->id) }}" style="display:inline" onsubmit="return confirm('¿Eliminar opinión?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:red;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Eliminar</button>

            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
