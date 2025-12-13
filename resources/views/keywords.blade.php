<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keywords · Infofilm</title>
    <meta name="description" content="Llista de paraules clau recollides de pel·lícules populars i en tendència (TMDB).">
    <meta property="og:title" content="Keywords · Infofilm">
    <meta property="og:description" content="Explora paraules clau per descobrir noves pel·lícules.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        header { padding:32px 24px 12px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
        h1 { margin:0; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .back { padding:10px 12px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.07); font-size:14px; text-decoration:none; color:inherit; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; padding:0 24px 24px; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; padding:12px; text-decoration:none; color:inherit; display:flex; align-items:center; gap:10px; }
        .card:hover { border-color:rgba(255,183,3,0.3); box-shadow:0 10px 24px rgba(0,0,0,0.35); }
        .card i { color:var(--accent); }
        .error { padding:24px; color:var(--muted); }
        @media (max-width:600px){ header{padding:24px 16px 8px;} .grid{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <h1>Totes les keywords</h1>
        <a class="back" href="{{ route('home') }}">← Portada</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif($keywords->isEmpty())
        <div class="error">No s'han trobat keywords.</div>
    @else
        <div class="grid">
            @foreach($keywords as $kw)
                <a class="card" href="{{ route('keyword.show', ['id' => $kw['id']]) }}">
                    <i class="fa fa-tag"></i>
                    <span>{{ $kw['name'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</body>
</html>
