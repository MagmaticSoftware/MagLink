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
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->morphs('viewable'); // viewable_type, viewable_id for polymorphic relation
            $table->boolean('consent_given')->default(false);
            
            // Dati raccolti solo se consent_given = true
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('language')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable(); // OS
            $table->string('platform_version')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('device_model')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->text('referrer')->nullable();
            
            $table->timestamps();
            
            // Index su created_at per performance (morphs() già crea l'indice su viewable_type, viewable_id)
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
