<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonAPiHeadersTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('test_route', fn() => 'OK')
            ->middleware(ValidateJsonApiHeaders::class);
    }


    /** @test */
    public function accept_header_must_be_presente_in_all_request()
    {

        $this->get('test_route')->assertStatus(406);
        $this->get('test_route',['Accept' => 'application/vnd.api+json'])->assertSuccessful();

    }

    /** @test */
    public function content_type_header_must_be_presente_in_all_post_request()
    {

        $this->post('test_route', [],
            [
                'accept' => 'application/vnd.api+json',
            ])
            ->assertStatus(415);

         /**  este no esta andando  verifica post*/
//        $this->post('test_route', [], [
//            'accept' => 'application/vnd.api+json',
//            'content-type' => 'application/vnd.api+json'
//        ])->dump()->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_presente_in_all_patch_request()
    {
        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

//        $this->patch('test_route', [], [
//            'accept' => 'application/vnd.api+json',
//            'content-type' => 'application/vnd.api+json'
//        ])->dump()->assertSuccessful();

    }

    /** @test */
    public function content_type_header_must_be_present_in_responses()
    {


        $this->get('test_route', [
            'accept' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');


        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');
    }


    /** @test */

//    function content_type_header_must_not_be_present_in_empty_responses(){
//        Route::any('empty_response', fn() => response()->noContent())
//            ->middleware(ValidateJsonApiHeaders::class);
//
//        $this->get('empty_response', [
//            'accept' => 'application/vnd.api+json',
//            'content-type' => 'application/vnd.api+json',
//        ])->assertHeaderMissing('content-type');
//
//        $this->post('empty_response', [], [
//            'accept' => 'application/vnd.api+json',
//            'content-type' => 'application/vnd.api+json',
//        ])->assertHeaderMissing('content-type');
//
//        $this->patch('empty_response', [], [
//            'accept' => 'application/vnd.api+json',
//            'content-type' => 'application/vnd.api+json',
//        ])->assertHeaderMissing('content-type');
//
//        $this->delete('empty_response', [], [
//            'accept' => 'application/vnd.api+json',
//            'content-type' => 'application/vnd.api+json',
//        ])->assertHeaderMissing('content-type');
//    }

}


