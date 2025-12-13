<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $collection['name'] ?? 'Col·lecció' }} · Infofilm</title>
    <meta property="og:title" content="{{ $collection['name'] ?? 'Col·lecció' }} · Infofilm">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($collection['overview'] ?? '', 150) }}">
    @if(!empty($collection['poster']))
        <meta property="og:image" content="https://image.tmdb.org/t/p/w780{{ $collection['poster'] }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="https://image.tmdb.org/t/p/w780{{ $collection['poster'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0d1117;
            --card: #161b22;
            --accent: #ffb703;
            --text: #e6edf3;
            --muted: #9da7b3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Space Grotesk", system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg);
            color: var(--text);
        }
        a { color: inherit; text-decoration: none; }
        header {
            padding: 32px 24px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .back {
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
            font-size: 14px;
        }
        .hero {
            display: grid;
            grid-template-columns: minmax(260px, 320px) 1fr;
            gap: 24px;
            padding: 0 24px 24px;
        }
        .poster {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.45);
            background: linear-gradient(135deg, rgba(255,183,3,0.12), rgba(13,17,23,0.7));
        }
        .poster img { width: 100%; display: block; }
        h1 { margin: 0 0 8px; font-size: clamp(26px, 3vw, 34px); letter-spacing: -0.02em; }
        .meta { color: var(--muted); font-size: 14px; }
        .section { padding: 0 24px 24px; }
        .section h2 { margin: 0 0 12px; font-size: 18px; }
        .grid-parts { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
        .card { background: var(--card); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; }
        .card img { width: 100%; display: block; }
        .card-body { padding: 10px; }
        .card-title { font-weight: 600; margin: 0 0 4px; }
        .card-meta { color: var(--muted); font-size: 13px; }
        .images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; }
        .img-card { position: relative; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); background: #0b0f14; }
        .img-card img { width: 100%; display: block; }
        .img-meta { position: absolute; right: 8px; bottom: 8px; background: rgba(0,0,0,0.55); color: #cfd6e0; padding: 4px 6px; border-radius: 8px; font-size: 12px; }
        .chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
        .chip { padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,0.06); font-size: 13px; }
        .error { padding: 24px; color: var(--muted); }
        @media (max-width: 820px) {
            .hero { grid-template-columns: 1fr; }
            header { padding: 24px 16px 8px; }
            .section { padding: 0 16px 16px; }
        }
    </style>
</head>
<body>
    <header>
        <a class="back" href="{{ url()->previous() }}">← Tornar</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif(!$collection)
        <div class="error">No s'ha trobat la col·lecció.</div>
    @else
        <section class="hero">
            <div class="poster">
                @if($collection['poster'])
                    <img src="https://image.tmdb.org/t/p/w500{{ $collection['poster'] }}" alt="Pòster de {{ $collection['name'] }}">
                @elseif($collection['backdrop'])
                    <img src="https://image.tmdb.org/t/p/w780{{ $collection['backdrop'] }}" alt="Fons de {{ $collection['name'] }}">
                @endif
            </div>
            <div>
                <h1>{{ $collection['name'] }}</h1>
                @if($collection['overview'])
                    <p class="meta" style="line-height:1.6;">{{ $collection['overview'] }}</p>
                @endif
            </div>
        </section>

        @if(!empty($collection['translations']))
            <section class="section">
                <h2>Traduccions</h2>
                <div class="chips">
                    @foreach($collection['translations'] as $tr)
                        <span class="chip" title="{{ $tr['title'] ?: 'Sense títol' }}">
                            {{ strtoupper($tr['language']) }}{{ $tr['country'] ? ' · ' . $tr['country'] : '' }} — {{ $tr['name'] ?: $tr['english_name'] }}
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($collection['parts']))
            <section class="section">
                <h2>Pel·lícules</h2>
                <div class="grid-parts">
                    @foreach($collection['parts'] as $part)
                        <a class="card" href="{{ route('movie.show', ['id' => $part['id']]) }}">
                            @if($part['poster'])
                                <img src="https://image.tmdb.org/t/p/w342{{ $part['poster'] }}" alt="Cartell de {{ $part['title'] }}">
                            @endif
                            <div class="card-body">
                                <div class="card-title">{{ $part['title'] }}</div>
                                <div class="card-meta">
                                    @if($part['release_date'])
                                        {{ \Illuminate\Support\Carbon::parse($part['release_date'])->translatedFormat('Y') }}
                                    @endif
                                    @if($part['vote_average'])
                                        · ★ {{ number_format($part['vote_average'], 1) }}
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($collection['backdrops']) || !empty($collection['posters']))
            <section class="section">
                <h2>Imatges</h2>
                <div class="images-grid">
                    @foreach($collection['backdrops'] as $img)
                        <div class="img-card">
                            <img src="https://image.tmdb.org/t/p/w780{{ $img['path'] }}" alt="Backdrop de {{ $collection['name'] }}">
                            @if($img['width'] && $img['height'])
                                <div class="img-meta">{{ $img['width'] }}×{{ $img['height'] }}</div>
                            @endif
                        </div>
                    @endforeach
                    @foreach($collection['posters'] as $img)
                        <div class="img-card">
                            <img src="https://image.tmdb.org/t/p/w500{{ $img['path'] }}" alt="Pòster de {{ $collection['name'] }}">
                            @if($img['width'] && $img['height'])
                                <div class="img-meta">{{ $img['width'] }}×{{ $img['height'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</body>
</html>
