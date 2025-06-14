<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Rating;
use App\Models\UserMatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserRatings extends Command
{
    protected $signature = 'users:update-ratings';
    protected $description = 'Update average ratings for all users';

    public function handle()
    {
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));

        foreach ($users as $user) {
            $averageRating = DB::table('ratings')
                ->join('user_matches', 'ratings.user_match_id', '=', 'user_matches.id')
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        // User ist Referrer und bekommt Bewertung von Referred
                        $q->where('user_matches.user_referrer_id', $user->id)
                          ->where('ratings.direction', 'referred_to_referrer');
                    })->orWhere(function ($q) use ($user) {
                        // User ist Referred und bekommt Bewertung vom Referrer
                        $q->where('user_matches.user_referred_id', $user->id)
                          ->where('ratings.direction', 'referrer_to_referred');
                    });
                })
                ->avg('ratings.score');

            $user->update(['average_rating' => $averageRating ?? 0]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All user ratings have been updated!');
    }
}
