<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TmdbPagesTest extends TestCase
{
    private function fakeHome()
    {
        Http::fake([
            'https://api.themoviedb.org/3/trending/movie/day*' => Http::response([
                'results' => [
                    ['id' => 1, 'title' => 'Trending One', 'poster_path' => '/p1.jpg', 'release_date' => '2024-01-01', 'vote_average' => 7.1],
                ],
            ]),
            'https://api.themoviedb.org/3/movie/now_playing*' => Http::response(['results' => []]),
            'https://api.themoviedb.org/3/movie/popular*' => Http::response(['results' => []]),
            'https://api.themoviedb.org/3/movie/top_rated*' => Http::response(['results' => []]),
            'https://api.themoviedb.org/3/movie/upcoming*' => Http::response(['results' => []]),
        ]);
    }

    public function test_home_page_loads()
    {
        $this->fakeHome();
        $this->get('/')->assertOk()->assertSee('Infofilm');
    }

    public function test_home_page_shows_error_when_tmdb_fails()
    {
        Http::fake([
            'https://api.themoviedb.org/3/trending/movie/day*' => Http::response([], 500),
            '*' => Http::response(['results' => []]),
        ]);

        $this->get('/')->assertOk()->assertSee("No s'han pogut obtenir les pel·lícules en tendència.");
    }

    public function test_movie_page_loads()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/123*' => Http::response([
                'id' => 123,
                'title' => 'Fake Movie',
                'overview' => 'Overview',
                'poster_path' => '/p.jpg',
                'backdrop_path' => '/b.jpg',
                'release_date' => '2024-01-01',
                'runtime' => 100,
                'vote_average' => 7.5,
                'vote_count' => 10,
                'adult' => false,
                'video' => false,
                'genres' => [['name' => 'Action']],
                'spoken_languages' => [['name' => 'Català']],
                'production_companies' => [['id' => 9, 'name' => 'Studio', 'logo_path' => '/l.png', 'origin_country' => 'ES']],
                'networks' => [['id' => 3, 'name' => 'Net', 'logo_path' => '/n.png', 'origin_country' => 'ES']],
                'production_countries' => [['name' => 'Espanya']],
                'alternative_titles' => ['titles' => [['title' => 'Alt', 'iso_3166_1' => 'ES']]],
                'translations' => ['translations' => [['iso_639_1' => 'ca', 'iso_3166_1' => 'ES', 'name' => 'Català', 'english_name' => 'Catalan', 'data' => ['title' => 'Fake Movie']]]],
                'keywords' => ['keywords' => [['id' => 1, 'name' => 'keyword']]],
                'release_dates' => ['results' => [['iso_3166_1' => 'ES', 'release_dates' => [['certification' => '7', 'type' => 3, 'release_date' => '2024-01-01']]]]],
                'credits' => [
                    'cast' => [['id' => 11, 'name' => 'Actor', 'character' => 'Hero', 'profile_path' => '/a.jpg']],
                    'crew' => [['id' => 12, 'name' => 'Director', 'job' => 'Director', 'profile_path' => '/d.jpg']],
                ],
                'videos' => ['results' => [['site' => 'YouTube', 'id' => 'v1', 'name' => 'Trailer', 'type' => 'Trailer', 'key' => 'abc', 'official' => true, 'published_at' => '2024-01-01']]],
                'images' => ['backdrops' => [['file_path' => '/bd.jpg', 'width' => 1280, 'height' => 720]], 'posters' => [['file_path' => '/pp.jpg', 'width' => 500, 'height' => 750]]],
                'recommendations' => ['results' => []],
                'similar' => ['results' => []],
                'reviews' => ['results' => []],
            ]),
            'https://api.themoviedb.org/3/movie/123/watch/providers*' => Http::response([
                'results' => ['ES' => ['link' => 'https://justwatch', 'flatrate' => [['provider_name' => 'JW', 'logo_path' => '/j.png']]]],
            ]),
            'https://api.themoviedb.org/3/certification/movie/list*' => Http::response([
                'certifications' => ['ES' => [['certification' => '7', 'meaning' => 'Majors de 7', 'order' => 1]]],
            ]),
        ]);

        $this->get('/movies/123')
            ->assertOk()
            ->assertSee('Fake Movie');
    }

    public function test_movie_fallback_reviews_to_english()
    {
        Http::fake([
            // Main movie call with empty reviews triggers fallback
            'https://api.themoviedb.org/3/movie/5?*' => Http::response([
                'id' => 5,
                'title' => 'Sense Ressenyes',
                'overview' => 'Overview',
                'poster_path' => '/p.jpg',
                'backdrop_path' => null,
                'release_date' => '2024-01-01',
                'runtime' => 90,
                'vote_average' => 6.0,
                'vote_count' => 2,
                'genres' => [],
                'spoken_languages' => [],
                'production_companies' => [],
                'production_countries' => [],
                'alternative_titles' => ['titles' => []],
                'translations' => ['translations' => []],
                'keywords' => ['keywords' => []],
                'release_dates' => ['results' => []],
                'credits' => ['cast' => [], 'crew' => []],
                'videos' => ['results' => []],
                'images' => ['backdrops' => [], 'posters' => []],
                'recommendations' => ['results' => []],
                'similar' => ['results' => []],
                'reviews' => ['results' => []],
            ]),
            // Fallback review in English
            'https://api.themoviedb.org/3/movie/5/reviews*' => Http::response([
                'results' => [
                    ['author' => 'Alice', 'content' => 'Great', 'author_details' => ['rating' => 7], 'created_at' => '2024-01-02', 'url' => 'http://example.com/rev'],
                ],
            ]),
            'https://api.themoviedb.org/3/movie/5/watch/providers*' => Http::response(['results' => []]),
            'https://api.themoviedb.org/3/certification/movie/list*' => Http::response(['certifications' => []]),
        ]);

        $this->get('/movies/5')->assertOk()->assertSee('Alice')->assertSee('Great');
    }

    public function test_person_page_loads()
    {
        Http::fake([
            'https://api.themoviedb.org/3/person/55*' => Http::response([
                'id' => 55,
                'name' => 'Fake Person',
                'known_for_department' => 'Acting',
                'gender' => 2,
                'biography' => 'Bio',
                'also_known_as' => ['Alias'],
                'birthday' => '1990-01-01',
                'place_of_birth' => 'City',
                'popularity' => 10,
                'profile_path' => '/p.jpg',
                'external_ids' => [],
                'images' => ['profiles' => [['file_path' => '/pf.jpg', 'width' => 500, 'height' => 750]]],
                'tagged_images' => ['results' => [['file_path' => '/ti.jpg', 'width' => 500, 'height' => 750]]],
                'combined_credits' => ['cast' => [], 'crew' => []],
                'movie_credits' => [
                    'cast' => [['id' => 1, 'title' => 'Movie A', 'poster_path' => '/ma.jpg', 'character' => 'Hero', 'popularity' => 5]],
                    'crew' => [['id' => 2, 'title' => 'Movie B', 'poster_path' => '/mb.jpg', 'job' => 'Director', 'popularity' => 4]],
                ],
            ]),
        ]);

        $this->get('/people/55')->assertOk()->assertSee('Fake Person');
    }

    public function test_person_groups_characters_and_jobs()
    {
        Http::fake([
            'https://api.themoviedb.org/3/person/99*' => Http::response([
                'id' => 99,
                'name' => 'Multi Rol',
                'known_for_department' => 'Acting',
                'gender' => 1,
                'biography' => '',
                'also_known_as' => [],
                'birthday' => null,
                'place_of_birth' => '',
                'popularity' => 1,
                'profile_path' => '/p.jpg',
                'external_ids' => [],
                'images' => ['profiles' => [], 'tagged_images' => ['results' => []]],
                'tagged_images' => ['results' => []],
                'combined_credits' => [],
                'movie_credits' => [
                    'cast' => [
                        ['id' => 1, 'title' => 'Movie X', 'poster_path' => '/a.jpg', 'character' => 'Char A', 'popularity' => 2],
                        ['id' => 1, 'title' => 'Movie X', 'poster_path' => '/a.jpg', 'character' => 'Char B', 'popularity' => 3],
                    ],
                    'crew' => [
                        ['id' => 2, 'title' => 'Movie Y', 'poster_path' => '/b.jpg', 'job' => 'Director', 'popularity' => 1],
                        ['id' => 2, 'title' => 'Movie Y', 'poster_path' => '/b.jpg', 'job' => 'Producer', 'popularity' => 2],
                    ],
                ],
            ]),
        ]);

        $this->get('/people/99')
            ->assertOk()
            ->assertSee('Char A · Char B')
            ->assertSee('Director · Producer');
    }

    public function test_collection_page_loads()
    {
        Http::fake([
            'https://api.themoviedb.org/3/collection/77*' => Http::response([
                'id' => 77,
                'name' => 'Fake Collection',
                'overview' => 'Overview',
                'poster_path' => '/c.jpg',
                'backdrop_path' => '/cb.jpg',
                'parts' => [['id' => 1, 'title' => 'Movie in Collection', 'poster_path' => '/mc.jpg', 'overview' => 'part', 'release_date' => '2023-01-01', 'vote_average' => 7]],
            ]),
            'https://api.themoviedb.org/3/collection/77/images*' => Http::response(['backdrops' => [], 'posters' => []]),
            'https://api.themoviedb.org/3/collection/77/translations*' => Http::response(['translations' => []]),
        ]);

        $this->get('/collections/77')->assertOk()->assertSee('Fake Collection');
    }

    public function test_keyword_page_loads()
    {
        Http::fake([
            'https://api.themoviedb.org/3/keyword/9*' => Http::response(['id' => 9, 'name' => 'Robots']),
            'https://api.themoviedb.org/3/discover/movie*' => Http::response(['results' => [['id' => 1, 'title' => 'Robot Movie', 'poster_path' => '/r.jpg', 'overview' => '', 'release_date' => '2022-01-01', 'vote_average' => 6.5]]]),
        ]);

        $this->get('/keywords/9')->assertOk()->assertSee('Robots')->assertSee('Robot Movie');
    }

    public function test_keywords_index_collects_from_popular_and_trending()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/popular*' => Http::response(['results' => [['id' => 1], ['id' => 2]]]),
            'https://api.themoviedb.org/3/trending/movie/day*' => Http::response(['results' => [['id' => 3]]]),
            'https://api.themoviedb.org/3/movie/1/keywords*' => Http::response(['keywords' => [['id' => 10, 'name' => 'Foo']]]),
            'https://api.themoviedb.org/3/movie/2/keywords*' => Http::response(['keywords' => [['id' => 11, 'name' => 'Bar']]]),
            'https://api.themoviedb.org/3/movie/3/keywords*' => Http::response(['keywords' => [['id' => 10, 'name' => 'Foo']]]),
        ]);

        $this->get('/keywords')
            ->assertOk()
            ->assertSee('Foo')
            ->assertSee('Bar');
    }

    public function test_providers_page_lists_providers()
    {
        Http::fake([
            'https://api.themoviedb.org/3/watch/providers/movie*' => Http::response([
                'results' => [
                    ['provider_id' => 8, 'provider_name' => 'Netflix', 'logo_path' => '/n.png', 'display_priority' => 1],
                    ['provider_id' => 1799, 'provider_name' => 'Filmin', 'logo_path' => '/f.png', 'display_priority' => 2],
                ],
            ]),
        ]);

        $this->get('/providers')
            ->assertOk()
            ->assertSee('Netflix')
            ->assertSee('Filmin');
    }

    public function test_providers_page_filters_movies_by_provider()
    {
        Http::fake([
            'https://api.themoviedb.org/3/watch/providers/movie*' => Http::response([
                'results' => [
                    ['provider_id' => 8, 'provider_name' => 'Netflix', 'logo_path' => '/n.png'],
                ],
            ]),
            'https://api.themoviedb.org/3/discover/movie*' => function ($request) {
                $data = $request->data();
                $this->assertSame('8', $data['with_watch_providers'] ?? null);
                $this->assertSame('flatrate', $data['with_watch_monetization_types'] ?? null);

                return Http::response([
                    'results' => [
                        ['id' => 1, 'title' => 'Provider Movie', 'poster_path' => '/p.jpg', 'release_date' => '2024-01-01', 'vote_average' => 7.2],
                    ],
                    'total_pages' => 2,
                ]);
            },
        ]);

        $this->get('/providers?provider=8&type=flatrate&page=1')
            ->assertOk()
            ->assertSee('Provider Movie')
            ->assertSee('1 / 2');
    }

    public function test_providers_ajax_returns_movies_for_infinite_scroll()
    {
        Http::fake([
            'https://api.themoviedb.org/3/watch/providers/movie*' => Http::response([
                'results' => [
                    ['provider_id' => 8, 'provider_name' => 'Netflix', 'logo_path' => '/n.png'],
                ],
            ]),
            'https://api.themoviedb.org/3/discover/movie*' => Http::response([
                'results' => [
                    ['id' => 2, 'title' => 'More Movie', 'poster_path' => '/m.jpg', 'release_date' => '2024-02-02', 'vote_average' => 6.5],
                ],
                'total_pages' => 3,
            ]),
        ]);

        $this->getJson('/providers?provider=8&page=2&ajax=1')
            ->assertOk()
            ->assertJsonFragment(['title' => 'More Movie'])
            ->assertJsonFragment(['page' => 2]);
    }

    public function test_providers_page_handles_discover_error()
    {
        Http::fake([
            'https://api.themoviedb.org/3/watch/providers/movie*' => Http::response([
                'results' => [
                    ['provider_id' => 1, 'provider_name' => 'Demo', 'logo_path' => null],
                ],
            ]),
            'https://api.themoviedb.org/3/discover/movie*' => Http::response([], 500),
        ]);

        $this->get('/providers?provider=1')
            ->assertOk()
            ->assertSee("No s'han pogut carregar les pel·lícules del proveïdor.");
    }

    public function test_search_suggest_returns_results()
    {
        Http::fake([
            'https://api.themoviedb.org/3/search/multi*' => Http::response([
                'results' => [
                    ['id' => 7, 'media_type' => 'movie', 'title' => 'Search Movie'],
                    ['id' => 8, 'media_type' => 'person', 'name' => 'Search Person'],
                ],
            ]),
        ]);

        $this->getJson('/search/suggest?q=sea')
            ->assertOk()
            ->assertJsonFragment(['id' => 7, 'type' => 'movie'])
            ->assertJsonFragment(['id' => 8, 'type' => 'person']);
    }

    public function test_people_popular_page_shows_pagination()
    {
        Http::fake([
            'https://api.themoviedb.org/3/person/popular*' => Http::response([
                'results' => [
                    ['id' => 1, 'name' => 'Actor One', 'profile_path' => '/a.jpg', 'known_for_department' => 'Acting', 'popularity' => 20, 'known_for' => []],
                ],
                'total_pages' => 5,
            ]),
        ]);

        $this->get('/people/popular?page=2')
            ->assertOk()
            ->assertSee('Actor One')
            ->assertSee('2 / 5');
    }

    public function test_people_popular_shows_empty_message()
    {
        Http::fake([
            'https://api.themoviedb.org/3/person/popular*' => Http::response([
                'results' => [],
                'total_pages' => 1,
            ]),
        ]);

        $response = $this->get('/people/popular');
        $response->assertOk();
        $response->assertSee("No s'han trobat persones.", false);
    }

    public function test_keywords_index_error_message()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/popular*' => Http::response([], 500),
            'https://api.themoviedb.org/3/trending/movie/day*' => Http::response([], 500),
        ]);

        $this->get('/keywords')->assertOk()->assertSee("No s'han pogut carregar les keywords.");
    }

    public function test_search_page_movie_results()
    {
        Http::fake([
            'https://api.themoviedb.org/3/search/movie*' => Http::response([
                'results' => [['id' => 1, 'title' => 'Result Movie', 'poster_path' => '/r.jpg', 'release_date' => '2024-01-01', 'vote_average' => 7]],
                'total_pages' => 1,
            ]),
        ]);

        $this->get('/search?q=demo')->assertOk()->assertSee('Result Movie');
    }

    public function test_search_page_movie_results_with_filters_are_sent()
    {
        Http::fake([
            'https://api.themoviedb.org/3/search/movie*' => function ($request) {
                $data = $request->data();
                $this->assertSame('demo', $data['query'] ?? null);
                $this->assertSame('12', $data['with_genres'] ?? null);
                $this->assertSame('2020', $data['primary_release_year'] ?? null);
                $this->assertSame('8', $data['with_watch_providers'] ?? null);
                $this->assertSame('es', $data['with_original_language'] ?? null);
                $this->assertSame('ES', $data['watch_region'] ?? null);

                return Http::response([
                    'results' => [
                        [
                            'id' => 1,
                            'title' => 'Filtered Movie',
                            'poster_path' => '/f.jpg',
                            'release_date' => '2020-01-01',
                            'vote_average' => 8.1,
                        ],
                    ],
                    'total_pages' => 1,
                ]);
            },
        ]);

        $this->get('/search?q=demo&genre=12&year=2020&provider=8&lang=es')
            ->assertOk()
            ->assertSee('Filtered Movie');
    }

    public function test_search_page_person_results()
    {
        Http::fake([
            'https://api.themoviedb.org/3/search/person*' => Http::response([
                'results' => [['id' => 2, 'name' => 'Person Result', 'profile_path' => '/p.jpg']],
                'total_pages' => 1,
            ]),
        ]);

        $this->get('/search?type=person&q=actor')->assertOk()->assertSee('Person Result');
    }

    public function test_company_page_loads()
    {
        Http::fake([
            'https://api.themoviedb.org/3/company/5*' => Http::response([
                'id' => 5,
                'name' => 'Acme',
                'logo_path' => '/l.png',
                'description' => 'Desc',
                'headquarters' => 'HQ',
                'homepage' => 'https://acme.test',
                'origin_country' => 'US',
                'parent_company' => ['name' => 'Parent'],
            ]),
            'https://api.themoviedb.org/3/company/5/alternative_names*' => Http::response([
                'results' => [['name' => 'Acme Alt']],
            ]),
            'https://api.themoviedb.org/3/company/5/images*' => Http::response([
                'logos' => [['file_path' => '/logo.png', 'vote_average' => 5]],
            ]),
        ]);

        $this->get('/companies/5')->assertOk()->assertSee('Acme');
    }

    public function test_network_page_loads()
    {
        Http::fake([
            'https://api.themoviedb.org/3/network/7*' => Http::response([
                'id' => 7,
                'name' => 'Net TV',
                'logo_path' => '/n.png',
                'headquarters' => 'HQ',
                'homepage' => 'https://net.test',
                'origin_country' => 'ES',
            ]),
            'https://api.themoviedb.org/3/network/7/alternative_names*' => Http::response([
                'results' => [['name' => 'Net Alt']],
            ]),
            'https://api.themoviedb.org/3/network/7/images*' => Http::response([
                'logos' => [['file_path' => '/n.png', 'vote_average' => 5]],
            ]),
        ]);

        $this->get('/networks/7')->assertOk()->assertSee('Net TV');
    }

    public function test_movie_error_page_still_renders()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/404*' => Http::response([], 500),
        ]);

        $this->get('/movies/404')->assertOk();
    }

    public function test_person_error_page_still_renders()
    {
        Http::fake([
            'https://api.themoviedb.org/3/person/404*' => Http::response([], 500),
        ]);

        $this->get('/people/404')->assertOk();
    }
}
