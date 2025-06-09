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
                    'offered_by_type' => $offer->offered_by_type,
                    'offer_user' => $offer->user->name,
                    'reward_total_cents' => $offer->reward_total_cents / 100, // Umrechnung in Euro
                    'status' => $offer->status, //->isPast() ? 'expired' : 'active',
                ];
            });

        return Inertia::render('offers/list', [
            'offers' => $offers,
        ]);
    }
}
