<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query Aufbau
        $query = Offer::query()
            ->join('users', 'offers.user_id', '=', 'users.id')
            ->join('companies', 'offers.company_id', '=', 'companies.id')
            ->select('offers.*', 'users.name as users-name', 'companies.name as companies-name')
        ;

        // Search-Parameter
        [$key, $value] = $this->getFirstSearchParam($request->query->all());
        if($key && $value) {
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        // Filter-Parameter
        $filters = $request->only(['offered_by_type', 'users-name', 'companies-name', 'offer_title', 'reward_total_cents', 'reward_offerer_percent', 'status', 'created_at', 'updated_at']);
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



            $sort_column = $request->input('sort_by');


            $query->orderBy($sort_column, $sortOrder);
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
