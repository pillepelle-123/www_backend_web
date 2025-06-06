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
        // Query Aufbau
        $query = User::query();

        // Search Parameter
        [$key, $value] = $this->getFirstSearchParam($request->query->all());
        if($key && $value) {
            $query->where($key, 'LIKE', '%' . $value . '%');
        }


        // Filter-Parameter
        $filters = $request->only(['name', 'email', 'created_at', 'updated_at']);
        foreach ($filters as $field => $value) {
            // Felder aus fremden Tabellen werden mit "tabelle-feld_name" angesprochen. Hier wird - (Bindestrich) als Trenner genommen und im folgenden durch . ersetzt, da . (Punkt) im URL-Paramter nicht vorkommen darf.
            if (str_contains($field, '-')) {
                $field = str_replace('-', '.', $field);
            };
            $query->where($field, $value);
        }

        // Sort-Parameter
        if ($request->has('sort_by')) {
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($request->input('sort_by'), $sortOrder);
        }

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
