<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Zuerst fügen wir das neue Feld für affiliate_links hinzu
        Schema::table('user_matches', function (Blueprint $table) {
            $table->foreignId('affiliate_link_id')->nullable()->after('user_referred_id');
        });

        // Dann erstellen wir für jeden bestehenden user_match einen affiliate_link
        $userMatches = DB::table('user_matches')->get();
        foreach ($userMatches as $userMatch) {
            // Hole die company_id aus dem offer
            $offer = DB::table('offers')->where('id', $userMatch->offer_id)->first();
            if ($offer) {
                // Erstelle einen neuen affiliate_link
                $affiliateLinkId = DB::table('affiliate_links')->insertGetId([
                    'company_id' => $offer->company_id,
                    'url' => $userMatch->affiliate_link ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Aktualisiere den user_match
                DB::table('user_matches')
                    ->where('id', $userMatch->id)
                    ->update(['affiliate_link_id' => $affiliateLinkId]);
            }
        }

        // Entferne das alte affiliate_link Feld
        // Schema::table('user_matches', function (Blueprint $table) {
        //     $table->dropColumn('affiliate_link');
        // });

        // Aktualisiere das status-Feld
        // Zuerst sichern wir die bestehenden Daten
        $userMatches = DB::table('user_matches')->select('id', 'status')->get();

        // Dann ändern wir das Feld
        Schema::table('user_matches', function (Blueprint $table) {
            // Entferne das alte Enum-Feld
            $table->dropColumn('status');
        });

        Schema::table('user_matches', function (Blueprint $table) {
            // Füge das neue Enum-Feld hinzu
            $table->enum('status', ['opened', 'closed'])
                  ->default('opened')
                  ->after('link_clicked');
        });

        // Konvertiere alte Status-Werte in neue
        foreach ($userMatches as $userMatch) {
            $newStatus = 'opened'; // Standardwert

            // Konvertiere alte Status-Werte in neue
            switch ($userMatch->status) {
                case 'open':
                case 'in_progress':
                    $newStatus = 'opened';
                    break;
                case 'successful':
                case 'unsuccessful':
                case 'inactive':
                    $newStatus = 'closed';
                    break;
            }

            DB::table('user_matches')
                ->where('id', $userMatch->id)
                ->update(['status' => $newStatus]);
        }

        // Füge das neue success_status-Feld hinzu
        Schema::table('user_matches', function (Blueprint $table) {
            $table->enum('success_status', ['pending', 'successful', 'unsuccessful'])
                  ->default('pending')
                  ->after('status');
        });

        // Setze success_status basierend auf dem alten status-Feld
        foreach ($userMatches as $userMatch) {
            $successStatus = 'pending'; // Standardwert

            // Setze success_status basierend auf dem alten status-Feld
            switch ($userMatch->status) {
                case 'successful':
                    $successStatus = 'successful';
                    break;
                case 'unsuccessful':
                    $successStatus = 'unsuccessful';
                    break;
            }

            DB::table('user_matches')
                ->where('id', $userMatch->id)
                ->update(['success_status' => $successStatus]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Zuerst sichern wir die bestehenden Daten
        $userMatches = DB::table('user_matches')
            ->select('id', 'status', 'success_status', 'affiliate_link_id')
            ->get();

        // Füge das alte affiliate_link Feld wieder hinzu
        Schema::table('user_matches', function (Blueprint $table) {
            $table->string('affiliate_link')->nullable()->after('user_referred_id');
        });

        // Kopiere die URLs aus den affiliate_links zurück
        foreach ($userMatches as $userMatch) {
            if ($userMatch->affiliate_link_id) {
                $affiliateLink = DB::table('affiliate_links')
                    ->where('id', $userMatch->affiliate_link_id)
                    ->first();

                if ($affiliateLink) {
                    DB::table('user_matches')
                        ->where('id', $userMatch->id)
                        ->update(['affiliate_link' => $affiliateLink->url]);
                }
            }
        }

        // Entferne das neue affiliate_link_id Feld
        if (Schema::hasColumn('user_matches', 'affiliate_link_id')) {
            // Bei PostgreSQL direkt SQL ausführen, um die Spalte zu entfernen
            // Dies entfernt automatisch alle Constraints
            DB::statement('ALTER TABLE user_matches DROP COLUMN IF EXISTS affiliate_link_id CASCADE');
        }

        // Entferne das success_status-Feld
        Schema::table('user_matches', function (Blueprint $table) {
            $table->dropColumn('success_status');
        });

        // Aktualisiere das status-Feld zurück
        // Zuerst sichern wir die bestehenden Daten
        $userMatches = DB::table('user_matches')->select('id', 'status', 'success_status')->get();

        // Dann ändern wir das Feld zurück
        Schema::table('user_matches', function (Blueprint $table) {
            // Entferne das neue Enum-Feld
            $table->dropColumn('status');
        });

        Schema::table('user_matches', function (Blueprint $table) {
            // Füge das alte Enum-Feld wieder hinzu
            $table->enum('status', ['open', 'in_progress', 'successful', 'unsuccessful', 'inactive'])
                  ->default('open')
                  ->after('link_clicked');
        });

        // Konvertiere neue Status-Werte zurück in alte
        foreach ($userMatches as $userMatch) {
            $oldStatus = 'open'; // Standardwert

            // Konvertiere neue Status-Werte zurück in alte
            if ($userMatch->status === 'opened') {
                $oldStatus = 'open';
            } else if ($userMatch->status === 'closed') {
                if ($userMatch->success_status === 'successful') {
                    $oldStatus = 'successful';
                } else if ($userMatch->success_status === 'unsuccessful') {
                    $oldStatus = 'unsuccessful';
                } else {
                    $oldStatus = 'inactive';
                }
            }

            DB::table('user_matches')
                ->where('id', $userMatch->id)
                ->update(['status' => $oldStatus]);
        }
    }
};
