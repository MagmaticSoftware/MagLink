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
        Schema::table('links', function (Blueprint $table) {
            $table->boolean('require_consent')->default(false)->after('url');
        });
        
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->boolean('require_consent')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('require_consent');
        });
        
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn('require_consent');
        });
    }
};
