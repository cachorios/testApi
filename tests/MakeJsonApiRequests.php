<?php

namespace Tests;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;


trait MakeJsonApiRequests
{


    protected function setUp(): void
    {
        parent::setUp();
        TestResponse::macro(
            'assertJsonApiValidationErrors',
            $this->assertJsonApiValidationErrors());
    }

    /**
     * @return \Closure
     */
    public function assertJsonApiValidationErrors(): \Closure
    {
        return function ($attribute) {
            /** @var TestResponse $this */

            try {
                $this->assertJsonFragment([
                    'source' => [
                        'pointer' => "/data/attributes/{$attribute}",
                    ],
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Fail to find JSON:API validation error for attribute '{$attribute}'"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage());
            }

            try{
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ],
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Fail to find a valid JSON:API error response"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage());
            }

            $this
                ->assertHeader('Content-Type', 'application/vnd.api+json')
                ->assertStatus(422);;
        };
    }


    public function json($method, $uri, array $data = [], array $headers = []): \Illuminate\Testing\TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';
        return parent::json($method, $uri, $data, $headers);
    }

    public function postJson($uri, array $data = [], array $headers = []): \Illuminate\Testing\TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::postJson( $uri, $data, $headers);
    }

    public function patchJson($uri, array $data = [], array $headers = []): \Illuminate\Testing\TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::patchJson( $uri, $data, $headers);
    }
}
