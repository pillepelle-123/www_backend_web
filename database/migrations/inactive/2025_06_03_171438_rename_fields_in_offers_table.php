<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldsInOffersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            // 1. offered_by → offered_by_type umbenennen
            $table->renameColumn('offered_by', 'offered_by_type');

            // 2. reward_split_percent → reward_offerer_percent umbenennen
            $table->renameColumn('reward_split_percent', 'reward_offerer_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            // Rollback: Umbenennungen rückgängig machen
            $table->renameColumn('offered_by_type', 'offered_by');
            $table->renameColumn('reward_offerer_percent', 'reward_split_percent');
        });
    }
}
