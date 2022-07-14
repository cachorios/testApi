<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use Illuminate\Http\Request;


class ArticleController extends Controller
{

    public function index() : ArticleCollection
    {
        return new ArticleCollection(Article::all());
    }


    public function show(Article $article)
    {
        return ArticleResource::make($article);
    }

    public function create(Request $request)
    {
        $article = Article::create([
            'title' => $request->input('data.attributes.title'),
            'slug' => $request->input('data.attributes.slug'),
            'content' => $request->input('data.attributes.content')
        ]);


        return ArticleResource::make($article);

    }
}
