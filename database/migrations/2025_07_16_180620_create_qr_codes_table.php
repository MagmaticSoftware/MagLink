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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('tenant_id')->nullable();
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('type')->default('static'); //static | dynamic
            $table->string('format')->default('url'); //url | text | email | phone | sms | vcard | ecc.
            $table->json('payload')->nullable();
            $table->nullableMorphs('target');
            $table->json('options')->nullable();
            $table->unsignedBigInteger('scans')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
