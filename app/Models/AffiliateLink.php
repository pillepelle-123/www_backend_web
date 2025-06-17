<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliateLink extends Model
{
    use HasFactory, CrudTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'url',
        'parameters',
        'click_count',
        'admin_status',
    ];

    /**
     * Get the company that owns the affiliate link.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user matches for the affiliate link.
     */
    public function userMatches(): HasMany
    {
        return $this->hasMany(UserMatch::class);
    }

    /**
     * Increment the click count.
     */
    public function incrementClickCount(): void
    {
        $this->increment('click_count');
    }
}