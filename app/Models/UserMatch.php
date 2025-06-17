<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class UserMatch extends Model
{
    use HasFactory, CrudTrait, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'user_referrer_id',
        'user_referred_id',
        'affiliate_link_id',
        'link_clicked',
        'status',
        'success_status',
        'reason_unsuccessful_referrer',
        'reason_unsuccessful_referred'
    ];

    protected $casts = [
        'link_clicked' => 'boolean',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
    
    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_referred_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
