<?php
namespace App\JsonApi;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JsonApiQueryBuilder
{
    public function allowedSorts() : Closure
    {
        return function ($allowedSorts) {
            /** @var Builder $this */

            if(request()->filled('sort')) {

                $sortFields = explode(',', request()->input('sort'));

                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    $sortField = ltrim($sortField, '-');

                    abort_unless(in_array($sortField, $allowedSorts), 400);


                    $this->orderBy($sortField, $sortDirection);
                }
            }


            return $this;
        };
    }


    public function jsonPaginate() : Closure
    {
        return function () {
            /** @var Builder $this */
            //paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
            return $this->paginate(
                $perPage = request()->input('page.size', 5),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request()->input('page.number', 1)
            )
                ->appends(request()->only('sort','page.size'));

        };
    }
}
