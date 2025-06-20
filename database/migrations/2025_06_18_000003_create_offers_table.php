<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('offerer_type', ['referrer', 'referred'])->default('referrer');
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('reward_total_cents');
            $table->decimal('reward_offerer_percent', 3, 2)->default(0.5);
            $table->enum('status', ['draft', 'live', 'hidden', 'matched', 'deleted'])->default('draft');
            $table->enum('admin_status', ['active', 'inactive', 'review', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
