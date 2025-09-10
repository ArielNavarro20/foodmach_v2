@extends('layouts.app')

@section('content')
@if(!auth()->check() || auth()->user()->rol !== 'admin')

  <h2>Arma tu comida personalizada </h2>

  @if ($errors->any())
    <div style="color:#b00;margin:.5rem 0">
      @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('preferencias.guardar') }}">
    @csrf

    <label>Nombre</label>
    <input type="text" name="nombre"
           value="{{ old('nombre', $lastPref['nombre'] ?? '') }}"
           required style="width:100%;padding:8px;margin:6px 0">

    <label>Edad</label>
    <input type="number" name="edad"
           value="{{ old('edad', $lastPref['edad'] ?? '') }}"
           required style="width:100%;padding:8px;margin:6px 0">

    <label>Dirección</label>
    <input type="text" name="direccion"
           value="{{ old('direccion', $lastPref['direccion'] ?? '') }}"
           required style="width:100%;padding:8px;margin:6px 0">

    <label>¿Envío a domicilio?</label>
    @php $env = old('envio', $lastPref['envio'] ?? 'sí'); @endphp
    <select name="envio" style="width:100%;padding:8px;margin:6px 0">
      <option value="sí" {{ $env === 'sí' ? 'selected' : '' }}>Sí</option>
      <option value="no" {{ $env === 'no' ? 'selected' : '' }}>No</option>
    </select>

    <label>Condición de salud</label>
    @php $condVal = old('enfermedad', $lastPref['enfermedad'] ?? ''); @endphp
    <select name="enfermedad" style="width:100%;padding:8px;margin:6px 0">
      <option value="">Sin condiciones</option>
      <option value="hipertenso"          {{ $condVal==='hipertenso' ? 'selected' : '' }}>Hipertenso (bajo en sodio)</option>
      <option value="diabetico"           {{ $condVal==='diabetico' ? 'selected' : '' }}>Diabético (bajo en azúcar)</option>
      <option value="intolerante_lactosa" {{ $condVal==='intolerante_lactosa' ? 'selected' : '' }}>Intolerante a la lactosa</option>
      <option value="celiaco"             {{ $condVal==='celiaco' ? 'selected' : '' }}>Celíaco (sin gluten)</option>
      <option value="cardiaco"            {{ $condVal==='cardiaco' ? 'selected' : '' }}>Cardíaco (bajo en grasa)</option>
    </select>

    <label>Preferencia alimentaria</label>
    @php $prefVal = old('preferencia', $lastPref['preferencia'] ?? ''); @endphp
    <select name="preferencia" style="width:100%;padding:8px;margin:6px 0">
      <option value="">Sin restricciones</option>
      <option value="vegano"      {{ $prefVal==='vegano' ? 'selected' : '' }}>Vegano</option>
      <option value="vegetariano" {{ $prefVal==='vegetariano' ? 'selected' : '' }}>Vegetariano</option>
    </select>
    <div class="muted" style="font-size:.9em;margin:-2px 0 8px">
      Si eres <strong>celíaco</strong>, marca esa opción en <em>Condición de salud</em>.
    </div>

    <label>Tipo de alimento deseado</label>
    @php $tipoVal = old('alimento', $lastPref['alimento'] ?? ''); @endphp
    <select name="alimento" style="width:100%;padding:8px;margin:6px 0">
      <option value="">No especifico</option>
      {{-- Si quisieras reactivar tipos, vuelve a habilitar estas opciones:
      <option value="ensalada"  {{ $tipoVal==='ensalada' ? 'selected' : '' }}>Ensalada</option>
      <option value="wrap"      {{ $tipoVal==='wrap' ? 'selected' : '' }}>Wrap</option>
      <option value="menu fit"  {{ $tipoVal==='menu fit' ? 'selected' : '' }}>Menú Fit</option>
      --}}
    </select>

    <button type="submit" class="btn btn-primary btn-lg"> Ver opciones</button>

    @auth
      <a href="{{ route('builder.form') }}" class="btn btn-dark"> Buscar menú avanzado</a>
 <a href="{{ route('plan.ver') }}" class="btn"> Plan semanal</a>

    @endauth
  </form>

  {{-- Criterios activos --}}
  @php
    $crit = [
      'enfermedad'  => $lastPref['enfermedad']  ?? '',
      'preferencia' => $lastPref['preferencia'] ?? '',
      'alimento'    => $lastPref['alimento']    ?? '',
    ];
    $hayCrit = array_filter($crit);
  @endphp
  @if($hayCrit)
    <p class="muted" style="margin:.8rem 0 0">
      <strong>Criterios:</strong>
      @if(!empty($crit['enfermedad']))  {{ $crit['enfermedad'] }} @endif
      @if(!empty($crit['preferencia'])) · {{ $crit['preferencia'] }} @endif
      @if(!empty($crit['alimento']))    · {{ $crit['alimento'] }} @endif
    </p>
  @endif

  @auth
    {{-- 
         Recomendados por historial 
        --}}
    @if($segunCompra->count())
      <hr>
      <div class="recs-verdoso">
      <h3>Recomendados según tu compra anterior</h3>
      @include('partials.lista_por_restaurante', ['lista' => $segunCompra])
      </div>
    @endif

    {{-- 
         Otros compatibles (agrupados) pero no para ser visto en el home... solo en el recomendados
       
    @if($otros->count())
      <hr>
      <div class="recs-amarillo">
      <h3> Otros menús compatibles</h3>
      @include('partials.lista_por_restaurante', ['lista' => $otros])
    @endif --}}
  @endauth

@else
  {{-- Vista mínima para ADMIN --}}
  <h2>Panel de inicio (admin)</h2>
  <p class="muted">
    Estás autenticado como <strong>administrador</strong>. Usa el
    <a href="{{ route('admin.index') }}">Panel Admin</a>
    para gestionar restaurantes, menús, pedidos y opiniones.
  </p>
     </div>
@endif
@endsection
