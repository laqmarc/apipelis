<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $person['name'] ?? 'Persona' }} · Infofilm</title>
    <meta property="og:title" content="{{ $person['name'] ?? 'Persona' }} · Infofilm">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($person['biography'] ?? '', 150) }}">
    @if(!empty($person['profile']))
        <meta property="og:image" content="https://image.tmdb.org/t/p/w780{{ $person['profile'] }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="https://image.tmdb.org/t/p/w780{{ $person['profile'] }}">
    @endif
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
        .hero { display:grid; grid-template-columns:minmax(240px,300px) 1fr; gap:24px; padding:0 24px 24px; }
        .profile { width:100%; border-radius:16px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.45); background: linear-gradient(135deg, rgba(255,183,3,0.12), rgba(13,17,23,0.7)); }
        .profile img { width:100%; display:block; }
        h1 { margin:0 0 8px; font-size:clamp(26px,3vw,34px); letter-spacing:-0.02em; }
        .meta { display:flex; flex-wrap:wrap; gap:10px; color:var(--muted); font-size:14px; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; background:rgba(255,183,3,0.12); color:#ffd166; font-size:13px; font-weight:600; }
        .section { padding:0 24px 24px; }
        .section h2 { margin:0 0 12px; font-size:18px; }
        .chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:4px; }
        .chip { padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.06); font-size:13px; }
        .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; }
        .info-item { background:var(--card); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:12px; }
        .label { color:var(--muted); font-size:12px; letter-spacing:0.02em; text-transform:uppercase; }
        .value { margin-top:4px; font-weight:600; }
        .card { background:var(--card); border:1px solid rgba(255,255,255,0.05); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
        .card img { width:100%; display:block; }
        .card-body { padding:10px; }
        .card-title { font-weight:600; margin:0 0 4px; }
        .card-meta { color:var(--muted); font-size:13px; }
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
        <a class="back" href="{{ url()->previous() }}">← Tornar</a>
    </header>

    @if($error ?? false)
        <div class="error">{{ $error }}</div>
    @elseif(!$person)
        <div class="error">No s'ha trobat la persona.</div>
    @else
        <section class="hero">
            <div class="profile">
                @if($person['profile'])
                    <img src="https://image.tmdb.org/t/p/w500{{ $person['profile'] }}" alt="Foto de {{ $person['name'] }}">
                @endif
            </div>
            <div>
                <h1>{{ $person['name'] }}</h1>
                <div class="meta">
                    @if($person['known_for_department']) <span>{{ $person['known_for_department'] }}</span> @endif
                    @if($person['gender']) <span>{{ $person['gender'] }}</span> @endif
                    @if($person['popularity']) <span class="pill">Popularitat {{ number_format($person['popularity'], 0) }}</span> @endif
                </div>
                <div class="info-grid" style="margin-top:12px;">
                    @if($person['birthday']) <div class="info-item"><div class="label">Naixement</div><div class="value">{{ \Illuminate\Support\Carbon::parse($person['birthday'])->translatedFormat('d M Y') }}</div></div> @endif
                    @if($person['deathday']) <div class="info-item"><div class="label">Defunció</div><div class="value">{{ \Illuminate\Support\Carbon::parse($person['deathday'])->translatedFormat('d M Y') }}</div></div> @endif
                    @if($person['place_of_birth']) <div class="info-item"><div class="label">Lloc de naixement</div><div class="value">{{ $person['place_of_birth'] }}</div></div> @endif
                    @if($person['homepage']) <div class="info-item"><div class="label">Web</div><div class="value"><a href="{{ $person['homepage'] }}" target="_blank" rel="noreferrer">Obrir</a></div></div> @endif
                    @if($person['imdb_id']) <div class="info-item"><div class="label">IMDb</div><div class="value"><a href="https://www.imdb.com/name/{{ $person['imdb_id'] }}" target="_blank" rel="noreferrer">{{ $person['imdb_id'] }}</a></div></div> @endif
                </div>
                @if(!empty($person['also_known_as']))
                    <div style="margin-top:10px;">
                        <div class="label">També conegut/da com</div>
                        <div class="chips">@foreach($person['also_known_as'] as $aka)<span class="chip">{{ $aka }}</span>@endforeach</div>
                    </div>
                @endif
            </div>
        </section>

        @if($person['biography'])
            <section class="section">
                <h2>Biografia</h2>
                <p style="color:var(--muted); line-height:1.6; margin:0;">{{ $person['biography'] }}</p>
            </section>
        @endif

        @if($person['instagram_id'] || $person['twitter_id'] || $person['facebook_id'] || $person['wikidata_id'] || $person['tiktok_id'] || $person['youtube_id'])
            <section class="section">
                <h2>Enllaços</h2>
                <div class="chips">
                    @if($person['instagram_id']) <span class="chip"><a href="https://www.instagram.com/{{ $person['instagram_id'] }}" target="_blank" rel="noreferrer">Instagram</a></span> @endif
                    @if($person['twitter_id']) <span class="chip"><a href="https://twitter.com/{{ $person['twitter_id'] }}" target="_blank" rel="noreferrer">Twitter</a></span> @endif
                    @if($person['facebook_id']) <span class="chip"><a href="https://www.facebook.com/{{ $person['facebook_id'] }}" target="_blank" rel="noreferrer">Facebook</a></span> @endif
                    @if($person['tiktok_id']) <span class="chip"><a href="https://www.tiktok.com/@{{ $person['tiktok_id'] }}" target="_blank" rel="noreferrer">TikTok</a></span> @endif
                    @if($person['youtube_id']) <span class="chip"><a href="https://www.youtube.com/{{ $person['youtube_id'] }}" target="_blank" rel="noreferrer">YouTube</a></span> @endif
                    @if($person['wikidata_id']) <span class="chip"><a href="https://www.wikidata.org/wiki/{{ $person['wikidata_id'] }}" target="_blank" rel="noreferrer">Wikidata</a></span> @endif
                </div>
            </section>
        @endif

        @if(!empty($person['profiles']))
            <section class="section">
                <h2>Galeria</h2>
                <div class="owl-carousel owl-theme images-carousel">
                    @foreach($person['profiles'] as $img)
                        <div class="card js-image" data-full="https://image.tmdb.org/t/p/original{{ $img['path'] }}">
                            <img src="https://image.tmdb.org/t/p/w500{{ $img['path'] }}" alt="Foto de {{ $person['name'] }}">
                            @if($img['width'] && $img['height']) <div class="card-meta" style="padding:8px;">{{ $img['width'] }}×{{ $img['height'] }}</div> @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($person['tagged_images']))
            <section class="section">
                <h2>Imatges etiquetades</h2>
                <div class="owl-carousel owl-theme images-carousel">
                    @foreach($person['tagged_images'] as $img)
                        <div class="card js-image" data-full="https://image.tmdb.org/t/p/original{{ $img['path'] }}">
                            <img src="https://image.tmdb.org/t/p/w500{{ $img['path'] }}" alt="Imatge etiquetada de {{ $person['name'] }}">
                            @if($img['width'] && $img['height']) <div class="card-meta" style="padding:8px;">{{ $img['width'] }}×{{ $img['height'] }}</div> @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($person['cast_credits']))
            <section class="section">
                <h2>Interpretació (cine)</h2>
                <div class="owl-carousel owl-theme cast-carousel">
                    @foreach($person['cast_credits'] as $c)
                        <a class="card" href="{{ route('movie.show', ['id' => $c['id']]) }}">
                            @if($c['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $c['poster'] }}" alt="Cartell de {{ $c['title'] }}"> @endif
                            <div class="card-body">
                                <div class="card-title">{{ $c['title'] }}</div>
                                <div class="card-meta">
                                    @if($c['release_date']) {{ \Illuminate\Support\Carbon::parse($c['release_date'])->translatedFormat('Y') }} @endif
                                    @if(!empty($c['characters'])) · {{ implode(' · ', $c['characters']) }} @endif
                                    @if($c['vote_average']) · ★ {{ number_format($c['vote_average'], 1) }} @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($person['crew_credits']))
            <section class="section">
                <h2>Equip tècnic (cine)</h2>
                <div class="owl-carousel owl-theme crew-carousel">
                    @foreach($person['crew_credits'] as $c)
                        <a class="card" href="{{ route('movie.show', ['id' => $c['id']]) }}">
                            @if($c['poster']) <img src="https://image.tmdb.org/t/p/w342{{ $c['poster'] }}" alt="Cartell de {{ $c['title'] }}"> @endif
                            <div class="card-body">
                                <div class="card-title">{{ $c['title'] }}</div>
                                <div class="card-meta">
                                    @if($c['release_date']) {{ \Illuminate\Support\Carbon::parse($c['release_date'])->translatedFormat('Y') }} @endif
                                    @if(!empty($c['jobs'])) · {{ implode(' · ', $c['jobs']) }} @endif
                                    @if($c['vote_average']) · ★ {{ number_format($c['vote_average'], 1) }} @endif
                                </div>
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

            const closeModal = () => { modal.classList.remove('open'); modalImg.src = ''; };
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('open')) closeModal(); });
        });
    </script>
</body>
</html>
