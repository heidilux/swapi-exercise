<?php

namespace App\Http\Controllers;

use NumberFormatter;
use Illuminate\Support\Str;
use App\Services\SwapiApiService;
use Illuminate\Http\JsonResponse;
use App\Traits\HandlesJsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class PlanetsController extends Controller
{
    use HandlesJsonResponse;

    private SwapiApiService $apiService;

    public function __construct(SwapiApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function all(): JsonResponse
    {
        return $this->respondWith($this->apiService->get('planets'));
    }

    public function totalPopulation(): JsonResponse
    {
        $populationCount = 0;
        $page = 1;

        $population = $this->incrementPopulation($page, $populationCount);

        $format = numfmt_create('en_US', NumberFormatter::SPELLOUT);
        $words = numfmt_format($format, $population);

        return response()->json(['population' => $population, 'text' => $words]);
    }

    private function incrementPopulation(int $page, int $count): int
    {
        $planetsResponse = $this->apiService->get('planets/?page=' . $page)->object();
        $lastPage = $planetsResponse?->next === null;

        foreach ($planetsResponse?->results as $planet) {
            // "unknown" populations are coerced to 0, so no harm here...
            $count += (int)$planet->population;
        }

        if ($lastPage) {
            return $count;
        }

        return $this->incrementPopulation(++$page, $count);
    }

    public function single(string $planet, ?string $relation = null): JsonResponse
    {
        try {
            $response = $this->apiService->get('planets/?search=' . $planet);
        } catch (ConnectionException $e) {
            return $this->respondTimeout();
        }

        $resultObject = $response->object();

        if (!$this->apiService->responseIsValid($resultObject)) {
            return $this->respondNotFound();
        }

        if (!$relation) {
            return $this->respondWith($response);
        }

        if (!$this->apiService->relationIsValid($resultObject, $relation)) {
            return $this->respondNotFound('relation');
        }

        $relationResults = $resultObject->results[0]->$relation;

        $relations['Planet'] = $resultObject->results[0]->name;
        foreach ($relationResults as $result) {
            $relationResponse = Http::get($result)->object();
            $relations[Str::title($relation)][$relationResponse->name ?? $relationResponse->title] = $relationResponse;
        }

        return response()->json($relations);
    }
}
