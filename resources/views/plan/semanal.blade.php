@extends('layouts.app')

@section('title','Plan semanal')

@section('content')
  <h2>Plan semanal compatible</h2>

  @if(!empty($tags))
    <p class="muted" style="margin:6px 0">
      <strong>Criterios activos:</strong> {{ implode(' · ', $tags) }}
    </p>
  @endif

  <p class="muted" style="margin:6px 0">{{ $nota ?? '' }}</p>

  <div style="overflow:auto">
    <table id="tablaPlan" style="width:100%;border-collapse:collapse;background:#fff">
      <thead>
        <tr style="background:#222;color:#fff">
          <th style="padding:8px;border:1px solid #ddd">Día</th>
          <th style="padding:8px;border:1px solid #ddd;text-align:left">Menú</th>
          <th style="padding:8px;border:1px solid #ddd;text-align:left">Restaurante</th>
          <th style="padding:8px;border:1px solid #ddd">Porción (g)</th>
          <th style="padding:8px;border:1px solid #ddd">kcal</th>
          <th style="padding:8px;border:1px solid #ddd">Prot (g)</th>
          <th style="padding:8px;border:1px solid #ddd">Grasa (g)</th>
          <th style="padding:8px;border:1px solid #ddd">Carbo (g)</th>
          <th style="padding:8px;border:1px solid #ddd">Azúcar (g)</th>
          <th style="padding:8px;border:1px solid #ddd">Sodio (mg)</th>
        </tr>
      </thead>

      <tbody>
        @foreach($plan as $idx => $fila)
          @php
            /** @var \stdClass $m */
            $m = $fila['menu'];
            $base = (int)($m->base_gramos ?? 0);               // suma de gramos del plato
            $startGrams = $base > 0 ? $base : 0;               // valor inicial del input
            $ajustable = $base > 0;                            // si no hay ingredientes cargados, no se puede escalar
          @endphp

          <tr>
            {{-- Día --}}
            <td style="padding:8px;border:1px solid #eee;font-weight:600">{{ $fila['dia'] }}</td>

            {{-- Menú + ingredientes --}}
            <td style="padding:8px;border:1px solid #eee">
              <div style="font-weight:600">{{ $m->menu_nombre }}</div>

              @if(!empty($m->ing_lista))
                <div class="muted" style="font-size:.9em">
                  Ingredientes ({{ (int)($m->ing_count ?? 0) }}): {{ $m->ing_lista }}
                </div>
              @elseif(!empty($m->menu_desc))
                <div class="muted" style="font-size:.9em">{{ $m->menu_desc }}</div>
              @endif
            </td>

            {{-- Restaurante --}}
            <td style="padding:8px;border:1px solid #eee">
              <div>{{ $m->restaurante_nombre }}</div>
            </td>

            {{-- Porción (g) --}}
            <td style="padding:8px;border:1px solid #eee;text-align:center">
              @if($ajustable)
                <input
                  id="g_{{ $idx }}"
                  type="number"
                  min="50"
                  step="10"
                  value="{{ $startGrams }}"
                  class="portion-input"
                  style="width:90px;text-align:right;padding:6px;border:1px solid #ccc;border-radius:6px"
                  data-idx="{{ $idx }}"
                  data-base="{{ $base }}"
                >
                <input id="base_{{ $idx }}" type="hidden" value="{{ $base }}">
                <div class="muted" style="font-size:.8em">Base: {{ $base }} g</div>
              @else
                <span class="muted" style="font-size:.9em">Base 320g</span>
                <div class="muted" style="font-size:.8em">Gramos estandarizados</div>
                <input id="g_{{ $idx }}" type="hidden" value="0" data-idx="{{ $idx }}" data-base="0">
                <input id="base_{{ $idx }}" type="hidden" value="0">
              @endif
            </td>

            {{-- Celdas con macros (dataset = valor base para escalar) --}}
            <td id="kcal_{{ $idx }}"   data-base="{{ (float)$m->kcal }}"     style="padding:8px;border:1px solid #eee;text-align:right">{{ (int)$m->kcal }}</td>
            <td id="prot_{{ $idx }}"   data-base="{{ (float)$m->prot_g }}"   style="padding:8px;border:1px solid #eee;text-align:right">{{ number_format((float)$m->prot_g,2) }}</td>
            <td id="grasa_{{ $idx }}"  data-base="{{ (float)$m->grasa_g }}"  style="padding:8px;border:1px solid #eee;text-align:right">{{ number_format((float)$m->grasa_g,2) }}</td>
            <td id="carb_{{ $idx }}"   data-base="{{ (float)$m->carb_g }}"   style="padding:8px;border:1px solid #eee;text-align:right">{{ number_format((float)$m->carb_g,2) }}</td>
            <td id="azucar_{{ $idx }}" data-base="{{ (float)$m->azucar_g }}" style="padding:8px;border:1px solid #eee;text-align:right">{{ number_format((float)$m->azucar_g,2) }}</td>
            <td id="sodio_{{ $idx }}"  data-base="{{ (float)$m->sodio_mg }}" style="padding:8px;border:1px solid #eee;text-align:right">{{ (int)$m->sodio_mg }}</td>
          </tr>
        @endforeach
      </tbody>

      <tfoot>
        <tr style="background:#f7f7f7;font-weight:600">
          <td colspan="4" style="padding:8px;border:1px solid #ddd;text-align:right">Totales semana</td>
          <td id="tot_kcal"  style="padding:8px;border:1px solid #ddd;text-align:right">{{ $totales['kcal'] }}</td>
          <td id="tot_prot"  style="padding:8px;border:1px solid #ddd;text-align:right">{{ $totales['prot'] }}</td>
          <td id="tot_grasa" style="padding:8px;border:1px solid #ddd;text-align:right">{{ $totales['grasa'] }}</td>
          <td id="tot_carb"  style="padding:8px;border:1px solid #ddd;text-align:right">{{ $totales['carb'] }}</td>
          <td id="tot_azuc"  style="padding:8px;border:1px solid #ddd;text-align:right">{{ $totales['azuc'] }}</td>
          <td id="tot_sodio" style="padding:8px;border:1px solid #ddd;text-align:right">{{ $totales['sodio'] }}</td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap">
    <a href="{{ route('home') }}" class="btn">Volver al inicio</a>
    <a href="{{ route('plan.print') }}" class="btn">Dame otro plan semanal</a>
    <button onclick="window.print()" class="btn btn-dark">Descargar e imprimir plan</button>
  </div>

  <style>
    .muted{color:#555}
    .btn{display:inline-block;padding:8px 12px;border-radius:8px;background:#0a7bff;color:#fff;text-decoration:none}
    .btn:hover{text-decoration:underline}
    .btn-dark{background:#111}
    @media print {
      nav.navbar{display:none!important}
      .btn{display:none!important}
      body{background:#fff!important}
    }
  </style>

  <script>
    
    function recalc(idx){
      const base  = Number(document.getElementById('base_'+idx)?.value || 0);
      const grams = Number(document.getElementById('g_'+idx)?.value    || 0);
      const factor = base > 0 ? (grams / base) : 0;

      const spec = [
        ['kcal',  0],
        ['prot',  2],
        ['grasa', 2],
        ['carb',  2],
        ['azucar',2],
        ['sodio', 0],
      ];

      spec.forEach(([key,dec])=>{
        const cell = document.getElementById(key+'_'+idx);
        if(!cell) return;
        const bval = Number(cell.dataset.base || 0);
        cell.textContent = (bval * factor).toFixed(dec);
      });

      recalcTotals();
    }

    
    function recalcTotals(){
      const rows = document.querySelectorAll('#tablaPlan tbody tr');
      let tk=0,tp=0,tg=0,tc=0,ta=0,ts=0;

      rows.forEach((tr, i)=>{
        tk += Number(document.getElementById('kcal_'+i)?.textContent || 0);
        tp += Number(document.getElementById('prot_'+i)?.textContent || 0);
        tg += Number(document.getElementById('grasa_'+i)?.textContent || 0);
        tc += Number(document.getElementById('carb_'+i)?.textContent  || 0);
        ta += Number(document.getElementById('azucar_'+i)?.textContent|| 0);
        ts += Number(document.getElementById('sodio_'+i)?.textContent || 0);
      });

      document.getElementById('tot_kcal').textContent   = Math.round(tk);
      document.getElementById('tot_prot').textContent   = tp.toFixed(1);
      document.getElementById('tot_grasa').textContent  = tg.toFixed(1);
      document.getElementById('tot_carb').textContent   = tc.toFixed(1);
      document.getElementById('tot_azuc').textContent   = ta.toFixed(1);
      document.getElementById('tot_sodio').textContent  = Math.round(ts);
    }

    
    document.addEventListener('DOMContentLoaded', ()=>{
      document.querySelectorAll('.portion-input').forEach(inp=>{
        inp.addEventListener('input', ()=>{
          const idx = inp.dataset.idx;
          recalc(idx);
        });
      });
      
      recalcTotals();
    });
  </script>
@endsection
