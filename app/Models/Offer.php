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
        'offered_by_type',
        'user_id',
        'company_id',
        'offer_title',
        'offer_description',
        'reward_total_cents',
        'reward_offerer_percent',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'offered_by_type' => 'string',
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
        'status' => 'active',
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
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company associated with the offer
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all ratings for this offer
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get human-readable offered_by_type
     */
    public function getOfferedByTypeLabelAttribute(): string
    {
        return [
            'referrer' => 'Werbender',
            'referred' => 'Interessent',
        ][$this->offered_by_type] ?? $this->offered_by_type;
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
            'active' => 'Aktiv',
            'inactive' => 'Inaktiv',
            'matched' => 'Zugewiesen',
            'closed' => 'Abgeschlossen'
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
     * Scope for active offers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for offers by referrer
     */
    public function scopeByReferrer($query)
    {
        return $query->where('offered_by_type', 'referrer');
    }



    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Automatically set the user_id if not provided
            if (empty($model->user_id) && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }
}
