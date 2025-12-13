<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerca · Infofilm</title>
    <meta name="description" content="Cercador d'Infofilm: troba pel·lícules, persones i col·leccions amb filtres per gènere, any, idioma i proveïdor.">
    <meta property="og:title" content="Cerca · Infofilm">
    <meta property="og:description" content="Troba ràpidament pel·lícules, persones i col·leccions amb filtres avançats.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        header { padding:32px 24px 12px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
        h1 { margin:0; font-size:clamp(24px,3vw,32px); letter-spacing:-0.02em; }
        .back { padding:10px 12px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.07); font-size:14px; text-decoration:none; color:inherit; }
        .panel { padding:0 24px 24px; }
        form { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:12px; align-items:end; }
        label { display:flex; flex-direction:column; gap:6px; font-size:13px; color:var(--muted); }
        input, select { padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05); color:var(--text); }
        .btn { padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:var(--accent); color:#0d1117; font-weight:600; cursor:pointer; }
        .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:12px; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; }
        .card img { width:100%; aspect-ratio:2/3; object-fit:cover; background:linear-gradient(135deg, rgba(255,183,3,0.15), rgba(13,17,23,0.6)); }
        .card-body { padding:10px; }
        .card-title { font-weight:600; margin:0 0 4px; font-size:15px; }
        .card-meta { color:var(--muted); font-size:12px; }
        .error { padding:24px; color:var(--muted); }
        .pagination { display:flex; gap:8px; margin-top:12px; }
        .pagination a { padding:8px 10px; border-radius:10px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:var(--text); text-decoration:none; }
        .pagination a.active { background:var(--accent); color:#0d1117; border-color:var(--accent); }
        @media (max-width:600px){ header{padding:24px 16px 8px;} .panel{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <h1>Cerca</h1>
        <a class="back" href="{{ route('home') }}">← Portada</a>
    </header>

    <div class="panel">
        <form method="get" action="{{ route('search') }}" id="search-form">
            <label>Text
                <input type="text" name="q" value="{{ $q ?? '' }}" id="search-input" autocomplete="off" placeholder="Cerca pel·lícules, persones, col·leccions">
            </label>
            <label>Tipus
                <select name="type">
                    <option value="movie" @selected(($type ?? 'movie')==='movie')>Pel·lícules</option>
                    <option value="person" @selected(($type ?? '')==='person')>Persones</option>
                    <option value="collection" @selected(($type ?? '')==='collection')>Col·leccions</option>
                </select>
            </label>
            <label>Gènere (pel·lis)
                <input type="text" name="genre" value="{{ $genre ?? '' }}" placeholder="ID TMDB del gènere">
            </label>
            <label>Any (pel·lis)
                <input type="number" name="year" value="{{ $year ?? '' }}" min="1900" max="2100">
            </label>
            <label>Idioma original (pel·lis)
                <input type="text" name="lang" value="{{ $langFilter ?? '' }}" placeholder="ex: en, es, ca">
            </label>
            <label>Proveïdor (pel·lis)
                <input type="text" name="provider" value="{{ $provider ?? '' }}" placeholder="ID de proveïdor TMDB">
            </label>
            <button class="btn" type="submit">Cerca</button>
        </form>

        @if($error ?? false)
            <div class="error">{{ $error }}</div>
        @elseif(isset($q) && $q !== '' && $results->isEmpty())
            <div class="error">Cap resultat.</div>
        @endif

        <div class="grid">
            @foreach($results as $item)
                @if($item['type']==='person')
                    <a class="card" href="{{ route('person.show', ['id' => $item['id']]) }}">
                    @if($item['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $item['poster'] }}" alt="{{ $item['title'] }}" loading="lazy"> @endif
                        <div class="card-body">
                            <div class="card-title">{{ $item['title'] }}</div>
                            <div class="card-meta">Persona</div>
                        </div>
                    </a>
                @elseif($item['type']==='collection')
                    <a class="card" href="{{ route('collection.show', ['id' => $item['id']]) }}">
                    @if($item['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $item['poster'] }}" alt="{{ $item['title'] }}" loading="lazy"> @endif
                        <div class="card-body">
                            <div class="card-title">{{ $item['title'] }}</div>
                            <div class="card-meta">Col·lecció</div>
                        </div>
                    </a>
                @else
                    <a class="card" href="{{ route('movie.show', ['id' => $item['id']]) }}">
                    @if($item['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $item['poster'] }}" alt="{{ $item['title'] }}" loading="lazy"> @endif
                        <div class="card-body">
                            <div class="card-title">{{ $item['title'] }}</div>
                            <div class="card-meta">
                                @if($item['release_date']) {{ \Illuminate\Support\Carbon::parse($item['release_date'])->translatedFormat('Y') }} @endif
                                @if($item['vote_average']) · ★ {{ number_format($item['vote_average'],1) }} @endif
                            </div>
                        </div>
                    </a>
                @endif
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
    </div>

    <script>
        const input = document.getElementById('search-input');
        let controller;
        const list = document.createElement('div');
        list.style.position = 'absolute';
        list.style.background = '#161b22';
        list.style.border = '1px solid rgba(255,255,255,0.1)';
        list.style.borderRadius = '10px';
        list.style.padding = '6px 0';
        list.style.width = input.offsetWidth + 'px';
        list.style.display = 'none';
        list.style.zIndex = '10';
        list.style.boxShadow = '0 10px 30px rgba(0,0,0,0.35)';
        input.parentElement.style.position = 'relative';
        input.parentElement.appendChild(list);

        input.addEventListener('input', async () => {
            const q = input.value.trim();
            if (q.length < 3) { list.style.display = 'none'; return; }
            if (controller) controller.abort();
            controller = new AbortController();
            try {
                const res = await fetch(`{{ route('search.suggest') }}?q=${encodeURIComponent(q)}`, { signal: controller.signal });
                const data = await res.json();
                list.innerHTML = '';
                data.forEach(item => {
                    const a = document.createElement('a');
                    a.href = item.type === 'person' ? `{{ url('/people') }}/${item.id}`
                        : item.type === 'collection' ? `{{ url('/collections') }}/${item.id}`
                        : `{{ url('/movies') }}/${item.id}`;
                    a.textContent = `${item.title} (${item.type})`;
                    a.style.display = 'block';
                    a.style.padding = '6px 10px';
                    a.style.color = 'var(--text)';
                    a.style.textDecoration = 'none';
                    a.addEventListener('mouseover', () => a.style.background = 'rgba(255,255,255,0.06)');
                    a.addEventListener('mouseout', () => a.style.background = 'transparent');
                    list.appendChild(a);
                });
                list.style.display = data.length ? 'block' : 'none';
            } catch (e) {
                list.style.display = 'none';
            }
        });

        document.addEventListener('click', (e) => {
            if (!list.contains(e.target) && e.target !== input) {
                list.style.display = 'none';
            }
        });
    </script>
</body>
</html>
