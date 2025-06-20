<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Rating;
use App\Models\Application;
use App\Observers\RatingObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        Rating::observe(RatingObserver::class);
        
        // Teile die Anzahl der ungelesenen Nachrichten mit allen Views
        Inertia::share('unreadApplicationsCount', function () {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Prüfe, ob die Tabelle existiert, bevor wir abfragen
                try {
                    if (!\Schema::hasTable('applications')) {
                        return 0;
                    }
                    
                    // Zähle ungelesene Nachrichten für den Benutzer
                    return Application::join('offers', 'applications.offer_id', '=', 'offers.id')
                        ->where(function ($query) use ($user) {
                            $query->where('applications.applicant_id', $user->id)
                                  ->where('applications.is_read_by_applicant', false);
                        })->orWhere(function ($query) use ($user) {
                            $query->where('offers.offerer_id', $user->id)
                                  ->where('applications.is_read_by_owner', false);
                        })->count();
                } catch (\Exception $e) {
                    return 0;
                }
            }
            
            return 0;
        });
    }
}
