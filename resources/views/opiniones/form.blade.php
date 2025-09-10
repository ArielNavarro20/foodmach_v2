@extends('layouts.app')

@section('content')
<div class="container" style="max-width:560px">
  <h2>Deja tu opinión </h2>

  @if ($errors->any())
    <div style="color:#b00;margin:.5rem 0">
      @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  <form action="{{ route('opiniones.store') }}" method="POST">
    @csrf

    <label>Mensaje</label>
    <textarea name="mensaje" rows="5" required
      style="width:100%;padding:10px;margin:6px 0">{{ old('mensaje') }}</textarea>

    <button type="submit" style="padding:10px 14px">Enviar opinión</button>
  </form>
</div>
@endsection
