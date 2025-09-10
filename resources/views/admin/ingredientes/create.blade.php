@extends('layouts.app')

@section('content')
<h2>Crear ingrediente</h2>

@if($errors->any())
  <div class="alert alert-danger">
    <strong>Revisa los campos:</strong>
    <ul class="mb-0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.ingredientes.store') }}">
      @csrf
      @include('admin.ingredientes._form', ['ing' => $ing])
    </form>
  </div>
</div>
@endsection
