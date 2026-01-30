<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentPageCustomization extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'logo_url',
        'brand_color',
        'headline',
        'description',
        'privacy_policy_text',
        'accept_button_text',
        'decline_button_text',
        'show_powered_by',
    ];

    protected $casts = [
        'show_powered_by' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
