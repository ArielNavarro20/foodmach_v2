@extends('layouts.app')
@section('content')
<h2>Iniciar Sesión </h2>
<form method="POST" action="{{ route('login.do') }}" class="card w400">
  @csrf
  <label>Email</label>
  <input type="email" name="email" value="{{ old('email') }}" required>
  @error('email')<small class="err">{{ $message }}</small>@enderror

  <label>Contraseña</label>
  <input type="password" name="password" required>
  @error('password')<small class="err">{{ $message }}</small>@enderror

  <button type="submit">Entrar</button>
</form>
@endsection