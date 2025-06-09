<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\RatingDirection;

class Rating extends Model
{
    use HasFactory, CrudTrait, HasApiTokens;


    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'ratings';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'offer_id',
        'from_user_id',
        'to_user_id',
        'score',
        'comment',
    ];
    protected $casts = [
        'direction' => RatingDirection::class, // Enum siehe unten
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function userMatch(): BelongsTo
    {
        return $this->belongsTo(UserMatch::class);
    }
    // public function offer(): BelongsTo
    // {
    //     return $this->belongsTo(Offer::class);
    // }

    // Ein Rating gehört zum "Bewerter" (from_user)
    // public function fromUser(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'from_user_id');
    // }

    // // Ein Rating gehört zum "Bewerteten" (to_user)
    // public function toUser(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'to_user_id');
    // }

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

    public function getScoreInStars(): string
    {
        return [
            '1' => '★',
            '2' => '★★',
            '3' => '★★★',
            '4' => '★★★★',
            '5' => '★★★★★'
        ][$this->score] ?? $this->score;
    }


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
