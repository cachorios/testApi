<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidationJsonApiDocumentTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('test_route', fn() => 'OK')
            ->middleware(ValidateJsonApiDocument::class);
    }

    /** @test */
    public function only_accepts_valid_json_api_document()
    {
        $this->postJson('test_route', [
            'data' =>[
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Test',
                ],
            ]
        ])
            ->assertSuccessful('data');

        $this->patchJson('test_route',  [
            'data' =>[
                'type' => 'articles',
                'id' => '1',
                'attributes' => [
                    'title' => 'Test',
                ],
            ]
        ])
            ->assertSuccessful('data');
    }

    /** @test */
    public function data_is_required()
    {
        $this->postJson('test_route', [])
            ->assertJsonApiValidationErrors('data');
        $this->patchJson('test_route', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_is_array()
    {
        $this->postJson('test_route', ['data' => 'string'])
            ->assertJsonApiValidationErrors('data');
        $this->patchJson('test_route', ['data' => 'string'])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function date_type_is_required()
    {
        $this->postJson('test_route', ['data' => [
            'attributes' => [
            ],
        ]])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', ['data' => [
            'attributes' => [],
        ]])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function date_type_is_a_string()
    {
        $this->postJson('test_route', ['data' => [
            'type' => 1,
            'attributes' => [
            ],
        ]])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', ['data' => [
            'type' => 1,
            'attributes' => [

            ],
        ]])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attribute_is_required()
    {
        $this->postJson('test_route', ['data' => [
            'type' => "1",

        ]])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', ['data' => [
            'type' => "1",

        ]])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attribute_must_be_an_array()
    {
        $this->postJson('test_route', ['data' => [
            'type' => "1",
            'attributes' => "string",

        ]])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', ['data' => [
            'type' => "1",
            'attributes' => "string",

        ]])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function date_id_is_required()
    {
        $this->patchJson('test_route', ['data' => [
            'type' => "1",
            'attributes' => ['name' => ''],
        ]])
            ->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function date_id_is_a_string()
    {
        $this->patchJson('test_route', ['data' => [
            'type' => "1",
            'attributes' => ['name' => ''],
            'id' => 1,
        ]])
            ->assertJsonApiValidationErrors('data.id');
    }
}
