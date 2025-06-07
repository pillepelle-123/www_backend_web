<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserCollection;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $users = User::all(); // Oder paginieren
        $userResource = UserResource::collection($users);

        dd($userResource);

        $fields = [
            'users-name', 'users-email', 'users-email_verified_at', 'users-created_at', 'users-updated_at'
        ];

        $query = User::query()
            ->join('offers', 'ratings.offer_id', '=', 'offers.id')
            // ->join('users as users_from', 'ratings.from_user_id', '=', 'users_from.id')
            // ->join('users as users_to', 'ratings.to_user_id', '=', 'users_to.id')
            // ->join('companies', 'offers.company_id', '=', 'companies.id')
            ->select(str_replace('-', '.', $fields))
        ;

        // nur Parameter mit filter[], search und sort_by erlaubt
        $request->only(['filter', 'search', 'sort_by']);

        // only Request Filter Parameter mit Feldern aus $fields erlaubt
        $filter_fields = $request->query('filter');
        $allowed_fields = array_intersect($fields, array_keys($filter_fields));

        foreach ($allowed_fields as $allowed_field) {
            foreach ($request->input('filter.' . $allowed_field, []) as $op => $value) {

            if (str_contains($allowed_field, '-')) {
                $allowed_field = str_replace('-', '.', $allowed_field);
            };

                // dump('allowed_filter: ' . $allowed_field . ', op: ' . $op . ', val: ' . $value);
                match ($op) {
                    'gt'  => $query->where($allowed_field, '>', $value),
                    'gte' => $query->where($allowed_field, '>=', $value),
                    'lt'  => $query->where($allowed_field, '<', $value),
                    'lte' => $query->where($allowed_field, '<=', $value),
                    default => $query->where($allowed_field, '=', $value),
                };
            }
        }



        // $fields = [
        //     'name', 'email', 'created_at', 'updated_at'
        // ];

        // // Query Aufbau
        // $query = User::query()
        //     ->select($fields);

        // $filters = $request->only($fields);
        // // foreach ($filters as $field => $value) {
        // //     if ($request->has('filter.' . $field)) {
        // //         $query->whereIn('category_id', $request->input('filter.' . $field));
        // //     }
        // // }

        // foreach ($request->input('filter.', []) as $op => $value) {
        //     foreach ($filters as $field => $value) {
        //         match ($op) {
        //             'gt'  => $query->where($field, '>', $value),
        //             'gte' => $query->where($field, '>=', $value),
        //             'lt'  => $query->where($field, '<', $value),
        //             'lte' => $query->where($field, '<=', $value),
        //             default => null
        //         };
        //     }
        // }

        // // Search Parameter
        // [$key, $value] = $this->getFirstSearchParam($request->query->all());
        // if($key && $value) {
        //     $query->where($key, 'LIKE', '%' . $value . '%');
        // }


        // // Filter-Parameter
        // $filters = $request->only(['name', 'email', 'created_at', 'updated_at']);
        // foreach ($filters as $field => $value) {
        //     // Felder aus fremden Tabellen werden mit "tabelle-feld_name" angesprochen. Hier wird - (Bindestrich) als Trenner genommen und im folgenden durch . ersetzt, da . (Punkt) im URL-Paramter nicht vorkommen darf.
        //     if (str_contains($field, '-')) {
        //         $field = str_replace('-', '.', $field);
        //     };
        //     $query->where($field, $value);
        // }

        // // Sort-Parameter
        // if ($request->has('sort_by')) {
        //     $sortOrder = $request->input('sort_order', 'asc');
        //     $query->orderBy($request->input('sort_by'), $sortOrder);
        // }

        $perPage = $request->input('per_page', 10);
        $paginatedItems = $query->paginate($perPage);

        return response()->json($paginatedItems);
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
