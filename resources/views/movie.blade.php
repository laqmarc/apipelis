<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $movie['title'] ?? 'Pel·lícula' }} · Infofilm</title>
    <meta property="og:title" content="{{ $movie['title'] ?? 'Pel·lícula' }} · Infofilm">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($movie['overview'] ?? '', 150) }}">
    @if(!empty($movie['poster']))
        <meta property="og:image" content="https://image.tmdb.org/t/p/w780{{ $movie['poster'] }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="https://image.tmdb.org/t/p/w780{{ $movie['poster'] }}">
    @endif
    <meta property="og:type" content="video.movie">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/owl/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/owl/owl.theme.default.min.css') }}">
    <style>
        :root { --bg:#0d1117; --card:#161b22; --accent:#ffb703; --text:#e6edf3; --muted:#9da7b3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Space Grotesk",system-ui,-apple-system,sans-serif; background: radial-gradient(circle at 20% 20%, rgba(255,183,3,0.08), transparent 30%), radial-gradient(circle at 80% 0%, rgba(3,169,244,0.08), transparent 25%), var(--bg); color:var(--text); }
        a { color: inherit; text-decoration: none; }
        header { padding:32px 24px 12px; display:flex; align-items:center; gap:12px; }
        .back { padding:10px 12px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.07); font-size:14px; }
        .hero { display:grid; grid-template-columns:minmax(260px,320px) 1fr; gap:24px; padding:0 24px 24px; }
        .poster { width:100%; border-radius:16px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.45); background: linear-gradient(135deg, rgba(255,183,3,0.12), rgba(13,17,23,0.7)); }
        .poster img { width:100%; display:block; }
        h1 { margin:0 0 8px; font-size:clamp(26px,3vw,34px); letter-spacing:-0.02em; }
        .tagline { margin:0 0 12px; color:var(--muted); font-size:16px; }
        .meta { display:flex; flex-wrap:wrap; gap:10px; color:var(--muted); font-size:14px; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; background:rgba(255,183,3,0.12); color:#ffd166; font-size:13px; font-weight:600; }
        .pill.red { background:rgba(239,68,68,0.14); color:#fca5a5; }
        .section { padding:0 24px 24px; }
        .section h2 { margin:0 0 12px; font-size:18px; }
        .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; }
        .info-item { background:var(--card); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:12px; }
        .label { color:var(--muted); font-size:12px; letter-spacing:0.02em; text-transform:uppercase; }
        .value { margin-top:4px; font-weight:600; }
        .chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:4px; }
        .chip { padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.06); font-size:13px; }
        .providers { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px; }
        .provider-card { background:var(--card); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:10px; }
        .provider-list { display:flex; flex-wrap:wrap; gap:8px; margin-top:6px; }
        .provider-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:10px; background:rgba(255,255,255,0.06); font-size:13px; }
        .provider-chip img { width:28px; height:28px; border-radius:6px; object-fit:cover; }
        .reviews { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:12px; }
        .review-card { background:var(--card); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:12px; display:flex; flex-direction:column; gap:8px; }
        .review-author { font-weight:600; }
        .review-content { color:var(--muted); font-size:14px; line-height:1.5; }
        .videos { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:12px; }
        .video-card iframe { width:100%; aspect-ratio:16/9; border:0; border-radius:12px; }
        .video-body { padding:8px 4px 0; }
        .recs { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
        .rec-card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
        .rec-card img { width:100%; display:block; }
        .rec-body { padding:10px; }
        .cast-card { background:var(--card); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:8px; display:flex; flex-direction:column; gap:6px; text-align:center; }
        .cast-name { font-weight:600; }
        .cast-role { color:var(--muted); font-size:13px; margin:0; }
        .owl-nav button.owl-prev,
        .owl-nav button.owl-next { background:rgba(255,255,255,0.06); color:var(--text); border:1px solid rgba(255,255,255,0.1); border-radius:10px; width:36px; height:32px; margin:0 4px; }
        .owl-nav button.owl-prev:hover,
        .owl-nav button.owl-next:hover { border-color:rgba(255,183,3,0.4); }
        .owl-theme .owl-dots { display:none; }
        .modal { position:fixed; inset:0; background:rgba(0,0,0,0.85); display:none; align-items:center; justify-content:center; z-index:1000; }
        .modal.open { display:flex; }
        .modal img { max-width:90vw; max-height:90vh; border-radius:12px; }
        .modal-close { position:absolute; top:16px; right:16px; background:rgba(0,0,0,0.6); color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:50%; width:36px; height:36px; display:grid; place-items:center; cursor:pointer; }
        @media (max-width:820px){ .hero{grid-template-columns:1fr;} header{padding:24px 16px 8px;} .section{padding:0 16px 16px;} }
    </style>
</head>
<body>
    <header>
        <a class="back" href="{{ route('home') }}">← Tornar</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif(!$movie)
        <div class="error">No s'ha trobat la pel·lícula.</div>
    @else
        <section class="hero">
            <div class="poster">
                @if($movie['poster'])
                    <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster'] }}" alt="Cartell de {{ $movie['title'] }}" loading="lazy">
                @elseif($movie['backdrop'])
                    <img src="https://image.tmdb.org/t/p/w780{{ $movie['backdrop'] }}" alt="Fons de {{ $movie['title'] }}" loading="lazy">
                @endif
            </div>
            <div>
                <h1>{{ $movie['title'] }}</h1>
                @if($movie['tagline'])
                    <p class="tagline">“{{ $movie['tagline'] }}”</p>
                @endif
                <div class="meta">
                    @if($movie['release_date']) <span>Estrena: {{ \Illuminate\Support\Carbon::parse($movie['release_date'])->translatedFormat('d M Y') }}</span> @endif
                    @if($movie['runtime']) <span>Durada: {{ $movie['runtime'] }} min</span> @endif
                    @if(!empty($movie['genres'])) <span>Gèneres: {{ implode(', ', $movie['genres']) }}</span> @endif
                    @if($movie['vote_average']) <span class="pill">★ {{ number_format($movie['vote_average'], 1) }} ({{ $movie['vote_count'] }} vots)</span> @endif
                    @if($movie['adult']) <span class="pill red">+18</span> @endif
                    @if($movie['video']) <span class="pill">▶ Vídeo</span> @endif
                </div>
                @if($movie['overview'])
                    <div style="margin-top:14px; color: var(--muted); line-height:1.6;">
                        {{ $movie['overview'] }}
                    </div>
                @endif
            </div>
        </section>

        <section class="section">
            <div class="info-grid">
                @if(!empty($movie['spoken_languages']))
                    <div class="info-item">
                        <div class="label">Idiomes</div>
                        <div class="chips">@foreach($movie['spoken_languages'] as $lang)<span class="chip">{{ $lang }}</span>@endforeach</div>
                    </div>
                @endif
                @if(!empty($movie['alternative_titles']))
                    <div class="info-item">
                        <div class="label">Títols alternatius</div>
                        <div class="chips">@foreach($movie['alternative_titles'] as $alt)<span class="chip">{{ $alt['title'] }}{{ $alt['country'] ? ' · ' . $alt['country'] : '' }}</span>@endforeach</div>
                    </div>
                @endif
                @if(!empty($movie['translations']))
                    <div class="info-item">
                        <div class="label">Traduccions</div>
                        <div class="chips">@foreach($movie['translations'] as $tr)<span class="chip" title="{{ $tr['title'] ?: 'Sense títol traduït' }}">{{ strtoupper($tr['language']) }}{{ $tr['country'] ? ' · ' . $tr['country'] : '' }} — {{ $tr['name'] ?: $tr['english_name'] }}</span>@endforeach</div>
                    </div>
                @endif
                @if(!empty($movie['production_countries']))
                    <div class="info-item">
                        <div class="label">Països d'origen</div>
                        <div class="chips">@foreach($movie['production_countries'] as $country)<span class="chip">{{ $country }}</span>@endforeach</div>
                    </div>
                @endif
                @if(!empty($movie['production_companies']))
                    <div class="info-item">
                        <div class="label">Productores</div>
                        <div class="chips">
                            @foreach($movie['production_companies'] as $company)
                                <span class="chip">
                                    @if($company['logo']) <img src="https://image.tmdb.org/t/p/w45{{ $company['logo'] }}" alt="{{ $company['name'] }}" style="height:20px; vertical-align:middle; margin-right:6px;" loading="lazy"> @endif
                                    <a href="{{ route('company.show', ['id' => $company['id']]) }}">{{ $company['name'] }}</a>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if(!empty($movie['networks']))
                    <div class="info-item">
                        <div class="label">Xarxes</div>
                        <div class="chips">
                            @foreach($movie['networks'] as $network)
                                <span class="chip">
                                    @if($network['logo']) <img src="https://image.tmdb.org/t/p/w45{{ $network['logo'] }}" alt="{{ $network['name'] }}" style="height:20px; vertical-align:middle; margin-right:6px;" loading="lazy"> @endif
                                    <a href="{{ route('network.show', ['id' => $network['id']]) }}">{{ $network['name'] }}</a>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if($movie['status'])
                    <div class="info-item"><div class="label">Estatus</div><div class="value">{{ $movie['status'] }}</div></div>
                @endif
                @if($movie['original_title'])
                    <div class="info-item"><div class="label">Títol original</div><div class="value">{{ $movie['original_title'] }}</div></div>
                @endif
                @if($movie['original_language'])
                    <div class="info-item"><div class="label">Llengua original</div><div class="value">{{ strtoupper($movie['original_language']) }}</div></div>
                @endif
                @if($movie['budget'])
                    <div class="info-item"><div class="label">Pressupost</div><div class="value">${{ number_format($movie['budget'] / 1_000_000, 1) }} M</div></div>
                @endif
                @if($movie['revenue'])
                    <div class="info-item"><div class="label">Ingressos</div><div class="value">${{ number_format($movie['revenue'] / 1_000_000, 1) }} M</div></div>
                @endif
                @if($movie['popularity'])
                    <div class="info-item"><div class="label">Popularitat</div><div class="value">{{ number_format($movie['popularity'], 0) }}</div></div>
                @endif
                @if($movie['collection'])
                    <div class="info-item">
                        <div class="label">Col·lecció</div>
                        <div class="value"><a href="{{ route('collection.show', ['id' => $movie['collection']['id']]) }}">{{ $movie['collection']['name'] }}</a></div>
                    </div>
                @endif
                @if(!empty($movie['keywords']))
                    <div class="info-item">
                        <div class="label">Paraules clau</div>
                        <div class="chips">@foreach($movie['keywords'] as $kw)<span class="chip"><a href="{{ route('keyword.show', ['id' => $kw['id']]) }}">{{ $kw['name'] }}</a></span>@endforeach</div>
                    </div>
                @endif
                @if($movie['homepage'])
                    <div class="info-item"><div class="label">Web oficial</div><div class="value"><a href="{{ $movie['homepage'] }}" target="_blank" rel="noreferrer">Obrir</a></div></div>
                @endif
                @if($movie['imdb_id'])
                    <div class="info-item"><div class="label">IMDb</div><div class="value"><a href="https://www.imdb.com/title/{{ $movie['imdb_id'] }}" target="_blank" rel="noreferrer">{{ $movie['imdb_id'] }}</a></div></div>
                @endif
                @if($movie['wikidata_id'] || $movie['facebook_id'] || $movie['instagram_id'] || $movie['twitter_id'])
                    <div class="info-item">
                        <div class="label">Enllaços externs</div>
                        <div class="chips">
                            @if($movie['wikidata_id']) <span class="chip"><a href="https://www.wikidata.org/wiki/{{ $movie['wikidata_id'] }}" target="_blank" rel="noreferrer">Wikidata</a></span> @endif
                            @if($movie['facebook_id']) <span class="chip"><a href="https://www.facebook.com/{{ $movie['facebook_id'] }}" target="_blank" rel="noreferrer">Facebook</a></span> @endif
                            @if($movie['instagram_id']) <span class="chip"><a href="https://www.instagram.com/{{ $movie['instagram_id'] }}" target="_blank" rel="noreferrer">Instagram</a></span> @endif
                            @if($movie['twitter_id']) <span class="chip"><a href="https://twitter.com/{{ $movie['twitter_id'] }}" target="_blank" rel="noreferrer">Twitter</a></span> @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if(!empty($movie['release_dates']))
            <section class="section">
                <h2>Dates d'estrena</h2>
                <div class="info-grid">
                    @foreach($movie['release_dates'] as $rel)
                        <div class="info-item">
                            <div class="label">{{ $rel['country'] ?: '—' }}</div>
                            <div class="value">
                                @if($rel['release_date'])
                                    {{ \Illuminate\Support\Carbon::parse($rel['release_date'])->translatedFormat('d M Y') }}
                                @else
                                    Sense data
                                @endif
                            </div>
                            @if($rel['certification']) <div class="pill" style="margin-top:6px; display:inline-flex;">{{ $rel['certification'] }}</div> @endif
                            @if($rel['certification_meaning']) <div style="color:var(--muted); font-size:12px; margin-top:4px;">{{ $rel['certification_meaning'] }}</div> @endif
                            @if($rel['type']) <div style="color:var(--muted); font-size:12px; margin-top:4px;">Tipus {{ $rel['type'] }}</div> @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($movie['watch_providers']['flatrate']) || !empty($movie['watch_providers']['rent']) || !empty($movie['watch_providers']['buy']))
            <section class="section">
                <h2>On veure-la ({{ $movie['region'] ?? '—' }})</h2>
                @if($movie['watch_providers']['link'])
                    <p style="margin:4px 0 12px; color:var(--muted); font-size:13px;">
                        <a href="{{ $movie['watch_providers']['link'] }}" target="_blank" rel="noreferrer" style="color:var(--accent);">Enllaç TMDB/JustWatch</a>
                    </p>
                @endif
                <div class="providers">
                    @foreach(['flatrate' => 'Streaming', 'rent' => 'Lloguer', 'buy' => 'Compra'] as $key => $label)
                        @if(!empty($movie['watch_providers'][$key]))
                            <div class="provider-card">
                                <div class="label" style="margin-bottom:6px;">{{ $label }}</div>
                                <div class="provider-list">
                                    @foreach($movie['watch_providers'][$key] as $prov)
                                        <span class="provider-chip">
                                            @if($prov['logo']) <img src="https://image.tmdb.org/t/p/w92{{ $prov['logo'] }}" alt="{{ $prov['name'] }}" loading="lazy"> @endif
                                            <span>{{ $prov['name'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <p style="color:var(--muted); font-size:12px; margin-top:10px;">Dades de proveïdors gràcies a TMDB / JustWatch.</p>
            </section>
        @endif

        @if(!empty($movie['cast']))
            <section class="section">
                <h2>Repartiment destacat</h2>
                <div class="owl-carousel owl-theme cast-carousel">
                    @foreach($movie['cast'] as $cast)
                        <a class="cast-card" href="{{ route('person.show', ['id' => $cast['id']]) }}">
                            @if($cast['profile']) <img src="https://image.tmdb.org/t/p/w185{{ $cast['profile'] }}" alt="Foto de {{ $cast['name'] }}" style="width:100%; border-radius:10px;" loading="lazy"> @endif
                            <div class="cast-name">{{ $cast['name'] }}</div>
                            <p class="cast-role">{{ $cast['character'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($movie['crew']))
            <section class="section">
                <h2>Equip tècnic</h2>
                <div class="owl-carousel owl-theme crew-carousel">
                    @foreach($movie['crew'] as $crew)
                        <a class="cast-card" href="{{ route('person.show', ['id' => $crew['id']]) }}">
                            @if($crew['profile']) <img src="https://image.tmdb.org/t/p/w185{{ $crew['profile'] }}" alt="Foto de {{ $crew['name'] }}" style="width:100%; border-radius:10px;" loading="lazy"> @endif
                            <div class="cast-name">{{ $crew['name'] }}</div>
                            <p class="cast-role">{{ $crew['job'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($movie['videos']))
            <section class="section">
                <h2>Vídeos</h2>
                <div class="videos">
                    @foreach($movie['videos'] as $video)
                        <div class="video-card">
                            @if($video['key']) <iframe src="https://www.youtube.com/embed/{{ $video['key'] }}" title="{{ $video['name'] }}" allowfullscreen></iframe> @endif
                            <div class="video-body">
                                <div style="font-weight:600;">{{ $video['name'] }}</div>
                                <div style="color:var(--muted); font-size:13px; margin-top:4px;">
                                    {{ $video['type'] }} @if($video['official']) · Oficial @endif
                                    @if($video['published_at']) · {{ \Illuminate\Support\Carbon::parse($video['published_at'])->translatedFormat('d M Y') }} @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($movie['reviews']))
            <section class="section">
                <h2>Crítiques</h2>
                <div class="reviews">
                    @foreach($movie['reviews'] as $rev)
                        <div class="review-card">
                            <div class="review-author">
                                {{ $rev['author'] ?: 'Anònim' }}
                                @if(!is_null($rev['rating'])) <span class="pill" style="margin-left:6px;">★ {{ number_format($rev['rating'], 1) }}</span> @endif
                            </div>
                            @if($rev['created_at']) <div style="color:var(--muted); font-size:12px;">{{ \Illuminate\Support\Carbon::parse($rev['created_at'])->translatedFormat('d M Y') }}</div> @endif
                            <div class="review-content">{{ \Illuminate\Support\Str::limit($rev['content'], 400) }}</div>
                            @if($rev['url'])
                                <a href="{{ $rev['url'] }}" target="_blank" rel="noreferrer" style="font-size:13px; color:var(--accent);">Llegir més →</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @php
            $images = collect($movie['backdrops'] ?? [])->map(function($img){ return ['path' => $img['path'], 'width' => $img['width'], 'height' => $img['height']]; })
                ->merge(collect($movie['posters'] ?? [])->map(function($img){ return ['path' => $img['path'], 'width' => $img['width'], 'height' => $img['height']]; }));
        @endphp
        @if($images->isNotEmpty())
            <section class="section">
                <h2>Imatges</h2>
                <div class="owl-carousel owl-theme images-carousel">
                    @foreach($images as $img)
                        <div class="img-card js-image" data-full="https://image.tmdb.org/t/p/original{{ $img['path'] }}">
                            <img src="https://image.tmdb.org/t/p/w780{{ $img['path'] }}" alt="Imatge de {{ $movie['title'] }}" loading="lazy">
                            @if($img['width'] && $img['height']) <div class="img-meta">{{ $img['width'] }}×{{ $img['height'] }}</div> @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($movie['similar']))
            <section class="section">
                <h2>Pel·lícules similars</h2>
                <div class="recs">
                    @foreach($movie['similar'] as $sim)
                        <a class="rec-card" href="{{ route('movie.show', ['id' => $sim['id']]) }}">
                            @if($sim['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $sim['poster'] }}" alt="Cartell de {{ $sim['title'] }}" loading="lazy"> @endif
                            <div class="rec-body">
                                <div style="font-weight:600;">{{ $sim['title'] }}</div>
                                @if($sim['vote_average']) <div style="color:var(--muted); font-size:13px; margin-top:4px;">★ {{ number_format($sim['vote_average'], 1) }}</div> @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($movie['recommendations']))
            <section class="section">
                <h2>També et pot agradar</h2>
                <div class="recs">
                    @foreach($movie['recommendations'] as $rec)
                        <a class="rec-card" href="{{ route('movie.show', ['id' => $rec['id']]) }}">
                            @if($rec['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $rec['poster'] }}" alt="Cartell de {{ $rec['title'] }}" loading="lazy"> @endif
                            <div class="rec-body">
                                <div style="font-weight:600;">{{ $rec['title'] }}</div>
                                @if($rec['vote_average']) <div style="color:var(--muted); font-size:13px; margin-top:4px;">★ {{ number_format($rec['vote_average'], 1) }}</div> @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif

    <div class="modal" id="image-modal">
        <button class="modal-close" aria-label="Tancar">×</button>
        <img src="" alt="Imatge gran">
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/owl/owl.carousel.min.js') }}"></script>
    <script>
        $(function () {
            $('.cast-carousel').owlCarousel({ loop:false, margin:12, nav:true, dots:false, responsive:{0:{items:2},600:{items:4},900:{items:10}} });
            $('.crew-carousel').owlCarousel({ loop:false, margin:12, nav:true, dots:false, responsive:{0:{items:2},600:{items:4},900:{items:10}} });
            $('.images-carousel').owlCarousel({ loop:false, margin:12, nav:true, dots:false, responsive:{0:{items:2},600:{items:3},900:{items:5}} });

            const modal = document.getElementById('image-modal');
            const modalImg = modal.querySelector('img');
            const closeBtn = modal.querySelector('.modal-close');

            document.querySelectorAll('.js-image').forEach(el => {
                el.addEventListener('click', () => {
                    const src = el.dataset.full;
                    if (!src) return;
                    modalImg.src = src;
                    modal.classList.add('open');
                });
            });

            const closeModal = () => {
                modal.classList.remove('open');
                modalImg.src = '';
            };
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('open')) closeModal(); });
        });
    </script>
</body>
</html>
