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
        // Füge admin_status zu companies hinzu
        Schema::table('companies', function (Blueprint $table) {
            $table->enum('admin_status', ['pending', 'active', 'inactive', 'review', 'archived'])
                  ->default('pending')
                  ->before('created_at');
        });
        
        // Füge admin_status zu users hinzu
        Schema::table('users', function (Blueprint $table) {
            $table->enum('admin_status', ['active', 'inactive', 'review', 'archived'])
                  ->default('active')
                  ->before('created_at');
        });
        
        // Füge admin_status zu offers hinzu
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('admin_status', ['active', 'inactive', 'review', 'archived'])
                  ->default('active')
                  ->before('created_at');
        });
        
        // Füge admin_status zu affiliate_links hinzu
        Schema::table('affiliate_links', function (Blueprint $table) {
            $table->enum('admin_status', ['pending', 'active', 'inactive', 'review', 'archived'])
                  ->default('pending')
                  ->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne admin_status von companies
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('admin_status');
        });
        
        // Entferne admin_status von users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_status');
        });
        
        // Entferne admin_status von offers
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('admin_status');
        });
        
        // Entferne admin_status von affiliate_links
        Schema::table('affiliate_links', function (Blueprint $table) {
            $table->dropColumn('admin_status');
        });
    }
};