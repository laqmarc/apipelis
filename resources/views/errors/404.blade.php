<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No trobat | Infofilm</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#0d1117; color:#e6edf3; display:flex; align-items:center; justify-content:center; height:100vh; text-align:center; padding:24px; }
        .card { max-width:420px; background:#161b22; border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:20px; }
        a { color:#ffb703; text-decoration:none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pàgina no trobada</h1>
        <p>No hem pogut trobar aquesta ruta. Comença de nou des de la portada.</p>
        <p><a href="{{ url('/') }}">← Torna a l'inici</a></p>
    </div>
</body>
</html>
