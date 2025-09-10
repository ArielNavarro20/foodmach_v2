@extends('layouts.app')

@section('content')
<h2>Construye tu menú avanzado </h2>
<p class="muted">Marca ingredientes y ajusta los gramos; los nutrientes se recalculan automáticamente.</p>

{{-- Filtros activos (desde la sesión del Home) --}}
@if(!empty($filtrosActivos))
  <div style="background:#f6f8fa;border:1px solid #e1e4e8;border-radius:8px;padding:8px 10px;margin:10px 0">
    <strong>Filtros activos:</strong>
    Preferencia = <em>{{ $filtrosActivos['preferencia'] ?: '—' }}</em>,
    Condición = <em>{{ $filtrosActivos['condicion'] ?: '—' }}</em>
  </div>
@endif

{{-- Errores --}}
@if ($errors->any())
  <div style="color:#b00;margin:.5rem 0">
    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
  </div>
@endif

{{-- Sin ingredientes compatibles --}}
@if(empty($grupos))
  <div style="padding:12px;border:1px solid #eee;border-radius:8px;background:#fff">
    No hay ingredientes compatibles con los filtros actuales.
    Cambia tu preferencia/condición en <a href="{{ route('home') }}">Inicio</a>.
  </div>
@else

<form method="POST" action="{{ route('builder.crear') }}" id="builderForm">
  @csrf

  @foreach($grupos as $categoria => $ings)
    <div style="border:1px solid #ddd;border-radius:10px;padding:12px;margin:14px 0;background:#fff">
      <h3 style="margin:.2rem 0">{{ ucfirst($categoria) }}</h3>

      <div style="display:grid;gap:10px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
        @foreach($ings as $ing)
          <label
            class="row-ing"
            style="border:1px solid #eee;border-radius:10px;padding:10px;display:flex;align-items:flex-start;gap:10px"
            data-id="{{ $ing->id }}"
            data-kcal="{{ (float)$ing->calorias }}"
            data-prot="{{ (float)$ing->proteina }}"
            data-grasa="{{ (float)$ing->grasa }}"
            data-carb="{{ (float)$ing->carbo }}"
            data-azucar="{{ (float)$ing->azucar }}"
            data-sodio="{{ (int)$ing->sodio_mg }}"
          >
            <input type="checkbox"
                   class="ck-ing"
                   name="ingrediente_id[]"
                   value="{{ $ing->id }}"
                   style="margin-top:4px">

            <div style="flex:1">
              <div style="font-weight:700">{{ $ing->nombre }}</div>

              {{-- Nutrición por 100g (base) y escala dinámica --}}
              <div class="muted" style="font-size:.9em">
                <span>Base (100g):</span>
                <span>{{ (int)$ing->calorias }} kcal</span> ·
                <span>P {{ (float)$ing->proteina }}</span> ·
                <span>G {{ (float)$ing->grasa }}</span> ·
                <span>C {{ (float)$ing->carbo }}</span>
              </div>

              <div class="muted dyn-nutri" style="font-size:.9em;display:none">
                <strong>Seleccionado (</strong><span class="dyn-g">100</span><strong> g):</strong>
                <span class="dyn-kcal">0</span> kcal ·
                P <span class="dyn-prot">0</span> ·
                G <span class="dyn-grasa">0</span> ·
                C <span class="dyn-carb">0</span>
              </div>
            </div>

            <div>
              <input type="number"
                     class="g-ing"
                     name="gramos[]"
                     value="100"
                     min="1"
                     max="2000"
                     disabled
                     style="width:96px;padding:6px;border:1px solid #ccc;border-radius:8px">
              <div class="muted" style="font-size:.9em;text-align:center">gramos</div>
            </div>
          </label>
        @endforeach
      </div>
    </div>
  @endforeach

  {{-- Resumen de totales y acciones --}}
  <div style="position:sticky;bottom:0;background:rgba(255,255,255,.96);backdrop-filter:saturate(180%) blur(6px);border:1px solid #ddd;border-radius:12px;padding:12px;margin-top:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between">
    <div class="muted" style="font-size:.95em">
      <strong>Totales seleccionados:</strong>
      <span class="t-kcal">0</span> kcal ·
      P <span class="t-prot">0</span> g ·
      G <span class="t-grasa">0</span> g ·
      C <span class="t-carb">0</span> g
      <span class="muted">· Azúcar <span class="t-azucar">0</span> g · Sodio <span class="t-sodio">0</span> mg</span>
    </div>

    <div style="display:flex;gap:10px;align-items:center">
      <button type="submit" class="btn btn-primary btn-lg">Crear menú y pedir</button>
      <a href="{{ route('recomendar') }}" class="btn btn-outline">Volver a Recomendados</a>
    </div>
  </div>
</form>

@endif

{{-- codigoJS recalcular nutrientes  --}}
<script>
(function(){
  function round2(n){ return Math.round(n * 100) / 100; }

  function recalcRow(row){
    const ck   = row.querySelector('.ck-ing');
    const gramsInput = row.querySelector('.g-ing');
    const dyn  = row.querySelector('.dyn-nutri');
    const gEl  = row.querySelector('.dyn-g');
    const kcalEl  = row.querySelector('.dyn-kcal');
    const protEl  = row.querySelector('.dyn-prot');
    const grasaEl = row.querySelector('.dyn-grasa');
    const carbEl  = row.querySelector('.dyn-carb');

    if(!ck || !gramsInput || !dyn) return {kcal:0,prot:0,grasa:0,carb:0,azucar:0,sodio:0};

    const selected = ck.checked;
    gramsInput.disabled = !selected;
    dyn.style.display   = selected ? 'block' : 'none';

    const g = selected ? (parseFloat(gramsInput.value || '0') || 0) : 0;

    const kcal   = parseFloat(row.dataset.kcal || '0')   * g / 100;
    const prot   = parseFloat(row.dataset.prot || '0')   * g / 100;
    const grasa  = parseFloat(row.dataset.grasa || '0')  * g / 100;
    const carb   = parseFloat(row.dataset.carb || '0')   * g / 100;
    const azucar = parseFloat(row.dataset.azucar || '0') * g / 100;
    const sodio  = parseFloat(row.dataset.sodio || '0')  * g / 100;

    if(selected){
      if(gEl)     gEl.textContent    = g;
      if(kcalEl)  kcalEl.textContent = Math.round(kcal);
      if(protEl)  protEl.textContent = round2(prot);
      if(grasaEl) grasaEl.textContent= round2(grasa);
      if(carbEl)  carbEl.textContent = round2(carb);
    }

    return {
      kcal: kcal,
      prot: prot,
      grasa: grasa,
      carb: carb,
      azucar: azucar,
      sodio: sodio
    };
  }

  function recalcAll(){
    const rows = document.querySelectorAll('.row-ing');
    let T = {kcal:0,prot:0,grasa:0,carb:0,azucar:0,sodio:0};

    rows.forEach(row=>{
      const r = recalcRow(row);
      T.kcal   += r.kcal;
      T.prot   += r.prot;
      T.grasa  += r.grasa;
      T.carb   += r.carb;
      T.azucar += r.azucar;
      T.sodio  += r.sodio;
    });

    // Totales
    const tKcal  = document.querySelector('.t-kcal');
    const tProt  = document.querySelector('.t-prot');
    const tGrasa = document.querySelector('.t-grasa');
    const tCarb  = document.querySelector('.t-carb');
    const tAz    = document.querySelector('.t-azucar');
    const tNa    = document.querySelector('.t-sodio');

    if(tKcal)  tKcal.textContent  = Math.round(T.kcal);
    if(tProt)  tProt.textContent  = round2(T.prot);
    if(tGrasa) tGrasa.textContent = round2(T.grasa);
    if(tCarb)  tCarb.textContent  = round2(T.carb);
    if(tAz)    tAz.textContent    = round2(T.azucar);
    if(tNa)    tNa.textContent    = Math.round(T.sodio);
  }


  document.addEventListener('change', function(e){
    if(e.target.matches('.ck-ing')){
     
      const row = e.target.closest('.row-ing');
      const grams = row?.querySelector('.g-ing');
      if(e.target.checked && grams && !grams.value){
        grams.value = 100;
      }
      recalcAll();
    }
  });

  document.addEventListener('input', function(e){
    if(e.target.matches('.g-ing')){
      
      const v = parseFloat(e.target.value || '0');
      if(isNaN(v) || v < 1){ e.target.value = 1; }
      recalcAll();
    }
  });

 
  recalcAll();
})();
</script>
@endsection
