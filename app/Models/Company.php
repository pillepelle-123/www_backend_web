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
        'logo_url',
        'website',
        'referral_program_url',
        'description'
    ];
    protected $hidden = [];

    // protected $casts = [
    //     'name' => 'string',
    //     'reward_total_cents' => 'integer',
    //     'reward_offerer_percent' => 'decimal:2',
    //     'status' => 'string',
    //     'created_at' => 'datetime',
    //     'updated_at' => 'datetime',
    // ];
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function offers()
    {
        return $this->hasMany(Offer::class, 'company_id');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
