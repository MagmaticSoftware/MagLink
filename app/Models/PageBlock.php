<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class PageBlock extends Model
{
    /** @use HasFactory<\Database\Factories\PageBlockFactory> */
    use HasFactory, BelongsToPrimaryModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'page_id',
        'type',
        'title',
        'content',
        'position',
        'size',
        'style',
        'settings',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'array',
        'size' => 'array',
        'style' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get the page that owns the block.
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the primary model that this block belongs to.
     */
    public function getRelationshipToPrimaryModel(): string
    {
        return 'page';
    }
}
