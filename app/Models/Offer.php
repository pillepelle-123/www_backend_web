<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class Offer extends Model
{
    use HasFactory, CrudTrait, HasApiTokens;

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offerer_type',
        'offerer_id',
        'company_id',
        'title',
        'description',
        'reward_total_cents',
        'reward_offerer_percent',
        'status',
        'admin_status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'offerer_type' => 'string',
        'reward_total_cents' => 'integer',
        'reward_offerer_percent' => 'decimal:2',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default attribute values
     *
     * @var array
     */
    protected $attributes = [
        'reward_offerer_percent' => 0.50,
        'status' => 'draft',
        'admin_status' => 'active',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that created the offer
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'offerer_id');
    }
    
    public function offerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'offerer_id');
    }

    /**
     * Get the company associated with the offer
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get all ratings for this offer
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'offer_id');
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get human-readable offerer_type
     */
    public function getOffererTypeLabelAttribute(): string
    {
        return [
            'referrer' => 'Werbender',
            'referred' => 'Interessent',
        ][$this->offerer_type] ?? $this->offerer_type;
    }
    /**
     * Accessor for reward in euros
     */
    public function getRewardTotalinEuro(): float
    {
        return $this->reward_total_cents / 100;
    }

    /**
     * Accessor for the percentage of the reward the offerer keeps for himself
     */
    public function getRewardOffererInPercent(): float
    {
        return $this->reward_offerer_percent * 100;
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return [
            'draft' => 'Entwurf',
            'live' => 'Live',
            'hidden' => 'Versteckt',
            'matched' => 'Zugewiesen',
            'deleted' => 'Gelöscht'
        ][$this->status] ?? $this->status;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    /**
     * Mutator for reward in euros
     */
    public function setRewardTotalinEuro(float $value): void
    {
        $this->attributes['reward_total_cents'] = $value * 100;
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    /**
     * Scope for live offers
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }
    
    /**
     * Scope for active offers (admin status)
     */
    public function scopeActive($query)
    {
        return $query->where('admin_status', 'active');
    }

    /**
     * Scope for offers by referrer
     */
    public function scopeByReferrer($query)
    {
        return $query->where('offerer_type', 'referrer');
    }



    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Automatically set the offerer_id if not provided
            if (empty($model->offerer_id) && Auth::check()) {
                $model->offerer_id = Auth::id();
            }
        });
    }
}
