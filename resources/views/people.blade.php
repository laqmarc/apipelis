<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persones populars · Infofilm</title>
    <meta name="description" content="Persones populars de TMDB ordenades per popularitat.">
    <meta property="og:title" content="Persones populars · Infofilm">
    <meta property="og:description" content="Descobreix actors, actrius i professionals més populars ara mateix.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        a { color: inherit; text-decoration: none; }
        header { padding:32px 24px 12px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
        h1 { margin:0; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .back { padding:10px 12px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.07); font-size:14px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; padding:0 24px 24px; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
        .card img { width:100%; display:block; }
        .card-body { padding:10px; }
        .card-title { font-weight:600; margin:0 0 4px; }
        .card-meta { color:var(--muted); font-size:13px; }
        .chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:6px; }
        .chip { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,0.06); font-size:12px; }
        .error { padding:24px; color:var(--muted); }
        .pagination { display:flex; gap:8px; margin:12px 24px 24px; }
        .pagination a { padding:8px 10px; border-radius:10px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:var(--text); text-decoration:none; }
        .pagination a.active { background:var(--accent); color:#0d1117; border-color:var(--accent); }
        @media (max-width:820px){ header{padding:24px 16px 8px;} .grid{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <h1>Persones populars</h1>
        <a class="back" href="{{ route('home') }}">← Portada</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif($people->isEmpty())
        <div class="error">No s'han trobat persones.</div>
    @else
        <div class="grid">
            @foreach($people as $person)
                <a class="card" href="{{ route('person.show', ['id' => $person['id']]) }}">
                    @if($person['profile'])
                        <img src="https://image.tmdb.org/t/p/w342{{ $person['profile'] }}" alt="Foto de {{ $person['name'] }}" loading="lazy">
                    @endif
                    <div class="card-body">
                        <div class="card-title">{{ $person['name'] }}</div>
                        <div class="card-meta">
                            {{ $person['department'] }}
                            @if($person['popularity'])
                                · Popularitat {{ number_format($person['popularity'], 0) }}
                            @endif
                        </div>
                        @if(!empty($person['known_for']))
                            <div class="chips">
                                @foreach($person['known_for'] as $kf)
                                    @if($kf['media_type'] === 'movie')
                                        <span class="chip"><a href="{{ route('movie.show', ['id' => $kf['id']]) }}">{{ $kf['title'] }}</a></span>
                                    @else
                                        <span class="chip">{{ $kf['title'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        @if(($totalPages ?? 1) > 1)
            <div class="pagination">
                @if(($page ?? 1) > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page-1]) }}">« Anterior</a>
                @endif
                <a class="active">{{ $page ?? 1 }} / {{ $totalPages }}</a>
                @if(($page ?? 1) < $totalPages)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page+1]) }}">Següent »</a>
                @endif
            </div>
        @endif
    @endif
</body>
</html>
