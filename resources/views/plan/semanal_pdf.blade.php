<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Plan semanal</title>
<style>
  body{ font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }
  h2{ margin: 0 0 8px }
  table{ width:100%; border-collapse:collapse }
  th,td{ border:1px solid #ccc; padding:6px; vertical-align:top }
  thead tr{ background:#444; color:#fff }
  .muted{ color:#555; margin: 0 0 10px }
</style>
</head>
<body>
  <h2>Plan semanal (Lunes a Viernes)</h2>
  <p class="muted">* Envío disponible a tu lugar de trabajo (oficina u otro destino).</p>
  <table>
    <thead>
      <tr>
        <th>Día</th>
        <th>Fecha</th>
        <th>Menú</th>
        <th>Restaurante</th>
        <th>Descripción</th>
      </tr>
    </thead>
    <tbody>
      @php $nombres = ['Lunes','Martes','Miércoles','Jueves','Viernes']; @endphp
      @foreach($dias as $i => $d)
        @php $m = $seleccion[$i] ?? null; @endphp
        <tr>
          <td>{{ $d['nombre'] ?? ($nombres[$i] ?? '') }}</td>
          <td>{{ $d['fecha'] }}</td>
          <td>{{ $m?->nombre ?? '—' }}</td>
          <td>{{ $m?->restaurante ?? '—' }}</td>
          <td>{{ $m?->descripcion ?? '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
