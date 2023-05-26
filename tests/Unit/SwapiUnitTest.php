<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\SwapiApiService;

class SwapiUnitTest extends TestCase
{
    private SwapiApiService $apiService;
    private object $validObjectWithValidRelation;
    private object $validObjectWithInvalidRelation;
    private object $invalidObject;

    protected function setUp(): void
    {
        $this->apiService = new SwapiApiService();

        $this->validObjectWithInvalidRelation = (object)[
            'results' => [
                (object)['foo' => []],
            ],
        ];

        $this->validObjectWithValidRelation = (object)[
            'results' => [
                (object)['characters' => []],
            ],
        ];

        $this->invalidObject = (object)[];
    }

    public function test_a_response_with_a_valid_object_returns_true(): void
    {
        $this->assertTrue($this->apiService->responseIsValid($this->validObjectWithInvalidRelation));
        $this->assertTrue($this->apiService->responseIsValid($this->validObjectWithValidRelation));
    }

    public function test_a_response_with_an_invalid_or_missing_object_returns_false(): void
    {
        $this->assertFalse($this->apiService->responseIsValid($this->invalidObject));
        $this->assertFalse($this->apiService->responseIsValid(null));
    }

    public function test_a_valid_object_with_a_valid_relation_returns_true(): void
    {
        $this->assertTrue($this->apiService->relationIsValid($this->validObjectWithValidRelation, 'characters'));
    }

    public function test_a_valid_object_with_an_invalid_relation_returns_false(): void
    {
        $this->assertFalse($this->apiService->relationIsValid($this->validObjectWithInvalidRelation, 'characters'));
    }
}
