<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_referred_id')->constrained('users')->cascadeOnDelete();
            // $table->string('affiliate_link');
            $table->boolean('link_clicked')->default(false);
            $table->enum('status', ['open', 'in_progress', 'successful', 'unsuccessful', 'inactive']);
            $table->text('reason_unsuccessful_referrer')->nullable();
            $table->text('reason_unsuccessful_referred')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_matches');
    }
};
