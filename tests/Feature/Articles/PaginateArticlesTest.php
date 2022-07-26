<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginateArticlesTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_paginate_articles()
    {
        $articles = Article::factory()->count(6)->create();

        // /articles?page[size]=2&page[number]=2
        $url = route('api.v1.articles.index', [
            'page' =>[
                'size' => 2,
                'number' => 2,
            ]
        ]);

        $response = $this->getJson($url);

        $response->assertSee([
                $articles[2]->title,
                $articles[3]->title,
            ])->assertDontSee([
                $articles[0]->title,
                $articles[1]->title,
                $articles[4]->title,
                $articles[5]->title,
            ]);

        $response->assertJsonStructure(
                [
                    'links' => [ 'first','last','prev','next' ],
                ]
            );

        $link = urldecode($response->json('links.first'));
        $this->assertStringContainsString('page[number]=1', $link);
        $this->assertStringContainsString('page[size]=2', $link);

        $link = urldecode($response->json('links.last'));
        $this->assertStringContainsString('page[number]=3', $link);
        $this->assertStringContainsString('page[size]=2', $link);

        $link = urldecode($response->json('links.prev'));
        $this->assertStringContainsString('page[number]=1', $link);
        $this->assertStringContainsString('page[size]=2', $link);

        $link = urldecode($response->json('links.next'));
        $this->assertStringContainsString('page[number]=3', $link);
        $this->assertStringContainsString('page[size]=2', $link);

    }

    /** @test */
    public function can_paginate_and_sort_article(){
        Article::factory()->create(['title' => 'C Title']);
        Article::factory()->create(['title' => 'A Title']);
        Article::factory()->create(['title' => 'B Title']);

        // /articles?sort=title,page[size]=2&page[number]=2
        $url = route('api.v1.articles.index', [
            'sort' => 'title',
            'page' =>[
                'size' => 1,
                'number' => 2,
            ]
        ]);


        $response = $this->getJson($url);
        $response->assertSee([
            'B Title',
        ])->assertDontSee([
            'C Title',
            'A Title',
        ]);

        $this->assertStringContainsString('sort=title', urldecode($response->json('links.first')));
        $this->assertStringContainsString('sort=title', urldecode($response->json('links.last')));
        $this->assertStringContainsString('sort=title', urldecode($response->json('links.prev')));
        $this->assertStringContainsString('sort=title', urldecode($response->json('links.next')));


    }
}
