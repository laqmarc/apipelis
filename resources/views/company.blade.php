<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company['name'] ?? 'Companyia' }} · Infofilm</title>
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
        .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; }
        .info-item { background:var(--card); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:12px; }
        .label { color:var(--muted); font-size:12px; letter-spacing:0.02em; text-transform:uppercase; }
        .value { margin-top:4px; font-weight:600; }
        .chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:4px; }
        .chip { padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.06); font-size:13px; }
        .logos { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
        .logo-card { background:var(--card); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center; }
        .logo-card img { max-width:100%; max-height:120px; object-fit:contain; }
        .parts { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
        .card img { width:100%; display:block; }
        .card-body { padding:10px; }
        .card-title { font-weight:600; margin:0 0 4px; }
        .card-meta { color:var(--muted); font-size:13px; }
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
    @elseif(!$company)
        <div class="error">No s'ha trobat la companyia.</div>
    @else
        <section class="hero">
            <div class="logo">
                @if($company['logo'])
                    <img src="https://image.tmdb.org/t/p/w500{{ $company['logo'] }}" alt="Logo {{ $company['name'] }}">
                @endif
            </div>
            <div>
                <h1>{{ $company['name'] }}</h1>
                <div class="meta">
                    @if($company['origin_country'])
                        País: {{ $company['origin_country'] }}
                    @endif
                </div>
                @if($company['description'])
                    <p class="meta" style="line-height:1.6; margin-top:10px;">{{ $company['description'] }}</p>
                @endif
            </div>
        </section>

        <section class="section">
            <div class="info-grid">
                @if($company['headquarters'])
                    <div class="info-item">
                        <div class="label">Seu</div>
                        <div class="value">{{ $company['headquarters'] }}</div>
                    </div>
                @endif
                @if($company['homepage'])
                    <div class="info-item">
                        <div class="label">Web</div>
                        <div class="value"><a href="{{ $company['homepage'] }}" target="_blank" rel="noreferrer">Obrir</a></div>
                    </div>
                @endif
                @if($company['parent_company'])
                    <div class="info-item">
                        <div class="label">Companyia pare</div>
                        <div class="value">{{ $company['parent_company']['name'] ?? $company['parent_company'] }}</div>
                    </div>
                @endif
            </div>
        </section>

        @if(!empty($company['alternative_names']))
            <section class="section">
                <h2>Noms alternatius</h2>
                <div class="chips">
                    @foreach($company['alternative_names'] as $name)
                        <span class="chip">{{ $name }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($company['logos']))
            <section class="section">
                <h2>Logos</h2>
                <div class="logos">
                    @foreach($company['logos'] as $logo)
                        <div class="logo-card">
                            <img src="https://image.tmdb.org/t/p/w300{{ $logo['path'] }}" alt="Logo {{ $company['name'] }}">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($company['movies']))
            <section class="section">
                <h2>Pel·lícules</h2>
                <div class="parts">
                    @foreach($company['movies'] as $movie)
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
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</body>
</html>
