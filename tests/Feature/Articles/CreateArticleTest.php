<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_create_article()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'New Article',
                    'slug' => 'new-article',
                    'content' => 'New Article Content',
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
                    'title' => 'New',
                    'slug' => 'new-article',
                    'content' => 'New Article Content',
        ]);

        $response->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function title_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
          //  'title' => 'New article',
            'slug' => 'new-article',
            'content' => 'New Article Content',
         ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            //'slug' => 'new-article',
            'content' => 'New Article Content',
        ]);
        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => $article->slug,
            'content' => 'New Article Content',
        ]);
        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_number_and_dashes()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'new-slug####%',
            'content' => 'New Article Content',
        ]);
        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'new_slug',
            'content' => 'New Article Content',
        ])
            ->assertSee(trans('validation.no_underscore',['attribute'=>'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug')
        ;
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => '-start-with-dash',
            'content' => 'New Article Content',
        ])
            ->assertSee(trans('validation.no_starting_dashes',['attribute'=>'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'start-with-dash-',
            'content' => 'New Article Content',
        ])
            ->assertSee(trans('validation.no_ending_dashes',['attribute'=>'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'new-article',
            //'content' => 'New Article Content',
        ]);

        $response->assertJsonApiValidationErrors('content');
    }
}
