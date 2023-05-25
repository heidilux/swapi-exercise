<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\SwapiApiService;
use Illuminate\Http\JsonResponse;
use App\Traits\HandlesJsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class PeopleController extends Controller
{
    use HandlesJsonResponse;

    private SwapiApiService $apiService;

    public function __construct(SwapiApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function all(): JsonResponse
    {
        return $this->respondWith($this->apiService->get('people'));
    }

    public function single(string $person, ?string $relation = null): JsonResponse
    {
        try {
            $response = $this->apiService->get('people/?search=' . $person);
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

        $relations['Person'] = $resultObject->results[0]->name;
        foreach ($relationResults as $result) {
            $relationResponse = Http::get($result)->object();
            $relations[Str::title($relation)][$relationResponse->name ?? $relationResponse->title] = $relationResponse;
        }

        return response()->json($relations);
    }
}
