<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;

class OfferController extends Controller
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

    /**
     * API endpoint to fetch more offers for infinite scrolling
     */
    public function fetchMore(Request $request)
    {
        $perPage = 20;
        $page = $request->input('page', 1);

        $query = Offer::query()
            ->with(['offerer', 'company']);

        // Suche (case-insensitive)
        if ($request->has('title') && !empty($request->title)) {
            $query->where('title', 'ilike', '%' . $request->title . '%');
        }

        if ($request->has('offer_company') && !empty($request->offer_company)) {
            $query->whereHas('company', function($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->offer_company . '%');
            });
        }

        // Filter
        if ($request->has('offerer_type') && !empty($request->offerer_type)) {
            $type = $request->offerer_type === 'Werbender' ? 'referrer' : 'referred';
            $query->where('offerer_type', $type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('average_rating_min') && $request->average_rating_min > 0) {
            $query->whereHas('offerer', function($q) use ($request) {
                $q->where('average_rating', '>=', $request->average_rating_min);
            });
        }

        if ($request->has('created_at_from') && !empty($request->created_at_from)) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }

        // Sortierung
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Mapping der Frontend-Sortierfelder zu Datenbankfeldern
        $sortFieldMap = [
            'created_at' => 'created_at',
            'reward_total_cents' => 'reward_total_cents',
            'reward_offerer_percent' => 'reward_offerer_percent',
            'average_rating' => 'users.average_rating'
        ];

        // Standardsortierung nach created_at, wenn kein gültiges Sortierfeld angegeben wurde
        $dbSortField = $sortFieldMap[$sortField] ?? 'created_at';

        // Spezielle Sortierung für Felder aus verknüpften Tabellen
        if ($dbSortField === 'users.average_rating') {
            $query->join('users', 'offers.offerer_id', '=', 'users.id')
                  ->select('offers.*');
        }

        $query->orderBy($dbSortField, $sortDirection);

        // Paginierung
        $offers = $query->paginate($perPage, ['*'], 'page', $page);

        // Transformation der Daten
        $offersData = $offers->through(function ($offer) {
            return [
                'id' => $offer->id,
                'title' => $offer->title,
                'description' => $offer->description,
                'offerer_type' => $offer->offerer_type == 'referrer' ? 'Werbender' : 'Beworbener',
                'offer_user' => $offer->offerer->name,
                'offer_company' => $offer->company->name,
                'logo_path' => $offer->company->logo_path,
                'reward_total_cents' => $offer->reward_total_cents,
                'reward_offerer_percent' => $offer->reward_offerer_percent,
                'status' => $offer->status,
                'created_at' => $offer->created_at->format('Y-m-d H:i:s'),
                'average_rating' => $offer->offerer->average_rating,
                'industry' => $offer->company->industry,
            ];
        });

        return response()->json([
            'offers' => $offersData->items(),
            'pagination' => [
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
            ],
        ]);
    }
}
