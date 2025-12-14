# Infofilm (Laravel + TMDB)

Aplicació Laravel en català per descobrir pel·lícules, persones, col·leccions i paraules clau a partir de l’API de TheMovieDB (TMDB). Interfície Blade amb dades enriquides (fitxa, crítiques, imatges, recomanacions) i cerca avançada.

## Configuració ràpida

1. Requisits: PHP 8.2+, Composer, Node.js 18+ i npm.
2. Variables d’entorn (afegiu-les a `.env`):
   - `TMDB_ACCESS_TOKEN` (token v4 obligatori) i, opcionalment, `TMDB_API_KEY`.
   - `TMDB_BASE_URL` (per defecte `https://api.themoviedb.org/3`), `TMDB_LANGUAGE` (per defecte `ca-ES`) i `TMDB_REGION` si voleu forçar la regió.
3. Instal·lació: `composer install` i `npm install`.
4. Execució:
   - Backend: `php artisan serve`
   - Frontend: `npm run dev` (o `npm run build` per producció).

## Funcionalitats

- Inici `/`: trending diari de pel·lícules amb filtres de llengua i regió; carrega també cartelleres (`now_playing`), populars, millor valorades i pròximes estrenes; mostra missatge d’error si TMDB falla.
- Fitxa de pel·lícula `/movies/{id}`: dades completes (sinopsi, tagline, gèneres, durada, col·lecció, pressupost/ingressos, idiomes, webs externes), crèdits, imatges, vídeos de YouTube, recomanacions/similars, títols alternatius i traduccions, dates d’estrena amb certificacions, proves de visualització per regió i crítiques (fa fallback a anglès si no hi ha ressenyes en català).
- Persones `/people/{id}`: biografia, alias, perfils socials, imatges, i filmografia agrupada per personatge/lloc de treball per evitar duplicats.
- Persones populars `/people/popular`: llista paginada amb comptador de pàgines.
- Col·leccions `/collections/{id}`: parts ordenades per popularitat, imatges i traduccions.
- Companyies i xarxes `/companies/{id}`, `/networks/{id}`: dades bàsiques, noms alternatius i logotips.
- Proveïdors `/providers`: catàleg de plataformes segons regió i filtre de pel·lícules per proveïdor i tipus (subscripció, gratis, anuncis, lloguer, compra) amb scroll infinit.
- Paraules clau `/keywords/{id}` i índex `/keywords`: llista de pel·lícules per keyword i recopilació de keywords a partir de populars + trending (sense duplicats, fins a 100).
- Cerca `/search`: cerca de pel·lícules (amb filtres de gènere, any, proveïdor i idioma original), persones o col·leccions; pàginació i ordenació per popularitat.
- Suggeriments `/search/suggest`: resposta JSON amb els primers resultats multi-search per autocompletar.
- Vistes: plantilles Blade a `resources/views` (trending, movie, person, collection, company, network, keyword, keywords, search, people) amb components compartits.

## Tests

- Execució: `php artisan test` o `composer test` (ja netegen la configuració amb `php artisan config:clear`). Les crides a TMDB es faken amb `Http::fake`, així que no calen tokens per als tests i no hi ha peticions externes.
- Cobertura principal (`tests/Feature/TmdbPagesTest.php`):
  - Inici: renderitza dades trending i mostra l’error si TMDB respon 500.
  - Pel·lícula: carrega la fitxa completa, comprova el fallback de crítiques en anglès i la tolerància a errors 500.
  - Persones: carrega biografia/imatges, agrupa personatges/feines i manté la pàgina si TMDB falla.
  - Col·leccions, companyies i xarxes: cada pàgina mostra les dades mínimes simulades.
  - Keywords: pàgina individual amb pel·lícules i índex agregat des de populars + trending, incloent missatge d’error.
  - Cerca: resultats de pel·lícules amb filtres enviats correctament, resultats de persones i endpoint JSON de suggeriments.
  - Persones populars: paginació i missatge buit quan no hi ha resultats.
- Tests bàsics addicionals: `tests/Feature/ExampleTest.php` valida el 200 inicial; `tests/Unit/ExampleTest.php` manté una asserció trivial.

## Scripts útils

- `npm run dev` / `npm run build` per al frontend (Vite + Tailwind).
- `composer test` o `php artisan test` per executar tota la suite.
