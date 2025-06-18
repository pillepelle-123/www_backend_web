<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Offer;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    // public function index() {
    //     return Inertia::render('offers/index', []);
    // }
    public function index(Request $request)
    {
        $perPage = 20; // Anzahl der Datensätze pro Seite

        $query = Offer::query()
            ->with(['user', 'company']);

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
        if ($request->has('offered_by_type') && !empty($request->offered_by_type)) {
            $type = $request->offered_by_type === 'Werbender' ? 'referrer' : 'referred';
            $query->where('offered_by_type', $type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('average_rating_min') && $request->average_rating_min > 0) {
            $query->whereHas('user', function($q) use ($request) {
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
            $query->join('users', 'offers.user_id', '=', 'users.id')
                  ->select('offers.*');
        }

        $query->orderBy($dbSortField, $sortDirection);

        // Paginierung
        $offers = $query->paginate($perPage);

        // Transformation der Daten
        $offersData = $offers->through(function ($offer) {
            return [
                'id' => $offer->id,
                'title' => $offer->title,
                'description' => $offer->description,
                'offered_by_type' => $offer->offered_by_type == 'referrer' ? 'Werbender' : 'Beworbener',
                'offer_user' => $offer->user->name,
                'offer_company' => $offer->company->name,
                'logo_path' => $offer->company->logo_path,
                'reward_total_cents' => $offer->reward_total_cents,
                'reward_offerer_percent' => $offer->reward_offerer_percent,
                'status' => $offer->status,
                'created_at' => $offer->created_at->format('Y-m-d H:i:s'),
                'average_rating' => $offer->user->average_rating,
                'industry' => $offer->company->industry,
            ];
        });

        return Inertia::render('offers/index', [
            'offers' => $offersData,
            'pagination' => [
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $offer = Offer::with(['user', 'company'])->findOrFail($id);
        $user = Auth::user();
        
        // Prüfe, ob der Benutzer bereits eine Anfrage für dieses Angebot gestellt hat
        $application = \App\Models\Application::where('offer_id', $offer->id)
            ->where('applicant_id', $user->id)
            ->first();
            
        $offerData = [
            'id' => $offer->id,
            'title' => $offer->title,
            'description' => $offer->description,
            'offered_by_type' => $offer->offered_by_type == 'referrer' ? 'Werbender' : 'Beworbener',
            'offer_user' => $offer->user->name,
            'offer_company' => $offer->company->name,
            'logo_path' => $offer->company->logo_path,
            'reward_total_cents' => $offer->reward_total_cents,
            'reward_offerer_percent' => $offer->reward_offerer_percent,
            'status' => $offer->status,
            'industry' => $offer->company->industry,
            'created_at' => $offer->created_at->format('Y-m-d H:i:s'),
            'average_rating' => $offer->user->average_rating,
            'has_application' => $application ? true : false,
            'application_status' => $application ? $application->status : null,
            'application_id' => $application ? $application->id : null,
            'is_owner' => $user->id === $offer->user_id,
        ];

        return Inertia::render('offers/show', [
            'offer' => $offerData,
        ]);
    }

    public function create()
    {
        $companies = Company::select('id', 'name')->get();
        return inertia('offers/create', [
            'companies' => $companies
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'reward_total_cents' => 'required|integer|min:0|max:100000',
            'reward_offerer_percent' => 'required|numeric|min:0|max:1', // Dezimalwert, z.B. 0.5
            'offered_by_type' => 'required|in:referrer,referred',
        ]);

        $offer = Offer::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'company_id' => $validated['company_id'],
            'reward_total_cents' => $validated['reward_total_cents'],
            'reward_offerer_percent' => $validated['reward_offerer_percent'],
            'user_id' => Auth::id(),
            'status' => 'active',
            'offered_by_type' => $validated['offered_by_type'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('web.offers.index')
            ->with('success', 'Offer created successfully.');
    }

    /**
     * API endpoint to fetch more offers for infinite scrolling
     */
    public function fetchMore(Request $request)
    {
        $perPage = 20;
        $page = $request->input('page', 1);

        $query = Offer::query()
            ->with(['user', 'company']);

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
        if ($request->has('offered_by_type') && !empty($request->offered_by_type)) {
            $type = $request->offered_by_type === 'Werbender' ? 'referrer' : 'referred';
            $query->where('offered_by_type', $type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('average_rating_min') && $request->average_rating_min > 0) {
            $query->whereHas('user', function($q) use ($request) {
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
            $query->join('users', 'offers.user_id', '=', 'users.id')
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
                'offered_by_type' => $offer->offered_by_type == 'referrer' ? 'Werbender' : 'Beworbener',
                'offer_user' => $offer->user->name,
                'offer_company' => $offer->company->name,
                'logo_path' => $offer->company->logo_path,
                'reward_total_cents' => $offer->reward_total_cents,
                'reward_offerer_percent' => $offer->reward_offerer_percent,
                'status' => $offer->status,
                'created_at' => $offer->created_at->format('Y-m-d H:i:s'),
                'average_rating' => $offer->user->average_rating,
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
