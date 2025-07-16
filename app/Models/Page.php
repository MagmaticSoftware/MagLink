<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property string $tenant_id
 * @property string $slug
 * @property string $title
 * @property string|null $description
 * @property array|null $style
 * @property array|null $settings
 * @property bool $is_active
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $last_viewed_at
 * @property \Illuminate\Support\Carbon|null $published_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\Tenant $tenant
 *
 * @method static \Database\Factories\PageFactory factory(...$parameters)
 */
class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, SoftDeletes, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'tenant_id',
        'slug',
        'title',
        'description',
        'style',
        'settings',
        'is_active',
        'views',
        'last_viewed_at',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'style' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_viewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the page.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the page.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the tenant that owns the page.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
