<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Promise\PromiseInterface;

class SwapiApiService
{
    private const BASE_URL = 'https://swapi.dev/api/';

    public function get(string $resource): PromiseInterface|Response
    {
        return Http::get(self::BASE_URL . $resource);
    }

    public function responseIsValid(?object $resultObject): bool
    {
        return !(!$resultObject || empty($resultObject->results));
    }

    public function relationIsValid(object $resultObject, string $relation): bool
    {
        if (!isset($resultObject->results[0]->$relation)) {
            return false;
        }

        return true;
    }
}
