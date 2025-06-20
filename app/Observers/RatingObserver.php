<?php

namespace App\Observers;

use App\Models\Rating;
use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Support\Facades\DB;

class RatingObserver
{
    /**
     * Handle the Rating "created" event.
     */
    public function created(Rating $rating): void
    {
        $this->updateUserAverageRating($rating->user_match_id);
    }

    /**
     * Handle the Rating "updated" event.
     */
    public function updated(Rating $rating): void
    {
        $this->updateUserAverageRating($rating->user_match_id);
    }

    /**
     * Handle the Rating "deleted" event.
     */
    public function deleted(Rating $rating): void
    {
        $this->updateUserAverageRating($rating->user_match_id);
    }

    /**
     * Update the average rating for a user
     */
    private function updateUserAverageRating(int $userMatchId): void
    {

        $applicantId = UserMatch::find($userMatchId)->application->applicant_id;
        $offererId = UserMatch::find($userMatchId)->application->offer->offerer_id;

        // Hole den UserMatch
        // $userMatch = UserMatch::find($userMatchId);
        // if (!$userMatch) return;



        // Berechne den Durchschnitt für beide User
        $this->updateAverageForUser($applicantId);
        $this->updateAverageForUser($offererId);
    }

    /**
     * Update average rating for a specific user
     */
    private function updateAverageForUser(int $userId): void
    {
        $averageRating = DB::table('ratings')
            ->join('user_matches', 'ratings.user_match_id', '=', 'user_matches.id')
            ->join('applications', 'user_matches.application_id', '=', 'applications.id')
            ->join('offers', 'applications.offer_id', '=', 'offers.id')
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    // Wenn der User der Offerer ist
                    $q->where('offers.offerer_id', $userId)
                      ->where('ratings.direction', 'referred_to_referrer');
                })->orWhere(function ($q) use ($userId) {
                    // Wenn der User der Applicant ist
                    $q->where('applications.applicant_id', $userId)
                      ->where('ratings.direction', 'referrer_to_referred');
                });
            })
            ->avg('ratings.score');

        User::where('id', $userId)
            ->update(['average_rating' => $averageRating ?? 0]);
    }
}
