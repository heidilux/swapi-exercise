<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;

class SwapiFeatureTest extends TestCase
{
    public function test_api_request_is_successful(): void
    {
        $response = $this->getJson('api/v1/');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success',
            ]);

    }

    public function test_luke_with_starships_returns_expected_response(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'https://swapi.dev/api/people/?search=luke' => Http::response([
                'count'    => 1,
                'next'     => null,
                'previous' => null,
                'results'  => [
                    [
                        'name'      => 'Luke Skywalker',
                        'starships' => [
                            'https://swapi.dev/api/starships/12/',
                        ],
                    ],
                ],
            ]),

            'https://swapi.dev/api/starships/12/' => Http::response([
                'name' => 'X-wing',
            ]),
        ]);

        $this->getJson('api/v1/people/luke/starships')
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->where('Person', 'Luke Skywalker')
                ->has('Starships', fn($json) => $json->has('X-wing', fn($json) => $json->where('name', 'X-wing')))
            );

        $this->getJson('api/v1/people/luke/shmeeships')
            ->assertStatus(404)
            ->assertJson(['message' => 'Your relation query did not return any results.']);
    }

    public function test_episode_1_with_species_returns_expected_response(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'https://swapi.dev/api/films/?search=hope' => Http::response([
                'count'    => 1,
                'next'     => null,
                'previous' => null,
                'results'  => [
                    [
                        'title'   => 'A New Hope',
                        'species' => [
                            'https://swapi.dev/api/species/1/',
                        ],
                    ],
                ],
            ]),

            'https://swapi.dev/api/species/1/' => Http::response([
                'name'           => 'Human',
                'classification' => 'mammal',
            ]),
        ]);

        $this->getJson('api/v1/films/hope/species')
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->where('Film', 'A New Hope')
                ->has('Species', fn($json) => $json->where('Human', 'mammal'))
            );

        $this->getJson('api/v1/films/hope/ewoks')
            ->assertStatus(404)
            ->assertJson(['message' => 'Your relation query did not return any results.']);
    }

    public function test_galaxy_population_returns_expected_response(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'https://swapi.dev/api/planets/?page=1' => Http::response([
                'count'    => 3,
                'next'     => 'https://swapi.dev/api/planets/?page=2',
                'previous' => null,
                'results'  => [
                    [
                        'name'       => 'Tatooine',
                        'population' => 900,
                    ],
                ],
            ]),

            'https://swapi.dev/api/planets/?page=2' => Http::response([
                'count'    => 3,
                'next'     => 'https://swapi.dev/api/planets/?page=3',
                'previous' => 'https://swapi.dev/api/planets/?page=1',
                'results'  => [
                    [
                        'name'       => 'Alderaan',
                        'population' => 600,
                    ],
                ],
            ]),

            'https://swapi.dev/api/planets/?page=3' => Http::response([
                'count'    => 3,
                'next'     => null,
                'previous' => 'https://swapi.dev/api/planets/?page=2',
                'results'  => [
                    [
                        'name'       => 'Stewjon',
                        'population' => 'unknown',
                    ],
                ],
            ]),
        ]);

        $this->getJson('api/v1/planets/population')
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->where('population', 1500)
                ->where('text', 'one thousand five hundred'));
    }

    public function test_search_with_no_results_returns_404(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'https://swapi.dev/api/*/?search=foo' => Http::response([
                'count'    => 0,
                'next'     => null,
                'previous' => null,
                'results'  => [],
            ]),
        ]);

        $this->getJson('api/v1/people/foo')
            ->assertStatus(404)
            ->assertJson(['message' => 'Your search query did not return any results.']);

        $this->getJson('api/v1/films/foo')
            ->assertStatus(404)
            ->assertJson(['message' => 'Your search query did not return any results.']);

        $this->getJson('api/v1/planets/foo')
            ->assertStatus(404)
            ->assertJson(['message' => 'Your search query did not return any results.']);
    }

    public function test_bad_endpoint_returns_404(): void
    {
        $this->getJson('api/v1/foo')
            ->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json');

        $this->getJson('foo')
            ->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json');
    }
}
