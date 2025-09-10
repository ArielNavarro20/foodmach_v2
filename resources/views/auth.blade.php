@extends('layouts.app')

@section('content')
<div class="container" style="max-width:420px">
  <h2>Iniciar sesión </h2>

  @if ($errors->any())
    <div style="color:#b00;margin:.5rem 0">
      @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('login.do') }}">
    @csrf
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email') }}" required style="width:100%;padding:8px;margin:6px 0">

    <label>Contraseña</label>
    <input type="password" name="password" required style="width:100%;padding:8px;margin:6px 0">

    <button type="submit" style="width:100%;padding:10px">Entrar</button>
  </form>
</div>
@endsection
