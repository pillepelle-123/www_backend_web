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
                ->join('applications', 'user_matches.application_id', '=', 'applications.id')
                ->join('offers', 'applications.offer_id', '=', 'offers.id')
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        // User ist Offerer und bekommt Bewertung vom Applicant
                        $q->where('offers.offerer_id', $user->id)
                          ->where('ratings.direction', 'referred_to_referrer');
                    })->orWhere(function ($q) use ($user) {
                        // User ist Applicant und bekommt Bewertung vom Offerer
                        $q->where('applications.applicant_id', $user->id)
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
