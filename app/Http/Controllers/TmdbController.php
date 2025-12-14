<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class TmdbController extends Controller
{
    public function trending(Request $request): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        $region = config('services.tmdb.region') ?: strtoupper(substr($language, -2) ?: 'US');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        $mapMovies = function (array $items) {
            return collect($items)->map(function (array $movie) {
                return [
                    'id' => $movie['id'] ?? null,
                    'title' => $movie['title'] ?? $movie['name'] ?? __('(Sense títol)'),
                    'overview' => $movie['overview'] ?? '',
                    'poster' => $movie['poster_path'] ?? null,
                    'release_date' => $movie['release_date'] ?? null,
                    'vote_average' => $movie['vote_average'] ?? null,
                ];
            });
        };

        try {
            $response = $client->get('/trending/movie/day', [
                'language' => $language,
            ])->throw();
        } catch (RequestException|ConnectionException $e) {
            $trending = collect();
            $error = __("No s'han pogut obtenir les pel·lícules en tendència.");

            return view('trending', [
                'trending' => $trending,
                'error' => $error,
                'nowPlaying' => collect(),
                'popular' => collect(),
                'topRated' => collect(),
                'upcoming' => collect(),
            ]);
        }

        $trending = $mapMovies($response->json('results', []))->take(20);

        $nowPlaying = $popular = $topRated = $upcoming = collect();

        try {
            $nowPlaying = $mapMovies(
                $client->get('/movie/now_playing', [
                    'language' => $language,
                    'region' => $region,
                ])->throw()->json('results', [])
            )->take(20);
        } catch (RequestException|ConnectionException $e) {
            $nowPlaying = collect();
        }

        try {
            $popular = $mapMovies(
                $client->get('/movie/popular', [
                    'language' => $language,
                    'region' => $region,
                ])->throw()->json('results', [])
            )->take(20);
        } catch (RequestException|ConnectionException $e) {
            $popular = collect();
        }

        try {
            $topRated = $mapMovies(
                $client->get('/movie/top_rated', [
                    'language' => $language,
                    'region' => $region,
                ])->throw()->json('results', [])
            )->take(20);
        } catch (RequestException|ConnectionException $e) {
            $topRated = collect();
        }

        try {
            $upcoming = $mapMovies(
                $client->get('/movie/upcoming', [
                    'language' => $language,
                    'region' => $region,
                ])->throw()->json('results', [])
            )->take(20);
        } catch (RequestException|ConnectionException $e) {
            $upcoming = collect();
        }

        $error = null;

        return view('trending', [
            'trending' => $trending,
            'nowPlaying' => $nowPlaying,
            'popular' => $popular,
            'topRated' => $topRated,
            'upcoming' => $upcoming,
            'error' => $error,
        ]);
    }

    public function show(int $id): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        $region = config('services.tmdb.region') ?: strtoupper(substr($language, -2) ?: 'US');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $response = $client->get("/movie/{$id}", [
                'language' => $language,
                'append_to_response' => 'credits,videos,recommendations,similar,reviews,alternative_titles,external_ids,images,keywords,release_dates',
                'include_image_language' => 'null,' . substr($language, 0, 2) . ',' . $language,
            ])->throw();
        } catch (RequestException|ConnectionException $e) {
            $error = __("No s'ha pogut carregar la pel·lícula.");

            return view('movie', ['movie' => null, 'error' => $error]);
        }

        $payload = $response->json();
        $external = $payload['external_ids'] ?? [];
        $reviewsPayload = collect($payload['reviews']['results'] ?? []);

        // Si en català no hi ha crítiques, intentem anglès com a fallback.
        if ($reviewsPayload->isEmpty() && $language !== 'en-US') {
            try {
                $fallback = $client->get("/movie/{$id}/reviews", [
                    'language' => 'en-US',
                    'page' => 1,
                ])->throw();
                $reviewsPayload = collect($fallback->json('results', []));
            } catch (RequestException|ConnectionException $e) {
                // Ignorem l'error, simplement deixarem la llista buida.
            }
        }

        $watchProviders = [
            'link' => null,
            'flatrate' => [],
            'rent' => [],
            'buy' => [],
        ];

        try {
            $providersResponse = $client->get("/movie/{$id}/watch/providers")->throw();
            $regionData = $providersResponse->json("results.$region", []);
            $mapProviders = function (array $items) {
                return collect($items)->map(function ($item) {
                    return [
                        'name' => $item['provider_name'] ?? '',
                        'logo' => $item['logo_path'] ?? null,
                    ];
                })->all();
            };

            $watchProviders = [
                'link' => $regionData['link'] ?? null,
                'flatrate' => $mapProviders($regionData['flatrate'] ?? []),
                'rent' => $mapProviders($regionData['rent'] ?? []),
                'buy' => $mapProviders($regionData['buy'] ?? []),
            ];
        } catch (RequestException|ConnectionException $e) {
            // Sense proveïdors disponibles o error; ho deixem buit.
        }

        $certificationsMap = collect();
        try {
            $certificationsResponse = $client->get('/certification/movie/list')->throw();
            $certificationsMap = collect($certificationsResponse->json('certifications', []))
                ->mapWithKeys(function ($list, $country) {
                    return [$country => collect($list)->keyBy('certification')];
                });
        } catch (RequestException|ConnectionException $e) {
            // Si falla, simplement no mostrarem significats de certificacions.
        }

        $collection = $payload['belongs_to_collection'] ?? null;

        $movie = [
            'id' => $payload['id'] ?? null,
            'title' => $payload['title'] ?? $payload['name'] ?? __('(Sense títol)'),
            'tagline' => $payload['tagline'] ?? '',
            'overview' => $payload['overview'] ?? '',
            'poster' => $payload['poster_path'] ?? null,
            'backdrop' => $payload['backdrop_path'] ?? null,
            'release_date' => $payload['release_date'] ?? null,
            'runtime' => $payload['runtime'] ?? null,
            'imdb_id' => $external['imdb_id'] ?? $payload['imdb_id'] ?? null,
            'wikidata_id' => $external['wikidata_id'] ?? null,
            'facebook_id' => $external['facebook_id'] ?? null,
            'instagram_id' => $external['instagram_id'] ?? null,
            'twitter_id' => $external['twitter_id'] ?? null,
            'original_language' => $payload['original_language'] ?? null,
            'original_title' => $payload['original_title'] ?? null,
            'popularity' => $payload['popularity'] ?? null,
            'collection' => $collection ? [
                'id' => $collection['id'] ?? null,
                'name' => $collection['name'] ?? '',
                'poster' => $collection['poster_path'] ?? null,
                'backdrop' => $collection['backdrop_path'] ?? null,
            ] : null,
            'genres' => collect($payload['genres'] ?? [])->pluck('name')->all(),
            'vote_average' => $payload['vote_average'] ?? null,
            'vote_count' => $payload['vote_count'] ?? null,
            'homepage' => $payload['homepage'] ?? null,
            'status' => $payload['status'] ?? null,
            'budget' => $payload['budget'] ?? null,
            'revenue' => $payload['revenue'] ?? null,
            'adult' => $payload['adult'] ?? false,
            'video' => $payload['video'] ?? false,
            'spoken_languages' => collect($payload['spoken_languages'] ?? [])->pluck('name')->all(),
            'production_companies' => collect($payload['production_companies'] ?? [])->map(function ($c) {
                return [
                    'id' => $c['id'] ?? null,
                    'name' => $c['name'] ?? '',
                    'logo' => $c['logo_path'] ?? null,
                    'origin_country' => $c['origin_country'] ?? '',
                ];
            })->all(),
            'networks' => collect($payload['networks'] ?? [])->map(function ($n) {
                return [
                    'id' => $n['id'] ?? null,
                    'name' => $n['name'] ?? '',
                    'logo' => $n['logo_path'] ?? null,
                    'origin_country' => $n['origin_country'] ?? '',
                ];
            })->all(),
            'production_countries' => collect($payload['production_countries'] ?? [])->pluck('name')->all(),
            'alternative_titles' => collect($payload['alternative_titles']['titles'] ?? [])->map(function ($title) {
                return [
                    'title' => $title['title'] ?? '',
                    'country' => $title['iso_3166_1'] ?? '',
                    'type' => $title['type'] ?? '',
                ];
            })->take(15)->all(),
            'translations' => collect($payload['translations']['translations'] ?? [])->map(function ($translation) {
                $data = $translation['data'] ?? [];

                return [
                    'language' => $translation['iso_639_1'] ?? '',
                    'country' => $translation['iso_3166_1'] ?? '',
                    'name' => $translation['name'] ?? '',
                    'english_name' => $translation['english_name'] ?? '',
                    'title' => $data['title'] ?? '',
                    'tagline' => $data['tagline'] ?? '',
                    'overview' => $data['overview'] ?? '',
                ];
            })->take(20)->all(),
            'keywords' => collect($payload['keywords']['keywords'] ?? [])->take(20)->map(function ($kw) {
                return [
                    'id' => $kw['id'] ?? null,
                    'name' => $kw['name'] ?? '',
                ];
            })->all(),
            'release_dates' => collect($payload['release_dates']['results'] ?? [])->map(function ($entry) use ($certificationsMap) {
                $country = $entry['iso_3166_1'] ?? '';
                return collect($entry['release_dates'] ?? [])->map(function ($date) use ($country, $certificationsMap) {
                    $cert = $date['certification'] ?? '';
                    $meaning = $certificationsMap[$country][$cert]['meaning'] ?? null;
                    return [
                        'country' => $country,
                        'certification' => $cert,
                        'certification_meaning' => $meaning,
                        'type' => $date['type'] ?? null,
                        'release_date' => $date['release_date'] ?? null,
                    ];
                });
            })->flatten(1)->sortByDesc('release_date')->take(10)->values()->all(),
            'cast' => collect($payload['credits']['cast'] ?? [])->take(12)->map(function ($cast) {
                return [
                    'id' => $cast['id'] ?? null,
                    'name' => $cast['name'] ?? '',
                    'character' => $cast['character'] ?? '',
                    'profile' => $cast['profile_path'] ?? null,
                ];
            })->all(),
            'crew' => collect($payload['credits']['crew'] ?? [])->sortBy('order')->take(12)->map(function ($crew) {
                return [
                    'id' => $crew['id'] ?? null,
                    'name' => $crew['name'] ?? '',
                    'job' => $crew['job'] ?? ($crew['department'] ?? ''),
                    'profile' => $crew['profile_path'] ?? null,
                ];
            })->all(),
            'videos' => collect($payload['videos']['results'] ?? [])->filter(fn ($v) => ($v['site'] ?? '') === 'YouTube')->take(6)->map(function ($video) {
                return [
                    'id' => $video['id'] ?? null,
                    'name' => $video['name'] ?? '',
                    'type' => $video['type'] ?? '',
                    'key' => $video['key'] ?? '',
                    'official' => $video['official'] ?? false,
                    'published_at' => $video['published_at'] ?? null,
                ];
            })->values()->all(),
            'backdrops' => collect($payload['images']['backdrops'] ?? [])->take(9)->map(function ($image) {
                return [
                    'path' => $image['file_path'] ?? null,
                    'width' => $image['width'] ?? null,
                    'height' => $image['height'] ?? null,
                ];
            })->all(),
            'posters' => collect($payload['images']['posters'] ?? [])->take(9)->map(function ($image) {
                return [
                    'path' => $image['file_path'] ?? null,
                    'width' => $image['width'] ?? null,
                    'height' => $image['height'] ?? null,
                ];
            })->all(),
            'recommendations' => collect($payload['recommendations']['results'] ?? [])->take(6)->map(function ($movie) {
                return [
                    'id' => $movie['id'] ?? null,
                    'title' => $movie['title'] ?? $movie['name'] ?? __('(Sense títol)'),
                    'poster' => $movie['poster_path'] ?? null,
                    'vote_average' => $movie['vote_average'] ?? null,
                ];
            })->all(),
            'similar' => collect($payload['similar']['results'] ?? [])->take(6)->map(function ($movie) {
                return [
                    'id' => $movie['id'] ?? null,
                    'title' => $movie['title'] ?? $movie['name'] ?? __('(Sense títol)'),
                    'poster' => $movie['poster_path'] ?? null,
                    'vote_average' => $movie['vote_average'] ?? null,
                ];
            })->all(),
            'reviews' => $reviewsPayload->take(6)->map(function ($review) {
                $author = $review['author_details'] ?? [];
                return [
                    'author' => $review['author'] ?? '',
                    'rating' => $author['rating'] ?? null,
                    'content' => $review['content'] ?? '',
                    'url' => $review['url'] ?? null,
                    'created_at' => $review['created_at'] ?? null,
                ];
            })->all(),
            'watch_providers' => $watchProviders,
            'region' => $region,
        ];

        $error = null;

        return view('movie', compact('movie', 'error'));
    }

    public function person(int $id): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $response = $client->get("/person/{$id}", [
                'language' => $language,
                'append_to_response' => 'combined_credits,external_ids,images,movie_credits,tagged_images',
            ])->throw();
        } catch (RequestException|ConnectionException $e) {
            $error = __("No s'ha pogut carregar la persona.");

            return view('person', ['person' => null, 'error' => $error]);
        }

        $payload = $response->json();
        $external = $payload['external_ids'] ?? [];
        $creditsCombined = $payload['combined_credits'] ?? [];
        $creditsMovies = $payload['movie_credits'] ?? [];

        $genderMap = [
            0 => 'No especificat',
            1 => 'Dona',
            2 => 'Home',
            3 => 'No binari',
        ];

        $person = [
            'id' => $payload['id'] ?? null,
            'name' => $payload['name'] ?? __('(Sense nom)'),
            'profile' => $payload['profile_path'] ?? null,
            'known_for_department' => $payload['known_for_department'] ?? '',
            'gender' => $genderMap[$payload['gender'] ?? 0] ?? 'No especificat',
            'biography' => $payload['biography'] ?? '',
            'also_known_as' => $payload['also_known_as'] ?? [],
            'birthday' => $payload['birthday'] ?? null,
            'deathday' => $payload['deathday'] ?? null,
            'place_of_birth' => $payload['place_of_birth'] ?? '',
            'popularity' => $payload['popularity'] ?? null,
            'homepage' => $payload['homepage'] ?? null,
            'imdb_id' => $external['imdb_id'] ?? null,
            'instagram_id' => $external['instagram_id'] ?? null,
            'twitter_id' => $external['twitter_id'] ?? null,
            'facebook_id' => $external['facebook_id'] ?? null,
            'wikidata_id' => $external['wikidata_id'] ?? null,
            'tiktok_id' => $external['tiktok_id'] ?? null,
            'youtube_id' => $external['youtube_id'] ?? null,
            'profiles' => collect($payload['images']['profiles'] ?? [])->take(12)->map(function ($img) {
                return [
                    'path' => $img['file_path'] ?? null,
                    'width' => $img['width'] ?? null,
                    'height' => $img['height'] ?? null,
                ];
            })->all(),
            'tagged_images' => collect($payload['tagged_images']['results'] ?? [])->take(12)->map(function ($img) {
                return [
                    'path' => $img['file_path'] ?? null,
                    'width' => $img['width'] ?? null,
                    'height' => $img['height'] ?? null,
                ];
            })->all(),
            'cast_credits' => collect($creditsMovies['cast'] ?? $creditsCombined['cast'] ?? [])
                ->filter(fn ($c) => ($c['media_type'] ?? '') === 'movie' || !isset($c['media_type']))
                ->groupBy('id')
                ->map(function ($items) {
                    $top = $items->sortByDesc('popularity')->first();
                    $characters = $items->pluck('character')->filter()->unique()->values()->all();
                    return [
                        'id' => $top['id'] ?? null,
                        'title' => $top['title'] ?? $top['name'] ?? __('(Sense títol)'),
                        'poster' => $top['poster_path'] ?? null,
                        'characters' => $characters,
                        'release_date' => $top['release_date'] ?? null,
                        'vote_average' => $top['vote_average'] ?? null,
                        'popularity' => $top['popularity'] ?? 0,
                    ];
                })
                ->sortByDesc('popularity')
                ->take(20)
                ->values()
                ->all(),
            'crew_credits' => collect($creditsMovies['crew'] ?? $creditsCombined['crew'] ?? [])
                ->filter(fn ($c) => ($c['media_type'] ?? '') === 'movie' || !isset($c['media_type']))
                ->groupBy('id')
                ->map(function ($items) {
                    $top = $items->sortByDesc('popularity')->first();
                    $jobs = $items->pluck('job')->filter()->unique()->values()->all();
                    if (empty($jobs)) {
                        $jobs = $items->pluck('department')->filter()->unique()->values()->all();
                    }

                    return [
                        'id' => $top['id'] ?? null,
                        'title' => $top['title'] ?? $top['name'] ?? __('(Sense títol)'),
                        'poster' => $top['poster_path'] ?? null,
                        'jobs' => $jobs,
                        'release_date' => $top['release_date'] ?? null,
                        'vote_average' => $top['vote_average'] ?? null,
                        'popularity' => $top['popularity'] ?? 0,
                    ];
                })
                ->sortByDesc('popularity')
                ->take(20)
                ->values()
                ->all(),
        ];

        $error = null;

        return view('person', compact('person', 'error'));
    }

    public function collection(int $id): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $details = $client->get("/collection/{$id}", [
                'language' => $language,
            ])->throw();

            $images = $client->get("/collection/{$id}/images", [
                'include_image_language' => 'null,' . substr($language, 0, 2) . ',' . $language,
            ])->throw();

            $translations = $client->get("/collection/{$id}/translations")->throw();
        } catch (RequestException|ConnectionException $e) {
            $error = __("No s'ha pogut carregar la col·lecció.");

            return view('collection', ['collection' => null, 'error' => $error]);
        }

        $d = $details->json();

        $collection = [
            'id' => $d['id'] ?? null,
            'name' => $d['name'] ?? '',
            'overview' => $d['overview'] ?? '',
            'poster' => $d['poster_path'] ?? null,
            'backdrop' => $d['backdrop_path'] ?? null,
            'parts' => collect($d['parts'] ?? [])->sortByDesc('popularity')->map(function ($p) {
                return [
                    'id' => $p['id'] ?? null,
                    'title' => $p['title'] ?? $p['name'] ?? __('(Sense títol)'),
                    'poster' => $p['poster_path'] ?? null,
                    'overview' => $p['overview'] ?? '',
                    'release_date' => $p['release_date'] ?? null,
                    'vote_average' => $p['vote_average'] ?? null,
                ];
            })->values()->all(),
            'backdrops' => collect($images->json('backdrops', []))->take(10)->map(function ($img) {
                return [
                    'path' => $img['file_path'] ?? null,
                    'width' => $img['width'] ?? null,
                    'height' => $img['height'] ?? null,
                ];
            })->all(),
            'posters' => collect($images->json('posters', []))->take(10)->map(function ($img) {
                return [
                    'path' => $img['file_path'] ?? null,
                    'width' => $img['width'] ?? null,
                    'height' => $img['height'] ?? null,
                ];
            })->all(),
            'translations' => collect($translations->json('translations', []))->map(function ($tr) {
                $data = $tr['data'] ?? [];
                return [
                    'language' => $tr['iso_639_1'] ?? '',
                    'country' => $tr['iso_3166_1'] ?? '',
                    'name' => $tr['name'] ?? '',
                    'english_name' => $tr['english_name'] ?? '',
                    'title' => $data['title'] ?? '',
                    'overview' => $data['overview'] ?? '',
                ];
            })->take(20)->all(),
        ];

        $error = null;

        return view('collection', compact('collection', 'error'));
    }

    public function company(int $id): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $details = $client->get("/company/{$id}", [
                'language' => $language,
            ])->throw();

            $alt = $client->get("/company/{$id}/alternative_names")->throw();
            $images = $client->get("/company/{$id}/images")->throw();
        } catch (RequestException|ConnectionException $e) {
            $error = __("No s'ha pogut carregar la companyia.");

            return view('company', ['company' => null, 'error' => $error]);
        }

        $d = $details->json();

        $company = [
            'id' => $d['id'] ?? null,
            'name' => $d['name'] ?? '',
            'logo' => $d['logo_path'] ?? null,
            'description' => $d['description'] ?? '',
            'headquarters' => $d['headquarters'] ?? '',
            'homepage' => $d['homepage'] ?? null,
            'origin_country' => $d['origin_country'] ?? '',
            'parent_company' => $d['parent_company'] ?? null,
            'alternative_names' => collect($alt->json('results', []))->pluck('name')->filter()->unique()->take(20)->values()->all(),
            'logos' => collect($images->json('logos', []))->sortByDesc('vote_average')->take(12)->map(function ($logo) {
                return [
                    'path' => $logo['file_path'] ?? null,
                    'file_type' => $logo['file_type'] ?? '',
                ];
            })->values()->all(),
            'movies' => [], // Placeholder; could be filled via discover if needed later.
        ];

        $error = null;

        return view('company', compact('company', 'error'));
    }

    public function keyword(int $id): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $detail = $client->get("/keyword/{$id}")->throw();
            $movies = $client->get("/discover/movie", [
                'with_keywords' => $id,
                'language' => $language,
                'sort_by' => 'popularity.desc',
            ])->throw();
        } catch (RequestException|ConnectionException $e) {
            $error = __("No s'ha pogut carregar la keyword.");

            return view('keyword', ['keyword' => null, 'movies' => collect(), 'error' => $error]);
        }

        $keyword = [
            'id' => $detail->json('id'),
            'name' => $detail->json('name'),
        ];

        $movies = collect($movies->json('results', []))->take(20)->map(function ($m) {
            return [
                'id' => $m['id'] ?? null,
                'title' => $m['title'] ?? $m['name'] ?? __('(Sense títol)'),
                'poster' => $m['poster_path'] ?? null,
                'overview' => $m['overview'] ?? '',
                'release_date' => $m['release_date'] ?? null,
                'vote_average' => $m['vote_average'] ?? null,
            ];
        });

        $error = null;

        return view('keyword', compact('keyword', 'movies', 'error'));
    }

    public function keywords(): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            // Agafem algunes pel·lícules populars/trending i demanem les seves keywords.
            $popular = $client->get('/movie/popular', ['language' => $language, 'page' => 1])->throw();
            $trending = $client->get('/trending/movie/day', ['language' => $language])->throw();

            $movieIds = collect($popular->json('results', []))
                ->merge($trending->json('results', []))
                ->pluck('id')
                ->filter()
                ->unique()
                ->take(30);

            $keywords = collect();
            foreach ($movieIds as $movieId) {
                try {
                    $kwRes = $client->get("/movie/{$movieId}/keywords")->throw();
                    $keywords = $keywords->merge($kwRes->json('keywords', []));
                } catch (RequestException|ConnectionException $e) {
                    // Si falla un, continuem amb la resta
                }
            }

            $keywords = $keywords
                ->filter(fn($k) => isset($k['id'], $k['name']))
                ->unique('id')
                ->sortBy('name')
                ->take(100)
                ->values();
        } catch (RequestException|ConnectionException $e) {
            $keywords = collect();
            $error = __("No s'han pogut carregar les keywords.");

            return view('keywords', compact('keywords', 'error'));
        }

        $error = null;

        return view('keywords', compact('keywords', 'error'));
    }

    public function providers(Request $request): View|JsonResponse
    {
        $language = config('services.tmdb.language', 'ca-ES');
        $region = config('services.tmdb.region') ?: strtoupper(substr($language, -2) ?: 'US');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        $providers = collect();
        try {
            $providersResponse = $client->get('/watch/providers/movie', [
                'language' => $language,
                'watch_region' => $region,
            ])->throw();

            $providers = collect($providersResponse->json('results', []))
                ->filter(fn ($p) => isset($p['provider_id']))
                ->sortBy('display_priority')
                ->map(function ($p) {
                    return [
                        'id' => $p['provider_id'] ?? null,
                        'name' => $p['provider_name'] ?? '',
                        'logo' => $p['logo_path'] ?? null,
                    ];
                })
                ->values();
        } catch (RequestException|ConnectionException $e) {
            $providers = collect();
        }

        $selectedProvider = $request->get('provider');
        $monetization = $request->get('type');
        $page = max(1, (int)$request->get('page', 1));
        $movies = collect();
        $totalPages = 1;
        $error = null;
        $isAjax = $request->boolean('ajax');

        if ($selectedProvider) {
            try {
                $params = [
                    'language' => $language,
                    'watch_region' => $region,
                    'with_watch_providers' => $selectedProvider,
                    'sort_by' => 'popularity.desc',
                    'page' => $page,
                ];

                if ($monetization) {
                    $params['with_watch_monetization_types'] = $monetization;
                }

                $discover = $client->get('/discover/movie', $params)->throw();
                $totalPages = $discover->json('total_pages', 1);
                $movies = collect($discover->json('results', []))->map(function ($m) {
                    return [
                        'id' => $m['id'] ?? null,
                        'title' => $m['title'] ?? $m['name'] ?? __('(Sense títol)'),
                        'poster' => $m['poster_path'] ?? null,
                        'release_date' => $m['release_date'] ?? null,
                        'vote_average' => $m['vote_average'] ?? null,
                    ];
                });
            } catch (RequestException|ConnectionException $e) {
                $movies = collect();
                $totalPages = 1;
                $error = __("No s'han pogut carregar les pel·lícules del proveïdor.");
            }
        }

        if ($isAjax) {
            return response()->json([
                'movies' => $movies->values(),
                'page' => $page,
                'total_pages' => $totalPages,
                'error' => $error,
            ]);
        }

        return view('providers', compact('providers', 'selectedProvider', 'monetization', 'movies', 'page', 'totalPages', 'error', 'region'));
    }

    public function search(Request $request): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        $region = config('services.tmdb.region') ?: strtoupper(substr($language, -2) ?: 'US');
        Carbon::setLocale('ca');

        $q = trim($request->get('q', ''));
        $type = $request->get('type', 'movie');
        $genre = $request->get('genre');
        $year = $request->get('year');
        $provider = $request->get('provider');
        $langFilter = $request->get('lang');
        $page = max(1, (int)$request->get('page', 1));

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        $results = collect();
        $totalPages = 1;

        if ($q !== '') {
            try {
                if ($type === 'person') {
                    $res = $client->get('/search/person', [
                        'query' => $q,
                        'language' => $language,
                        'page' => $page,
                        'region' => $region,
                    ])->throw();
                    $totalPages = $res->json('total_pages', 1);
                    $results = collect($res->json('results', []))->map(function ($p) {
                        return [
                            'id' => $p['id'] ?? null,
                            'title' => $p['name'] ?? '',
                            'poster' => $p['profile_path'] ?? null,
                            'type' => 'person',
                        ];
                    });
                } elseif ($type === 'collection') {
                    $res = $client->get('/search/collection', [
                        'query' => $q,
                        'language' => $language,
                        'page' => $page,
                    ])->throw();
                    $totalPages = $res->json('total_pages', 1);
                    $results = collect($res->json('results', []))->map(function ($c) {
                        return [
                            'id' => $c['id'] ?? null,
                            'title' => $c['name'] ?? '',
                            'poster' => $c['poster_path'] ?? null,
                            'type' => 'collection',
                        ];
                    });
                } else {
                    // Movie search/discover with filtres aplicats abans de la crida
                    $params = [
                        'language' => $language,
                        'page' => $page,
                        'sort_by' => 'popularity.desc',
                        'with_watch_providers' => $provider ?: null,
                        'watch_region' => $region,
                        'with_original_language' => $langFilter ?: null,
                        'with_genres' => $genre ?: null,
                        'primary_release_year' => $year ?: null,
                    ];
                    if ($q) {
                        $params['query'] = $q;
                        $res = $client->get('/search/movie', $params)->throw();
                    } else {
                        $res = $client->get('/discover/movie', $params)->throw();
                    }
                    $totalPages = $res->json('total_pages', 1);
                    $results = collect($res->json('results', []))->map(function ($m) {
                        return [
                            'id' => $m['id'] ?? null,
                            'title' => $m['title'] ?? $m['name'] ?? '',
                            'poster' => $m['poster_path'] ?? null,
                            'type' => 'movie',
                            'release_date' => $m['release_date'] ?? null,
                            'vote_average' => $m['vote_average'] ?? null,
                        ];
                    });
                }
            } catch (RequestException|ConnectionException $e) {
                $results = collect();
                $totalPages = 1;
                $error = __("No s'han pogut carregar resultats.");
                return view('search', compact('results', 'error', 'q', 'type', 'genre', 'year', 'provider', 'langFilter', 'page', 'totalPages'));
            }
        }

        $error = null;

        return view('search', compact('results', 'error', 'q', 'type', 'genre', 'year', 'provider', 'langFilter', 'page', 'totalPages'));
    }

    public function searchSuggest(Request $request)
    {
        $language = config('services.tmdb.language', 'ca-ES');
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $res = $client->get('/search/multi', [
                'query' => $q,
                'language' => $language,
                'page' => 1,
            ])->throw();
        } catch (RequestException|ConnectionException $e) {
            return response()->json([]);
        }

        $data = collect($res->json('results', []))
            ->filter(function ($item) {
                return in_array($item['media_type'] ?? '', ['movie', 'person', 'collection']);
            })
            ->take(8)
            ->map(function ($item) {
                return [
                    'id' => $item['id'] ?? null,
                    'type' => $item['media_type'] ?? 'movie',
                    'title' => $item['title'] ?? $item['name'] ?? '',
                ];
            })
            ->values();

        return response()->json($data);
    }

    public function network(int $id): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $details = $client->get("/network/{$id}", [
                'language' => $language,
            ])->throw();

            $alts = $client->get("/network/{$id}/alternative_names")->throw();
            $images = $client->get("/network/{$id}/images")->throw();
        } catch (RequestException|ConnectionException $e) {
            $error = __("No s'ha pogut carregar la xarxa.");

            return view('network', ['network' => null, 'error' => $error]);
        }

        $d = $details->json();

        $network = [
            'id' => $d['id'] ?? null,
            'name' => $d['name'] ?? '',
            'logo' => $d['logo_path'] ?? null,
            'headquarters' => $d['headquarters'] ?? '',
            'homepage' => $d['homepage'] ?? null,
            'origin_country' => $d['origin_country'] ?? '',
            'alternative_names' => collect($alts->json('results', []))->pluck('name')->filter()->unique()->take(20)->values()->all(),
            'logos' => collect($images->json('logos', []))->sortByDesc('vote_average')->take(12)->map(function ($logo) {
                return [
                    'path' => $logo['file_path'] ?? null,
                    'file_type' => $logo['file_type'] ?? '',
                ];
            })->values()->all(),
        ];

        $error = null;

        return view('network', compact('network', 'error'));
    }

    public function popularPeople(Request $request): View
    {
        $language = config('services.tmdb.language', 'ca-ES');
        Carbon::setLocale('ca');
        $page = max(1, (int)$request->get('page', 1));

        $client = Http::withToken(config('services.tmdb.access_token'))
            ->baseUrl(config('services.tmdb.base_url', 'https://api.themoviedb.org/3'))
            ->acceptJson()
            ->asJson();

        try {
            $response = $client->get('/person/popular', [
                'language' => $language,
                'page' => $page,
            ])->throw();
        } catch (RequestException|ConnectionException $e) {
            $people = collect();
            $error = __("No s'han pogut obtenir les persones populars.");

            return view('people', compact('people', 'error', 'page') + ['totalPages' => 1]);
        }

        $people = collect($response->json('results', []))->take(24)->map(function ($p) {
            return [
                'id' => $p['id'] ?? null,
                'name' => $p['name'] ?? '',
                'profile' => $p['profile_path'] ?? null,
                'department' => $p['known_for_department'] ?? '',
                'popularity' => $p['popularity'] ?? null,
                'known_for' => collect($p['known_for'] ?? [])->take(3)->map(function ($k) {
                    return [
                        'id' => $k['id'] ?? null,
                        'title' => $k['title'] ?? $k['name'] ?? '',
                        'media_type' => $k['media_type'] ?? 'movie',
                    ];
                })->all(),
            ];
        });

        $error = null;

        $totalPages = $response->json('total_pages', 1);

        return view('people', compact('people', 'error', 'page', 'totalPages'));
    }
}
