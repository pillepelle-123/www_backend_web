<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Rating::query()
            ->join('offers', 'ratings.offer_id', '=', 'offers.id')
            ->join('users as users_from', 'ratings.from_user_id', '=', 'users_from.id')
            ->join('users as users_to', 'ratings.to_user_id', '=', 'users_to.id')
            // ->join('companies', 'offers.company_id', '=', 'companies.id')
            ->select('ratings.id', 'offers.id as offers-id', 'offers.user_id as offers-user_id', 'offers.offer_title as offers-offer_title', 'offers.reward_total_cents as offers-reward_total_cents', 'offers.reward_offerer_percent as offers-reward_offerer_percent', 'offers.status as offers-status', 'users_from.name as users_from-name', 'users_to.name as users_to-name')
        ;

        $columns = array_keys($query->first()->toArray());

        // Search Parameter
        [$key, $value] = $this->getFirstSearchParam($request->query->all());
        if($key && $value) {
            if(array_search($key, $columns))
            if (str_contains($key, '-')) {
                $key = str_replace('-', '.', $key);
            };
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        // Filter-Parameter
        $filters = $request->only(['offers-id', 'offers-user_id', 'offers-offered_by_type', 'offers-offer_title', 'offers-reward_total_cents', 'created_at', 'updated_at', 'offers-status', 'users_from-name', 'users_to-name']);
        foreach ($filters as $field => $value) {
            // Felder aus fremden Tabellen werden mit "tabelle-feld_name" angesprochen. Hier wird - (Bindestrich) als Trenner genommen und im folgenden durch . ersetzt, da . (Punkt) im URL-Paramter nicht vorkommen darf.
            if (str_contains($field, '-')) {
                $field = str_replace('-', '.', $field);
            };
            $query->where($field, $value);
        }

        // Sort-Parameter
        if ($request->has('sort_by')) {
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($request->input('sort_by'), $sortOrder);
        }

        $perPage = $request->input('per_page', 10);
        $paginatedItems = $query->paginate($perPage);

        return response()->json($paginatedItems);
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
