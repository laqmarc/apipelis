<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pel·lícules en tendència · Infofilm</title>
    <meta name="description" content="Pots veure pel·lícules en tendència, en cartellera, populars, millor valorades i properes estrenes (dades TMDB).">
    <meta property="og:title" content="Pel·lícules en tendència · Infofilm">
    <meta property="og:description" content="Explora què es mira ara mateix i què està per arribar al cinema.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/owl/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/owl/owl.theme.default.min.css') }}" />
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        header { padding:32px 24px 16px; display:flex; align-items:center; justify-content:space-between; gap:16px; }
        h1 { margin:0; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .subtitle { margin:4px 0 0; color:var(--muted); font-size:14px; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:999px; background:rgba(255,183,3,0.12); color:#ffd166; font-size:12px; font-weight:600; text-decoration:none; }
        .section { padding:0 24px 24px; }
        .section h2 { margin:0 0 12px; font-size:20px; }
        .owl-carousel .owl-nav button.owl-prev,
        .owl-carousel .owl-nav button.owl-next { background:rgba(255,255,255,0.06); color:var(--text); border:1px solid rgba(255,255,255,0.1); border-radius:10px; width:36px; height:32px; margin:0 4px; }
        .owl-carousel .owl-nav button:hover { border-color:rgba(255,183,3,0.4); }
        .owl-theme .owl-dots { display:none; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease; text-decoration:none; color:inherit; }
        .card:hover { transform: translateY(-4px); border-color: rgba(255,183,3,0.3); box-shadow: 0 12px 30px rgba(0,0,0,0.35); }
        .poster { width:100%; aspect-ratio:2/3; object-fit:cover; background: linear-gradient(135deg, rgba(255,183,3,0.15), rgba(13,17,23,0.6)); }
        .card-body { padding:10px; display:flex; flex-direction:column; gap:6px; }
        .title { font-weight:600; font-size:15px; margin:0; }
        .meta { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:12px; flex-wrap:wrap; }
        .empty, .error { padding:24px; color:var(--muted); font-size:15px; }
        @media (max-width:600px){ header{padding:24px 16px 12px;} .section{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <div>
            <h1>Infofilm · Tendència</h1>
            <p class="subtitle">Les pel·lícules més vistes ara mateix (TMDB, idioma català).</p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <a class="pill" href="{{ route('people.popular') }}">★ Persones populars</a>
            <a class="pill" href="{{ route('keywords.index') }}">🏷️ Keywords</a>
            <a class="pill" href="{{ route('search') }}">🔍 Cerca</a>
        </div>
    </header>

    @if(!empty($error))
        <div class="error">{{ $error }}</div>
    @endif

    @php
        $sections = [
            ['title' => 'Tendència', 'items' => $trending, 'id' => 'trending'],
            ['title' => 'Ara en cartellera', 'items' => $nowPlaying, 'id' => 'nowplaying'],
            ['title' => 'Populars', 'items' => $popular, 'id' => 'popular'],
            ['title' => 'Millor valorades', 'items' => $topRated, 'id' => 'toprated'],
            ['title' => 'Pròximes estrenes', 'items' => $upcoming, 'id' => 'upcoming'],
        ];
    @endphp

    @foreach($sections as $section)
        <section class="section">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <h2>{{ $section['title'] }}</h2>
            </div>
            @if($section['items']->isEmpty())
                <div class="empty">No hi ha pel·lícules disponibles.</div>
            @else
                <div class="owl-carousel owl-theme" id="row-{{ $section['id'] }}">
                    @foreach($section['items'] as $movie)
                        <a class="card" href="{{ route('movie.show', ['id' => $movie['id']]) }}">
                            @if($movie['poster'])
                                <img class="poster" src="https://image.tmdb.org/t/p/w342{{ $movie['poster'] }}" alt="Cartell de {{ $movie['title'] }}" loading="lazy">
                            @else
                                <div class="poster"></div>
                            @endif
                            <div class="card-body">
                                <p class="title">{{ $movie['title'] }}</p>
                                <div class="meta">
                                    @if($movie['release_date'])
                                        <span>{{ \Illuminate\Support\Carbon::parse($movie['release_date'])->translatedFormat('d M Y') }}</span>
                                    @endif
                                    @if($movie['vote_average'])
                                        <span class="pill">★ {{ number_format($movie['vote_average'], 1) }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endforeach

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/owl/owl.carousel.min.js') }}"></script>
    <script>
        $(function () {
            $('.owl-carousel').owlCarousel({
                loop: false,
                margin: 12,
                nav: true,
                dots: false,
                navText: ['‹', '›'],
                responsive: {
                    0: { items: 2 },
                    600: { items: 3 },
                    900: { items: 5 },
                    1200: { items: 6 }
                }
            });
        });
    </script>
</body>
</html>
