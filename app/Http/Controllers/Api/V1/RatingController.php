<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Rating;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;

class RatingController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ratings = QueryBuilder::for(Rating::class)
            ->allowedFields(['score', 'userMatch.referrer.name', 'userMatch.referred.name'])
            ->allowedFilters(['comment',
                AllowedFilter::operator('score', FilterOperator::DYNAMIC),
                AllowedFilter::operator('created_at', FilterOperator::DYNAMIC),
                AllowedFilter::operator('updated_at', FilterOperator::DYNAMIC),
            ])
            ->allowedIncludes(['userMatch', 'userMatch.referrer', 'userMatch.referred'])
            ->allowedSorts('comment', 'score', 'created_at', 'updated_at')
            ->paginate(15);

        return $ratings;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        //
    }
}
