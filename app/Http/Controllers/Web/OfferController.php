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
    public function index()
    {
        $offers = Offer::query()
            // ->where('user_id', auth()->id())
            ->get()
            ->map(function ($offer) {
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
                    'status' => $offer->status, //->isPast() ? 'expired' : 'active',
                    'created_at' => $offer->created_at->format('Y-m-d H:i:s'),
                    'average_rating' => $offer->user->average_rating,
                    'industry' => $offer->company->industry,
                ];
            })
            ->sortByDesc('created_at')
            ->values()  // Konvertiert die Collection in ein Array
            ->toArray(); // Stellt sicher, dass es ein Array ist

        return Inertia::render('offers/index', [
            'offers' => $offers,
        ]);
    }

    public function show($id)
    {
        $offer = Offer::with(['user', 'company'])->findOrFail($id);

        $offerData = [
            'id' => $offer->id,
            'title' => $offer->offer_title,
            'description' => $offer->offer_description,
            'offered_by_type' => $offer->offered_by_type,
            'offer_user' => $offer->user->name,
            'offer_company' => $offer->company->name,
            'logo_url' => $offer->company->logo_url,
            'reward_total_cents' => $offer->reward_total_cents / 100,
            'reward_offerer_percent' => $offer->reward_offerer_percent,
            'status' => $offer->status,
            'industry' => $offer->company->industry,
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
            'offer_title' => 'required|string|max:255',
            'offer_description' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'reward_total_cents' => 'required|integer|min:0|max:100000',
            'reward_offerer_percent' => 'required|numeric|min:0|max:1', // Dezimalwert, z.B. 0.5
            'offered_by_type' => 'required|in:referrer,referred',
        ]);

        $offer = Offer::create([
            'offer_title' => $validated['offer_title'],
            'offer_description' => $validated['offer_description'],
            'company_id' => $validated['company_id'],
            'reward_total_cents' => $validated['reward_total_cents'],
            'reward_offerer_percent' => $validated['reward_offerer_percent'],
            'user_id' => Auth::id(),
            'status' => 'active',
            'offered_by_type' => $validated['offered_by_type'],
        ]);

        return redirect()->route('offers.index')
            ->with('success', 'Offer created successfully.');
    }
}
