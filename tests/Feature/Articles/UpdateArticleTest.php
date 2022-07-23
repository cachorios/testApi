<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_update_article()
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Update Article',
            'slug' => $article->slug,
            'content' => 'UpdateNew Article Content',
        ]);
        $response->assertOk();

        $response->assertHeader(
            'Location', route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' => [
                    'title' => 'Update Article',
                    'slug' => $article->slug,
                    'content' => 'UpdateNew Article Content',
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
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Up',
            'slug' => 'update-article',
            'content' => 'Update Article Content',
        ]);

        $response->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update',$article), [
            //'title' => 'Update article',
            'slug' => 'update-article',
            'content' => 'Update Article Content',
        ]);
        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Update article',
            //'slug' => 'update-article',
            'content' => 'Update Article Content',
        ]);
        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update',$article1), [
            'title' => 'New article',
            'slug' => $article2->slug,
            'content' => 'New Article Content',
        ]);
        $response->assertJsonApiValidationErrors('slug');
    }


    /** @test */
    public function slug_must_only_contain_letters_number_and_dashes()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'New article',
            'slug' => 'new-slug####%',
            'content' => 'New Article Content',
        ]);
        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
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
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
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
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
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
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Update article',
            'slug' => 'update-article',
            //'content' => 'Update Article Content',
        ]);

        $response->assertJsonApiValidationErrors('content');
    }
}
