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
        Schema::create('consent_page_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tenant_id');
            
            // Customization fields (Premium feature)
            $table->string('logo_url')->nullable();
            $table->string('brand_color')->default('#3b82f6');
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->text('privacy_policy_text')->nullable(); // Override default
            $table->string('accept_button_text')->default('Accetta e continua');
            $table->string('decline_button_text')->default('Rifiuta e continua');
            $table->boolean('show_powered_by')->default(true);
            
            $table->timestamps();
            
            $table->index(['user_id', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_page_customizations');
    }
};
