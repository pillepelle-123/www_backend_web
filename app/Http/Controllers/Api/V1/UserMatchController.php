<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UserMatch;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
// use App\Http\Resources\V1\UserMatchResource;
// use App\Http\Resources\V1\UserMatchCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserMatchController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userMatches = QueryBuilder::for(UserMatch::class)
            ->allowedFields(['affiliate_link', 'link_clicked', 'status', 'reason_unsuccessful_referrer', 'reason_unsuccessful_referred'])
            ->allowedFilters(
                ['comment', 'affiliate_link', 'link_clicked', 'status', 'reason_unsuccessful_referrer', 'reason_unsuccessful_referred', 'referrer.name',
                AllowedFilter::callback('referrer_or_referred', function ($query, $value) {
                $query->whereHas('referrer', fn($q) => $q->where('name', $value))
                    ->orWhereHas('referred', fn($q) => $q->where('name', $value));
                })
            ])
            ->allowedIncludes(['offer', 'referrer', 'referred'])
            ->allowedSorts('status', 'referrer.name')
            ->paginate(15);

        return $userMatches;
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
    public function show(UserMatch $userMatch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserMatch $userMatch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMatch $userMatch)
    {
        //
    }
}
