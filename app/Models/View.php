<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class View extends Model
{
    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'consent_given',
        'ip_address',
        'user_agent',
        'country',
        'country_code',
        'city',
        'language',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'device_type',
        'device_model',
        'screen_resolution',
        'referrer',
        'privacy_version',
        'privacy_text',
        'consent_at',
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'consent_at' => 'datetime',
    ];

    /**
     * Get the parent viewable model (Link or QrCode).
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create a view without personal data (no consent).
     */
    public static function createWithoutConsent($viewable): self
    {
        return self::create([
            'viewable_type' => get_class($viewable),
            'viewable_id' => $viewable->id,
            'consent_given' => false,
        ]);
    }

    /**
     * Create a view with full tracking data (consent given).
     */
    public static function createWithConsent($viewable, array $data, string $privacyVersion, string $privacyText): self
    {
        return self::create([
            'viewable_type' => get_class($viewable),
            'viewable_id' => $viewable->id,
            'consent_given' => true,
            'consent_at' => now(),
            'privacy_version' => $privacyVersion,
            'privacy_text' => $privacyText,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'country' => $data['country'] ?? null,
            'country_code' => $data['country_code'] ?? null,
            'city' => $data['city'] ?? null,
            'language' => $data['language'] ?? null,
            'browser' => $data['browser'] ?? null,
            'browser_version' => $data['browser_version'] ?? null,
            'platform' => $data['platform'] ?? null,
            'platform_version' => $data['platform_version'] ?? null,
            'device_type' => $data['device_type'] ?? null,
            'device_model' => $data['device_model'] ?? null,
            'screen_resolution' => $data['screen_resolution'] ?? null,
            'referrer' => $data['referrer'] ?? null,
        ]);
    }
}
