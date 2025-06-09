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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_match_id')
                ->constrained('user_matches')
                ->cascadeOnDelete();
            $table->enum('direction', ['referrer_to_referred', 'referred_to_referrer']);
            $table->unsignedTinyInteger('score')->from(1)->to(5);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('ratings', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('offer_id')->constrained()->onDelete('cascade');
//             $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
//             $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
//             $table->unsignedTinyInteger('score')->from(1)->to(5);
//             $table->text('comment')->nullable();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('ratings');
//     }
// };

