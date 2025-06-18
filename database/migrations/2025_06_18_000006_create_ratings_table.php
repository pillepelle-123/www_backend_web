<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_match_id')->constrained('user_matches')->cascadeOnDelete();
            $table->enum('direction', ['referrer_to_referred', 'referred_to_referrer'])->default('referrer_to_referred');
            $table->unsignedTinyInteger('score')->from(1)->to(5);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
