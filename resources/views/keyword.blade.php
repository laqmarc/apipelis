<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $keyword['name'] ?? 'Keyword' }} · Infofilm</title>
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
        h1 { margin:0; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .section { padding:0 24px 24px; }
        .section h2 { margin:0 0 12px; font-size:18px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
        .card img { width:100%; display:block; }
        .card-body { padding:10px; }
        .card-title { font-weight:600; margin:0 0 4px; }
        .card-meta { color:var(--muted); font-size:13px; }
        .error { padding:24px; color:var(--muted); }
        @media (max-width:820px){ header{padding:24px 16px 8px;} .section{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <a class="back" href="{{ url()->previous() }}">← Tornar</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif(!$keyword)
        <div class="error">No s'ha trobat la keyword.</div>
    @else
        <section class="section">
            <h1>{{ $keyword['name'] }}</h1>
        </section>

        @if($movies->isNotEmpty())
            <section class="section">
                <h2>Pel·lícules amb aquesta keyword</h2>
                <div class="grid">
                    @foreach($movies as $movie)
                        <a class="card" href="{{ route('movie.show', ['id' => $movie['id']]) }}">
                            @if($movie['poster'])
                                <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster'] }}" alt="Cartell de {{ $movie['title'] }}">
                            @endif
                            <div class="card-body">
                                <div class="card-title">{{ $movie['title'] }}</div>
                                <div class="card-meta">
                                    @if($movie['release_date'])
                                        {{ \Illuminate\Support\Carbon::parse($movie['release_date'])->translatedFormat('Y') }}
                                    @endif
                                    @if($movie['vote_average'])
                                        · ★ {{ number_format($movie['vote_average'], 1) }}
                                    @endif
                                </div>
                                @if($movie['overview'])
                                    <p class="card-meta" style="margin:6px 0 0; line-height:1.4;">{{ \Illuminate\Support\Str::limit($movie['overview'], 110) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</body>
</html>
