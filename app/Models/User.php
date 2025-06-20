<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use CrudTrait, HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'average_rating',
        'admin_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'average_rating' => 'float',
            'admin_status' => 'string',
        ];
    }

    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'from_user_id');
    }

    // User kann viele Ratings erhalten haben (als "to_user")
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'offerer_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
