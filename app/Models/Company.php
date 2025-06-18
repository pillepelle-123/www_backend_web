<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasFactory, CrudTrait, HasApiTokens;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'companies';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'domain',
        'website_url',
        'referral_program_url',
        'logo_url',
        'description',
        'is_active',
        'industry',
        'admin_status',
    ];
    protected $hidden = [];

    protected $casts = [
        'is_active' => 'boolean',
        'admin_status' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function offers()
    {
        return $this->hasMany(Offer::class, 'company_id');
    }
    
    public function affiliateLinks()
    {
        return $this->hasMany(AffiliateLink::class, 'company_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFullReferralUrlAttribute(): ?string
    {
        if (!$this->referral_program_url) {
            return null;
        }

        // Wenn die URL bereits mit http:// oder https:// beginnt, gib sie direkt zurück
        if (str_starts_with($this->referral_program_url, 'http')) {
            return $this->referral_program_url;
        }

        // Ansonsten füge https:// hinzu
        return 'https://' . $this->referral_program_url;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
