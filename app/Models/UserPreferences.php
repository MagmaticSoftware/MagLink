<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class UserPreferences extends Model
{
    /** @use HasFactory<\Database\Factories\UserPreferencesFactory> */
    use HasFactory, SoftDeletes, BelongsToPrimaryModel;

    protected $fillable = [
        'user_id',
        'usage_type',
        'discovery_source',
        'main_goal',
        'estimated_usage',
        'team_size',
        'lang',
        'timezone',
        'terms_and_conditions',
        'terms_and_conditions_accepted_at',
        'privacy_policy',
        'privacy_policy_accepted_at',
        'newsletter',
        'newsletter_accepted_at',
    ];

    /**
     * Get relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the primary model for the tenant.
     */
    public function getRelationshipToPrimaryModel(): string
    {
        return 'user';
    }
}