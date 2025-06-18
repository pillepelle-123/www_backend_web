<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website_url')->nullable();
            $table->string('referral_program_url')->nullable();
            $table->text('description')->nullable();
            $table->enum('admin_status', ['pending', 'active', 'inactive', 'review', 'archived'])->default('pending');
				$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
