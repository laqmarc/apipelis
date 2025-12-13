# Roadmap Infofilm

## Sprint 0 – Fonaments
- Corregir codificació UTF-8 a vistes i textos; moure literals a `lang/ca`.
- Corregir `search()` perquè apliqui filtres amb o sense text; afegir proves.
- Introduir layout Blade compartit amb Vite/Tailwind i metadades OG bàsiques.
- Caché curt per peticions TMDB repetides i maneig centralitzat d’errors.

## Sprint 1 – Cercador avançat i favorits
- Filtres combinats: gènere, any, llengua original, país producció, rang puntuació, rang durada, només streaming a la meva regió.
- Desa cerques favorites (sessió/DB simple) i “recarrega cerca”.
- Millora `search.suggest` per preomplir filtres; proves de combinacions.
- Badges ràpids a targetes: `+18`, `>8.0`, `té streaming`.

## Sprint 2 – Rànquings i context
- Rutes: `/ranking/weekly`, `/ranking/year`, `/ranking/today`, `/ranking/upcoming`.
- Fitxa de pel·lícula: línia temporal d’estrenes per país, graella de certificacions amb significat, comparador pressupost vs ingressos (ROI).
- Sitemap bàsic i metadades OG/Twitter amb imatge de fallback.

## Sprint 3 – Col·leccions i persones
- Pàgina “Col·leccions destacades” per popularitat + ordre cronològic dins de la saga.
- Filmografia filtrable per rol (actor/director/productor), ordre per any o popularitat.
- Xarxa de col·laboracions: top company actors/directors amb #projectes compartits.
- Proves d’agrupació/ordenació de crèdits.

## Sprint 4 – Keywords i exploració
- Núvol de keywords més freqüents (trending/popular) cachejat; mapa keyword → gènere.
- “Si t’agrada X”: similars + recomanacions + keywords compartides en sliders.
- Mode exploració: carrossels per gènere, “Cinema en català” (lang=ca), “Indie >7”, “Òscars/Palmes” (keywords predefinides).

## Sprint 5 – Proveïdors i disponibilitat
- Pàgina de proveïdors amb selector de regió i populars per proveïdor (`with_watch_providers`, `watch_region`).
- Alertes de disponibilitat a targetes i watchlist: streaming/lloguer/compra.
- Watchlist local (sessió/DB) amb estats “per veure” / “vista”; botó ràpid a targetes.

## Sprint 6 – Multimèdia i UX
- Galeria avançada: mosaic, zoom/teclat, filtre per tipus de vídeo (tràiler/featurette).
- Accessibilitat (focus/ARIA) i skeleton loading per carrossels.

## QA contínua
- Ampliar suite de tests a cada sprint (feature + smoke de rutes).
- Monitoritzar límits de rate TMDB; ajustar caché i backoff.
