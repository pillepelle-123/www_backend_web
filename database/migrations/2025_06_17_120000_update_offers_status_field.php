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
        // Zuerst sichern wir die bestehenden Daten
        $offers = DB::table('offers')->select('id', 'status')->get();
        
        // Dann ändern wir das Feld
        Schema::table('offers', function (Blueprint $table) {
            // Entferne das alte Enum-Feld
            $table->dropColumn('status');
        });
        
        Schema::table('offers', function (Blueprint $table) {
            // Füge das neue Enum-Feld hinzu
            $table->enum('status', ['draft', 'live', 'hidden', 'matched', 'deleted'])
                  ->default('draft')
                  ->before('created_at');
        });
        
        // Konvertiere alte Status-Werte in neue
        foreach ($offers as $offer) {
            $newStatus = 'draft'; // Standardwert
            
            // Konvertiere alte Status-Werte in neue
            switch ($offer->status) {
                case 'active':
                    $newStatus = 'live';
                    break;
                case 'inactive':
                    $newStatus = 'hidden';
                    break;
                case 'closed':
                case 'matched':
                    $newStatus = 'matched';
                    break;
            }
            
            DB::table('offers')
                ->where('id', $offer->id)
                ->update(['status' => $newStatus]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Zuerst sichern wir die bestehenden Daten
        $offers = DB::table('offers')->select('id', 'status')->get();
        
        // Dann ändern wir das Feld zurück
        Schema::table('offers', function (Blueprint $table) {
            // Entferne das neue Enum-Feld
            $table->dropColumn('status');
        });
        
        Schema::table('offers', function (Blueprint $table) {
            // Füge das alte Enum-Feld wieder hinzu
            $table->enum('status', ['active', 'inactive', 'closed', 'matched'])
                  ->default('active')
                  ->before('created_at');
        });
        
        // Konvertiere neue Status-Werte zurück in alte
        foreach ($offers as $offer) {
            $oldStatus = 'active'; // Standardwert
            
            // Konvertiere neue Status-Werte zurück in alte
            switch ($offer->status) {
                case 'live':
                    $oldStatus = 'active';
                    break;
                case 'hidden':
                case 'draft':
                    $oldStatus = 'inactive';
                    break;
                case 'matched':
                    $oldStatus = 'matched';
                    break;
                case 'deleted':
                    $oldStatus = 'inactive';
                    break;
            }
            
            DB::table('offers')
                ->where('id', $offer->id)
                ->update(['status' => $oldStatus]);
        }
    }
};