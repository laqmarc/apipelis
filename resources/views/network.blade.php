<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $network['name'] ?? 'Network' }} · Infofilm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        a { color: inherit; text-decoration: none; }
        header { padding:32px 24px 12px; display:flex; align-items:center; gap:12px; }
        .back { padding:10px 12px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.07); font-size:14px; }
        .hero { display:grid; grid-template-columns:minmax(220px,260px) 1fr; gap:24px; padding:0 24px 24px; align-items:center; }
        .logo { width:100%; border-radius:16px; overflow:hidden; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; padding:20px; }
        .logo img { max-width:100%; max-height:180px; object-fit:contain; display:block; }
        h1 { margin:0 0 8px; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .meta { color:var(--muted); font-size:14px; }
        .section { padding:0 24px 24px; }
        .section h2 { margin:0 0 12px; font-size:18px; }
        .chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:4px; }
        .chip { padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.06); font-size:13px; }
        .logos { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
        .logo-card { background:var(--card); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center; }
        .logo-card img { max-width:100%; max-height:120px; object-fit:contain; }
        .error { padding:24px; color:var(--muted); }
        @media (max-width:820px){ .hero{grid-template-columns:1fr;} header{padding:24px 16px 8px;} .section{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <a class="back" href="{{ url()->previous() }}">← Tornar</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif(!$network)
        <div class="error">No s'ha trobat la xarxa.</div>
    @else
        <section class="hero">
            <div class="logo">
                @if($network['logo'])
                    <img src="https://image.tmdb.org/t/p/w500{{ $network['logo'] }}" alt="Logo {{ $network['name'] }}">
                @endif
            </div>
            <div>
                <h1>{{ $network['name'] }}</h1>
                <div class="meta">
                    @if($network['origin_country'])
                        País: {{ $network['origin_country'] }}
                    @endif
                </div>
                @if($network['headquarters'])
                    <p class="meta" style="margin-top:10px;">Seu: {{ $network['headquarters'] }}</p>
                @endif
                @if($network['homepage'])
                    <p class="meta" style="margin-top:6px;"><a href="{{ $network['homepage'] }}" target="_blank" rel="noreferrer" style="color:var(--accent);">Web oficial</a></p>
                @endif
            </div>
        </section>

        @if(!empty($network['alternative_names']))
            <section class="section">
                <h2>Noms alternatius</h2>
                <div class="chips">
                    @foreach($network['alternative_names'] as $name)
                        <span class="chip">{{ $name }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($network['logos']))
            <section class="section">
                <h2>Logos</h2>
                <div class="logos">
                    @foreach($network['logos'] as $logo)
                        <div class="logo-card">
                            <img src="https://image.tmdb.org/t/p/w300{{ $logo['path'] }}" alt="Logo {{ $network['name'] }}">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</body>
</html>
