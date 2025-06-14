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
        // Hole den UserMatch
        $userMatch = UserMatch::find($userMatchId);
        if (!$userMatch) return;

        // Berechne den Durchschnitt für beide User
        $this->updateAverageForUser($userMatch->user_referrer_id);
        $this->updateAverageForUser($userMatch->user_referred_id);
    }

    /**
     * Update average rating for a specific user
     */
    private function updateAverageForUser(int $userId): void
    {
        $averageRating = DB::table('ratings')
            ->join('user_matches', 'ratings.user_match_id', '=', 'user_matches.id')
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    // Wenn der User der Referrer ist
                    $q->where('user_matches.user_referrer_id', $userId)
                      ->where('ratings.direction', 'referred_to_referrer');
                })->orWhere(function ($q) use ($userId) {
                    // Wenn der User der Referred ist
                    $q->where('user_matches.user_referred_id', $userId)
                      ->where('ratings.direction', 'referrer_to_referred');
                });
            })
            ->avg('ratings.score');

        User::where('id', $userId)
            ->update(['average_rating' => $averageRating ?? 0]);
    }
}
