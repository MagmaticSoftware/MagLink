<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SearchableInTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Link extends Model
{
    /** @use HasFactory<\Database\Factories\LinkFactory> */
    use HasFactory, SoftDeletes, BelongsToTenant, SearchableInTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'tenant_id',
        'slug',
        'url',
        'title',
        'description',
        'is_active',
        'type',
        'require_consent',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['short_url'];

    /**
     * Get the shortened URL for the link.
     */
    public function getShortUrlAttribute(): string
    {
        return config('app.short_url') . '/' . $this->slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    protected function searchableFields(): array
    {
        return ['title', 'slug', 'description', 'url'];
    }
}
