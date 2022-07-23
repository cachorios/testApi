<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('articles',[\App\Http\Controllers\Api\ArticleController::class,'index'])->name('api.v1.articles.index');
//Route::get('articles/{article}',[\App\Http\Controllers\Api\ArticleController::class,'show'])->name('api.v1.articles.show');
//Route::post('articles',[\App\Http\Controllers\Api\ArticleController::class,'store'])->name('api.v1.articles.store');
//Route::patch('articles/{article}',[\App\Http\Controllers\Api\ArticleController::class,'update'])->name('api.v1.articles.update');
//Route::delete('articles/{article}',[\App\Http\Controllers\Api\ArticleController::class,'destroy'])->name('api.v1.articles.destroy');

Route::apiResource('articles',ArticleController::class)->names('api.v1.articles');

