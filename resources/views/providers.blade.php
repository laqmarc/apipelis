<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveïdors de streaming · Infofilm</title>
    <meta name="description" content="Filtra pel·lícules per plataforma de streaming (Netflix, Disney+, Prime Video, Filmin i més) amb dades de TMDB.">
    <meta property="og:title" content="Proveïdors de streaming · Infofilm">
    <meta property="og:description" content="Explora què pots veure a cada plataforma a la regió {{ $region ?? '—' }}.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        header { padding:32px 24px 12px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
        h1 { margin:0; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .back { padding:10px 12px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.07); font-size:14px; text-decoration:none; color:inherit; }
        .panel { padding:0 24px 20px; display:flex; flex-direction:column; gap:12px; }
        .lead { color:var(--muted); margin:0; font-size:14px; }
        .providers { display:grid; grid-template-columns:repeat(auto-fit, minmax(120px, 1fr)); gap:10px; }
        .provider-card { display:flex; flex-direction:column; align-items:center; gap:8px; padding:12px; border-radius:12px; background:var(--card); border:1px solid rgba(255,255,255,0.06); text-decoration:none; color:inherit; transition: transform 0.2s ease, border-color 0.2s ease; }
        .provider-card:hover { transform:translateY(-3px); border-color:rgba(255,183,3,0.4); }
        .provider-card img { width:72px; height:72px; object-fit:contain; background:rgba(255,255,255,0.03); border-radius:14px; padding:8px; }
        .provider-card .name { font-weight:600; font-size:14px; text-align:center; }
        form { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; align-items:end; }
        label { display:flex; flex-direction:column; gap:6px; font-size:13px; color:var(--muted); }
        select { padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05); color:var(--text); }
        .btn { padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:var(--accent); color:#0d1117; font-weight:600; cursor:pointer; }
        .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:12px; padding:0 24px 24px; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; }
        .card img { width:100%; aspect-ratio:2/3; object-fit:cover; background:linear-gradient(135deg, rgba(255,183,3,0.15), rgba(13,17,23,0.6)); }
        .poster-fallback { width:100%; aspect-ratio:2/3; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, rgba(255,183,3,0.12), rgba(3,169,244,0.08)); font-weight:700; font-size:22px; color:#0d1117; }
        .card-body { padding:10px; display:flex; flex-direction:column; gap:6px; }
        .card-title { font-weight:600; margin:0; font-size:15px; }
        .card-meta { color:var(--muted); font-size:12px; }
        .empty, .error { padding:24px; color:var(--muted); }
        .pagination { padding:0 24px 24px; display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
        .pagination a { padding:8px 10px; border-radius:10px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:var(--text); text-decoration:none; }
        .pagination a.active { background:var(--accent); color:#0d1117; border-color:var(--accent); }
        .load-more-btn { padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.06); color:var(--text); cursor:pointer; }
        .load-more-btn:hover { border-color:rgba(255,183,3,0.4); }
        @media (max-width:600px){ header{padding:24px 16px 8px;} .panel{padding:0 16px 16px;} .grid{padding:0 16px 16px;} .pagination{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <h1>Proveïdors de streaming</h1>
        <a class="back" href="{{ route('home') }}">← Portada</a>
    </header>

    <div class="panel">
        <p class="lead">Explora plataformes disponibles a la regió {{ $region ?? '—' }} i filtra les pel·lícules per on veure-les.</p>

        @if($providers->isEmpty())
            <div class="empty">No s'han pogut carregar els proveïdors. Reintenta-ho més tard.</div>
        @else
            <div class="providers">
                @foreach($providers as $p)
                    <a class="provider-card" href="{{ route('providers', ['provider' => $p['id'], 'type' => $monetization]) }}">
                        @if($p['logo'])
                            <img src="https://image.tmdb.org/t/p/w185{{ $p['logo'] }}" alt="{{ $p['name'] }}">
                        @else
                            <div style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.03);border-radius:14px;font-weight:700;font-size:22px;">{{ strtoupper(substr($p['name'],0,1)) }}</div>
                        @endif
                        <div class="name">{{ $p['name'] }}</div>
                    </a>
                @endforeach
            </div>
        @endif

        <form method="get" action="{{ route('providers') }}">
            <label>Proveïdor
                <select name="provider">
                    <option value="">Selecciona un proveïdor</option>
                    @foreach($providers as $p)
                        <option value="{{ $p['id'] }}" @selected((string)$selectedProvider === (string)$p['id'])>{{ $p['name'] }}</option>
                    @endforeach
                </select>
            </label>
            <label>Tipus
                <select name="type">
                    <option value="">Tots</option>
                    <option value="flatrate" @selected(($monetization ?? '') === 'flatrate')>Subscripció</option>
                    <option value="free" @selected(($monetization ?? '') === 'free')>Gratis</option>
                    <option value="ads" @selected(($monetization ?? '') === 'ads')>Amb anuncis</option>
                    <option value="rent" @selected(($monetization ?? '') === 'rent')>Lloguer</option>
                    <option value="buy" @selected(($monetization ?? '') === 'buy')>Compra</option>
                </select>
            </label>
            <button class="btn" type="submit">Filtrar</button>
        </form>
    </div>

    @if($error)
        <div class="error">{{ $error }}</div>
    @endif

        @if($selectedProvider)
            @if($movies->isEmpty() && !$error)
                <div class="empty">No s'han trobat pel·lícules per a aquest proveïdor.</div>
            @elseif($movies->isNotEmpty())
                <div class="grid" id="movies-grid">
                    @foreach($movies as $movie)
                        <a class="card" href="{{ route('movie.show', ['id' => $movie['id']]) }}">
                            @if($movie['poster'])
                                <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster'] }}" alt="{{ $movie['title'] }}">
                            @else
                            <div class="poster-fallback">{{ strtoupper(substr($movie['title'], 0, 1)) }}</div>
                        @endif
                        <div class="card-body">
                            <div class="card-title">{{ $movie['title'] }}</div>
                            <div class="card-meta">
                                {{ $movie['release_date'] ?? 'Sense data' }} · {{ $movie['vote_average'] ? number_format($movie['vote_average'], 1) : '—' }} ★
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <div class="pagination" id="pager" @if($totalPages <= 1) style="display:none" @endif>
                    <a class="active" href="#" id="page-indicator">{{ $page }} / {{ $totalPages }}</a>
                    <button type="button" class="load-more-btn" id="load-more-btn">Carrega més</button>
                </div>
                <div id="load-more-status" class="empty" style="display:none">Carregant més pel·lícules…</div>
                <div id="load-more-sentinel" style="height:1px;"></div>
            @endif
        @endif
    <script>
        (() => {
            const selectedProvider = "{{ $selectedProvider }}";
            if (!selectedProvider) return;
            let page = Number({{ $page ?? 1 }});
            const totalPages = Number({{ $totalPages ?? 1 }});
            const type = "{{ $monetization }}";
            let loading = false;
            const grid = document.getElementById('movies-grid');
            const status = document.getElementById('load-more-status');
            const indicator = document.getElementById('page-indicator');
            const btn = document.getElementById('load-more-btn');
            const sentinel = document.getElementById('load-more-sentinel');

            async function loadMore() {
                if (loading) return;
                if (page >= totalPages) return;
                loading = true;
                status.style.display = 'block';
                const nextPage = page + 1;
                const url = new URL("{{ route('providers') }}", window.location.origin);
                url.searchParams.set('provider', selectedProvider);
                if (type) url.searchParams.set('type', type);
                url.searchParams.set('page', nextPage);
                url.searchParams.set('ajax', '1');
                try {
                    const res = await fetch(url.toString());
                    if (!res.ok) throw new Error('Response not ok');
                    const data = await res.json();
                    (data.movies || []).forEach(movie => {
                        const a = document.createElement('a');
                        a.className = 'card';
                        a.href = "{{ route('movie.show', ['id' => 'ID']) }}".replace('ID', movie.id || '');
                        const hasPoster = !!movie.poster;
                        a.innerHTML = `
                            ${hasPoster
                                ? `<img src="https://image.tmdb.org/t/p/w342${movie.poster}" alt="${movie.title || ''}">`
                                : `<div class="poster-fallback">${(movie.title || '?').substring(0,1).toUpperCase()}</div>`
                            }
                            <div class="card-body">
                                <div class="card-title">${movie.title || ''}</div>
                                <div class="card-meta">
                                    ${(movie.release_date || 'Sense data')} · ${(movie.vote_average ? Number(movie.vote_average).toFixed(1) : '—')} ★
                                </div>
                            </div>
                        `;
                        grid.appendChild(a);
                    });
                    page = data.page || nextPage;
                    if (indicator) indicator.textContent = `${page} / ${data.total_pages || totalPages}`;
                    if (page >= (data.total_pages || totalPages)) {
                        window.removeEventListener('scroll', onScroll);
                        if (observer) observer.disconnect();
                        status.style.display = 'none';
                    } else {
                        status.style.display = 'none';
                    }
                } catch (e) {
                    status.textContent = 'No s\'han pogut carregar més resultats.';
                } finally {
                    loading = false;
                }
            }

            function onScroll() {
                const scrollPos = window.innerHeight + window.scrollY;
                const threshold = document.body.offsetHeight - 300;
                if (scrollPos >= threshold) loadMore();
            }

            let observer = null;
            if (sentinel && 'IntersectionObserver' in window) {
                observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) loadMore();
                    });
                }, { rootMargin: '200px' });
                observer.observe(sentinel);
            }

            if (btn) {
                btn.addEventListener('click', loadMore);
            }

            if (page < totalPages) {
                window.addEventListener('scroll', onScroll);
            }
        })();
    </script>
</body>
</html>
