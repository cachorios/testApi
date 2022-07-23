<?php

namespace Tests;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;


trait MakeJsonApiRequests
{

    protected bool $formatJsonApiDocument = true;




    public function withoutJsonApiDocumentFormatting()
    {
        $this->formatJsonApiDocument = false;
    }

    public function json($method, $uri, array $data = [], array $headers = []): \Illuminate\Testing\TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';


        if ($this->formatJsonApiDocument) {
            $formattedData = $this->getFormattedData($uri, $data);
        }

        //dd($formattedData ?? $data);
        return parent::json($method, $uri, $formattedData ?? $data, $headers);
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

    /**
     * @param $uri
     * @param array $data
     * @return array
     */
    /*protected function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('api/v1/')->before('/');
        $id = (string) Str::of($path)->after($type)->replace('/', '');

        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationshipData($data['_relationships'] ?? [])
            ->toArray();
    }*/

    /**
     * @param $uri
     * @param array $data
     * @return array
     */
    public function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string)Str::of($path)->after('api/v1/')->before("/");
        $id   = (string)Str::of($path)->after($type)->replace('/', '');

        return ['data' => array_filter( [
            'type' => $type,
            'id' => $id,
            'attributes' => $data,
        ])];


    }
}
