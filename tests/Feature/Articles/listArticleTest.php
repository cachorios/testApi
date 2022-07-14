<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class listArticleTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fech_a_single_article()
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show',$article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show',$article) ,
                ],

            ]
        ]);
    }

    /** @test */
    public function can_fetch_all_articles()
    {
        $articles = Article::factory()->count(3)->create();
        $response = $this->getJson(route('api.v1.articles.index'));

        $response->assertExactJson([
            'data' => array_map(function ( $art) {
                $article = new Article();
                $article->fill($art);
                return [
                    'type' => 'articles',
                    'id' => (string) $article->id,
                    'attributes' => [
                        'title' => $article->title,
                        'slug' => $article->slug,
                        'content' => $article->content,
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show',$article) ,
                    ],
                ];
            }, $articles->toArray()),
            'links' => [
                'self' => route('api.v1.articles.index') ,
            ],
        ]);
    }


}
