<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;
// use App\Http\Resources\V1\UserCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\AllowedInclude;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(/*Request $request*/)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFields('name') // http://localhost:8000/api/v1/users?sort=-name&fields[users]=name
            ->allowedFilters(['name', 'email',
                AllowedFilter::operator('created_at', FilterOperator::DYNAMIC),
                AllowedFilter::operator('updated_at', FilterOperator::DYNAMIC),
            ])
            ->allowedIncludes(['offers', 'givenRatings', 'receivedRatings',
                AllowedInclude::count('offersCount')
            ])
            ->allowedSorts('-name', 'email', 'created_at', 'updated_at')
            ->paginate(15);

        return $users;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
