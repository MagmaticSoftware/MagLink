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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('usage_type')->nullable(); //company, personal, agency, ecc.
            $table->string('discovery_source')->nullable(); //google, social, word of mouth, ecc.
            $table->string('main_goal')->nullable(); //monitorare campagne, gestire QR, ecc.
            $table->string('estimated_usage')->nullable(); //low, medium, high
            $table->string('team_size')->nullable(); //solo, 1-5, 6-10, 11-20, 21+
            $table->string('lang')->nullable(); //en, it, fr, ecc.
            $table->string('timezone')->nullable(); //Europe/Rome, America/New_York, ecc.
            $table->boolean('terms_and_conditions')->default(false);
            $table->timestamp('terms_and_conditions_accepted_at')->nullable();
            $table->boolean('privacy_policy')->default(false);
            $table->timestamp('privacy_policy_accepted_at')->nullable();
            $table->boolean('newsletter')->default(false);
            $table->timestamp('newsletter_accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
