<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\Response;

trait HandlesJsonResponse
{
    protected function respondWith(Response $response): JsonResponse
    {
        if ($response->clientError()) {
            return response()->json(['message' => 'Malformed request. Please check the request format and try again.'],
                400);
        }

        if ($response->serverError()) {
            return response()->json(['message' => 'Whoops, the api is currently having trouble. Try again later.'],
                500);
        }

        return response()->json($response->json());
    }

    protected function respondNotFound(string $type = 'search'): JsonResponse
    {
        return response()
            ->json(['message' => 'Your ' . $type . ' query did not return any results.'], 404);
    }

    protected function respondTimeout(): JsonResponse
    {
        return response()
            ->json(['message' => 'The connection timed out. Please try again later.'], 408);
    }
}
