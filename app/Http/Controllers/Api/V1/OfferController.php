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
        $perPage = $request->get('per_page', 20);
        
        $query = QueryBuilder::for(Offer::class)
            ->allowedFilters([
                AllowedFilter::exact('offered_by_type'), 
                AllowedFilter::exact('status'),
                AllowedFilter::partial('offer_title'),
                AllowedFilter::partial('company.name'),
                AllowedFilter::operator('reward_total_cents', FilterOperator::DYNAMIC),
                AllowedFilter::operator('reward_offerer_percent', FilterOperator::DYNAMIC),
                AllowedFilter::operator('created_at', FilterOperator::DYNAMIC),
                AllowedFilter::callback('average_rating_min', function ($query, $value) {
                    $query->whereHas('user', function ($q) use ($value) {
                        $q->where('average_rating', '>=', $value)
                          ->whereNotNull('average_rating');
                    });
                }),
            ])
            ->allowedIncludes(['user', 'company'])
            ->allowedSorts('offer_title', 'reward_total_cents', 'reward_offerer_percent', 'created_at', 'user.average_rating')
            ->defaultSort('-created_at') // Default sort by created_at desc
            ->with(['user', 'company']);
        
        $offers = $query->paginate($perPage);

        // Transform the data to match the frontend expectations
        $offers->getCollection()->transform(function ($offer) {
            return [
                'id' => $offer->id,
                'title' => $offer->offer_title,
                'description' => $offer->offer_description,
                'offered_by_type' => $offer->offered_by_type == 'referrer' ? 'Werbender' : 'Beworbener',
                'offer_user' => $offer->user->name,
                'offer_company' => $offer->company->name,
                'logo_path' => $offer->company->logo_path,
                'reward_total_cents' => $offer->reward_total_cents,
                'reward_offerer_percent' => $offer->reward_offerer_percent,
                'status' => $offer->status,
                'created_at' => $offer->created_at->format('Y-m-d H:i:s'),
                'average_rating' => $offer->user->average_rating ?? 0,
                'industry' => $offer->company->industry,
            ];
        });

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
