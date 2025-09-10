<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'FoodMach' }}</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

  <style>
    
    .navbar {
      background: linear-gradient(90deg, #1f2937, #111827);
      padding: 12px 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      border-bottom: 2px solid #4ade80; 
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }
    .navbar a, .navbar form button.link {
      color: #f9fafb;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      transition: color 0.3s, transform 0.2s;
    }
    .navbar a:hover, .navbar form button.link:hover {
      color: #4ade80; 
      transform: translateY(-2px);
    }
    .navbar .spacer {
      flex-grow: 1; 
    }
    button.link {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0;
    }

    
    .container {
      max-width: 1000px;
      margin: 20px auto;
      padding: 0 16px;
    }

    
    @media (max-width: 768px) {
      .navbar {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
      }
      .navbar a, .navbar form button.link {
        font-size: 14px;
      }
    }
  </style>
</head>
<body class="@yield('body_class')">
  <nav class="navbar">
    <a href="{{ route('home') }}">Inicio</a>

    @auth
      @if(auth()->user()->rol === 'admin')
        {{-- Navegación para ADMIN --}}
        <a href="{{ route('admin.index') }}">Panel Admin</a>
        <a href="{{ route('opiniones.index') }}">Opiniones</a>
      @else
        {{-- Navegación para CLIENTE --}}
        <a href="{{ route('mis.pedidos') }}">Mis Pedidos</a>
        <a href="{{ route('boleta') }}">Boleta</a>
        <a href="{{ route('recomendar') }}">Recomendados</a>
        <a href="{{ route('opiniones.index') }}">Opiniones</a>
        <a href="{{ route('opiniones.form') }}">Opinar</a>
        <a href="{{ route('builder.form') }}">Buscar menú</a>
      @endif

      <div class="spacer"></div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="link">Cerrar sesión</button>
      </form>
    @else
      <div class="spacer"></div>
      <a href="{{ route('login.form') }}">Iniciar sesión</a>
    @endauth
  </nav>

  <main class="container">
    @yield('content')
  </main>
</body>
</html>
