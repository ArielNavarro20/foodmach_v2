@php
  // helper rápido para valores
  function v($ing, $attr, $default = '') {
      return old($attr, $ing->$attr ?? $default);
  }
@endphp

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nombre *</label>
      <input type="text" name="nombre" value="{{ v($ing,'nombre') }}" class="form-control" required maxlength="120">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Categoría *</label>
      <input type="text" name="categoria" value="{{ v($ing,'categoria') }}" class="form-control" required maxlength="40" placeholder="Carne / Cereal / Lácteo / Legumbre / ...">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-2">
    <label>kcal *</label>
    <input type="number" name="calorias" value="{{ v($ing,'calorias',0) }}" class="form-control" min="0" required>
  </div>
  <div class="col-md-2">
    <label>Proteína (g) *</label>
    <input type="number" step="0.01" name="proteina" value="{{ v($ing,'proteina',0) }}" class="form-control" min="0" required>
  </div>
  <div class="col-md-2">
    <label>Grasa (g) *</label>
    <input type="number" step="0.01" name="grasa" value="{{ v($ing,'grasa',0) }}" class="form-control" min="0" required>
  </div>
  <div class="col-md-2">
    <label>Carbo (g) *</label>
    <input type="number" step="0.01" name="carbo" value="{{ v($ing,'carbo',0) }}" class="form-control" min="0" required>
  </div>
  <div class="col-md-2">
    <label>Azúcar (g) *</label>
    <input type="number" step="0.01" name="azucar" value="{{ v($ing,'azucar',0) }}" class="form-control" min="0" required>
  </div>
  <div class="col-md-2">
    <label>Sodio (mg) *</label>
    <input type="number" name="sodio_mg" value="{{ v($ing,'sodio_mg',0) }}" class="form-control" min="0" required>
  </div>
</div>

<div class="mt-3">
  <label class="mr-3"><input type="checkbox" name="es_gluten"  value="1" {{ old('es_gluten', $ing->es_gluten ?? false) ? 'checked' : '' }}> Contiene gluten</label>
  <label class="mr-3"><input type="checkbox" name="es_lactosa" value="1" {{ old('es_lactosa',$ing->es_lactosa ?? false) ? 'checked' : '' }}> Contiene lactosa</label>
  <label class="mr-3"><input type="checkbox" name="es_animal"  value="1" {{ old('es_animal', $ing->es_animal ?? false) ? 'checked' : '' }}> Origen animal</label>
</div>

<div class="mt-3">
  <button class="btn btn-primary">Guardar</button>
  <a href="{{ route('admin.index') }}" class="btn btn-light">Volver</a>
</div>
