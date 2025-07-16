<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class BillingProfile extends Model
{
    /** @use HasFactory<\Database\Factories\BillingProfileFactory> */
    use HasFactory, BelongsToPrimaryModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'company_name',
        'phone',
        'address',
        'city',
        'province',
        'region',
        'country',
        'zip',
        'vat_id',
        'fiscal_code',
        'sdi_code',
        'pec_email',
        'currency',
        'payment_method',
        'notes',
    ];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'company';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
