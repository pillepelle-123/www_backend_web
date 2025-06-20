<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Application extends Model
{
    use HasFactory, CrudTrait, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'applicant_id',
        'message',
        'status',
        'is_read_by_applicant',
        'is_read_by_owner',
        'is_archived_by_applicant',
        'is_archived_by_owner',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read_by_applicant' => 'boolean',
        'is_read_by_owner' => 'boolean',
        'is_archived_by_applicant' => 'boolean',
        'is_archived_by_owner' => 'boolean',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the offer that owns the application.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the applicant user.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * Get the offer owner user through the offer relationship.
     */
    public function offerOwner(): BelongsTo
    {
        return $this->offer()->getRelated()->offerer();
    }

    /**
     * Scope a query to only include pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include unread applications for the applicant.
     */
    public function scopeUnreadByApplicant($query)
    {
        return $query->where('is_read_by_applicant', false);
    }

    /**
     * Scope a query to only include unread applications for the offer owner.
     */
    public function scopeUnreadByOwner($query)
    {
        return $query->where('is_read_by_owner', false);
    }
}