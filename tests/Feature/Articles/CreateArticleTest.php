<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_create_article()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data'=> [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New Article',
                    'slug' => 'new-article',
                    'content' => 'New Article Content',
                ],
            ],

        ]);

        $response->assertCreated();

        $article = Article::first();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article),
                ],
            ]
        ]);
    }

    /** @test */
    public function title_must_be_at_least_4_character()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data'=> [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New',
                    'slug' => 'new-article',
                    'content' => 'New Article Content',
                ],
            ],

        ]);

        $response->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function title_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data'=> [
                'type' => 'articles',
                'attributes' => [
                    //'title' => 'New article',
                    'slug' => 'new-article',
                    'content' => 'New Article Content',
                ],
            ],

        ]);
        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data'=> [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New article',
                    //'slug' => 'new-article',
                    'content' => 'New Article Content',
                ],
            ],

        ]);

        //$response->assertJsonValidationErrors('data.attributes.slug');

        $response->assertJsonApiValidationErrors('slug');
    }
    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data'=> [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New article',
                    'slug' => 'new-article',
                    //'content' => 'New Article Content',
                ],
            ],

        ]);

        $response->assertJsonApiValidationErrors('content');
    }
}
