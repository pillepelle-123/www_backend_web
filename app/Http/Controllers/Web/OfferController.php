<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Offer;

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
                    'offered_by_type' => $offer->offered_by_type,
                    'offer_user' => $offer->user->name,
                    'offer_company' => $offer->company->name,
                    'logo_url' => $offer->company->logo_url,
                    'reward_total_cents' => $offer->reward_total_cents / 100, // Umrechnung in Euro
                    'reward_offerer_percent' => $offer->reward_offerer_percent,
                    'status' => $offer->status, //->isPast() ? 'expired' : 'active',
                ];
            });

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
        ];

        return Inertia::render('offers/show', [
            'offer' => $offerData,
        ]);
    }
}
