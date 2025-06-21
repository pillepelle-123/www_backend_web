<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserMatchController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $userMatches = UserMatch::with([
            'application.offer.company',
            'application.offer.offerer',
            'application.applicant',
            'affiliateLink'
        ])
        ->whereHas('application', function ($query) use ($user) {
            $query->where('applicant_id', $user->id)
                  ->orWhereHas('offer', function ($offerQuery) use ($user) {
                      $offerQuery->where('offerer_id', $user->id);
                  });
        })
        ->latest('created_at')
        ->get()
        ->map(function ($userMatch) use ($user) {
            $application = $userMatch->application;
            $offer = $application->offer;
            $isApplicant = $application->applicant_id === $user->id;

            // Berechne Belohnung für eingeloggten User (wie in OfferCard)
            $rewardTotalEuro = $offer->reward_total_cents / 100;
            $userReward = $isApplicant
                ? (1 - $offer->reward_offerer_percent) * $offer->reward_total_cents / 100
                : $offer->reward_offerer_percent * $offer->reward_total_cents / 100;

            // Bestimme Partner und Rollen
            $partner = $isApplicant ? $offer->offerer : $application->applicant;
            $userRole = null;
            $partnerRole = null;

            if ($offer->offerer_type === 'referrer') {
                $userRole = $isApplicant ? 'referred' : 'referrer';
                $partnerRole = $isApplicant ? 'referrer' : 'referred';
            } else {
                $userRole = $isApplicant ? 'referrer' : 'referred';
                $partnerRole = $isApplicant ? 'referred' : 'referrer';
            }

            return [
                'id' => $userMatch->id,
                'title' => $offer->title,
                'company_name' => $offer->company->name,
                'status' => $userMatch->status,
                'success_status' => $userMatch->success_status,
                'created_at' => $userMatch->created_at->format('Y-m-d H:i:s'),
                'is_applicant' => $isApplicant,
                'user_role' => $userRole,
                'partner_name' => $partner->name,
                'partner_role' => $partnerRole,
                'user_reward' => $userReward,
                'is_archived' => $userMatch->status === 'closed',
            ];
        });

        return Inertia::render('user-matches/index', [
            'userMatches' => $userMatches,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $userMatch = UserMatch::with([
            'application.offer.company',
            'application.offer.offerer',
            'application.applicant',
            'affiliateLink'
        ])->findOrFail($id);

        $application = $userMatch->application;
        $offer = $application->offer;

        // Prüfe Berechtigung
        if ($user->id !== $application->applicant_id && $user->id !== $offer->offerer_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        $isApplicant = $application->applicant_id === $user->id;

        // Berechne Belohnung für eingeloggten User (wie in OfferCard)
        $rewardTotalEuro = $offer->reward_total_cents / 100;
        $userReward = $isApplicant
            ? (1 - $offer->reward_offerer_percent) * $offer->reward_total_cents / 100
            : $offer->reward_offerer_percent * $offer->reward_total_cents / 100;

        $userRewardPercent = $offer->reward_offerer_percent * 100;

        // Bestimme Partner und Rollen
        $partner = $isApplicant ? $offer->offerer : $application->applicant;

        $partnerReward = $offer->reward_total_cents / 100 - $userReward;

        $partnerRewardPercent = (1- $offer->reward_offerer_percent) * 100;

        $userRole = null;
        $partnerRole = null;

        if ($offer->offerer_type === 'referrer') {
            $userRole = $isApplicant ? 'referred' : 'referrer';
            $partnerRole = $isApplicant ? 'referrer' : 'referred';
        } else {
            $userRole = $isApplicant ? 'referrer' : 'referred';
            $partnerRole = $isApplicant ? 'referred' : 'referrer';
        }

        return Inertia::render('user-matches/show', [
            'userMatch' => [
                'id' => $userMatch->id,
                'title' => $offer->title,
                'description' => $offer->description,
                'company_name' => $offer->company->name,
                'affiliate_url' => $userMatch->affiliateLink->url ?? null,
                'status' => $userMatch->status,
                'success_status' => $userMatch->success_status,
                'created_at' => $userMatch->created_at->format('Y-m-d H:i:s'),
                'is_applicant' => $isApplicant,
                'user_name' => $user->name,
                'user_role' => $userRole,
                'partner_name' => $partner->name,
                'partner_role' => $partnerRole,
                'reward_total_euro' => $rewardTotalEuro,
                'user_reward' => $userReward,
                'user_reward_percent' => $userRewardPercent,
                'partner_reward_percent' => $partnerRewardPercent,
                'partner_reward' => $partnerReward,
                'application_message' => $application->message,
            ],
        ]);
    }

    public function markSuccessful($id)
    {
        $userMatch = UserMatch::findOrFail($id);

        // Prüfe Berechtigung
        $user = Auth::user();
        if ($user->id !== $userMatch->application->applicant_id &&
            $user->id !== $userMatch->application->offer->offerer_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        $userMatch->update([
            'status' => 'closed',
            'success_status' => 'successful',
        ]);

        return redirect()->route('web.user-matches.index')
            ->with('success', 'Match als erfolgreich markiert.');
    }

    public function dissolve($id)
    {
        $userMatch = UserMatch::findOrFail($id);

        // Prüfe Berechtigung
        $user = Auth::user();
        if ($user->id !== $userMatch->application->applicant_id &&
            $user->id !== $userMatch->application->offer->offerer_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        $userMatch->update([
            'status' => 'closed',
            'success_status' => 'unsuccessful',
        ]);

        return redirect()->route('web.user-matches.index')
            ->with('success', 'Match aufgelöst.');
    }

    public function report($id)
    {
        // Placeholder für Melden-Funktionalität
        console_log("Match $id wurde gemeldet");

        return redirect()->route('web.user-matches.index')
            ->with('info', 'Match wurde gemeldet.');
    }
}
