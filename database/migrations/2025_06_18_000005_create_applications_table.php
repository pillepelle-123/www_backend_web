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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('offer_owner_id')->constrained('users')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->boolean('is_read_by_applicant')->default(false);
            $table->boolean('is_read_by_owner')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected', 'retracted'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
