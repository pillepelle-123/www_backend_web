<?php

namespace App\Observer;

use App\Models\Rating;
use App\Enums\RatingDirection;

class RatingObserver
{
    public function creating(Rating $rating)
    {
        $existingRatings = Rating::where('match_id', $rating->match_id)
            ->whereIn('direction', [
                RatingDirection::REFERRER_TO_REFERRED->value,
                RatingDirection::REFERRED_TO_REFERRER->value
            ])
            ->count();

        if ($existingRatings >= 2) {
            throw new \Exception('Maximal 2 Ratings pro Match erlaubt');
        }
    }
}
