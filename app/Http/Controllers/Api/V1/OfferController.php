<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Offer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;

class OfferController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $offers = QueryBuilder::for(Offer::class)
            // ->join('users', 'offers.user_id', 'users.id')
            // ->join('companies', 'offers.company_id', 'companies.id')
            // ->select([
            //     'offers.*',
            //     'users.name as user_name', // Alias für user name
            //     'companies.name as company_name' // Alias für company name
            // ])
            ->allowedFilters(['offerer_type', 'title', 'description', 'status', 'created_at', 'updated_at',
            AllowedFilter::exact('user.name'),
            AllowedFilter::operator('reward_total_cents', FilterOperator::DYNAMIC),
            AllowedFilter::operator('reward_offerer_percent', FilterOperator::DYNAMIC),
            ])
            ->allowedFields('user.name')
            ->allowedIncludes(['user', 'company'])
            ->allowedSorts('title', 'reward_total_cents', 'reward_offerer_percent', 'created_at')
            ->with(['user', 'company'])
            ->paginate(15);
        return $offers;
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
    public function show(Offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        //
    }
}
