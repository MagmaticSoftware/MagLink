<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spark\Billable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'name',
        'logo',
        'email',
        'website',
        'social_links',
    ];
    
    public function billingProfile()
    {
        return $this->hasOne(BillingProfile::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function paddleEmail(): string|null
    {
        return $this->email;
    }
}
