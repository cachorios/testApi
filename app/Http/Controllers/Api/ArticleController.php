<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;

class ArticleController extends Controller
{

    public function index() : ArticleCollection
    {
        $articles = Article::allowedSorts(['title', 'content']);
        return  ArticleCollection::make( $articles->jsonPaginate() );
    }

    public function show(Article $article)
    {
        return ArticleResource::make($article);
    }

    public function store(SaveArticleRequest $request)
    {
        $article = Article::create($request->validated());
        return ArticleResource::make($article);
    }

    public function update(SaveArticleRequest $request, Article $article)
    {
        $article->update($request->validated());
        return ArticleResource::make($article);
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response()->noContent();
    }
}
