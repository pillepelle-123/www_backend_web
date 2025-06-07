<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RatingController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fields = [
            'ratings.id', 'ratings.score', 'ratings.comment', 'ratings.created_at', 'ratings.updated_at', 'offers.id', 'offers.user_id', 'offers.offer_title', 'offers.reward_total_cents', 'offers.reward_offerer_percent', 'offers.status', 'users_from.name', 'users_to.name'
        ];

        $fieldsWithAliases = array_map(
            function ($field) {
                // Sonderfall: Entferne "ratings." im Alias, falls vorhanden
                $alias = str_replace('.', '-', $field);
                if (str_starts_with($field, 'ratings.')) {
                    $alias = str_replace('ratings-', '', $alias);
                }
                return $field . ' as ' . $alias;
            },
            $fields
        );

        $query = Rating::query()
            ->join('offers', 'ratings.offer_id', '=', 'offers.id')
            ->join('users as users_from', 'ratings.from_user_id', '=', 'users_from.id')
            ->join('users as users_to', 'ratings.to_user_id', '=', 'users_to.id')
            ->select($fieldsWithAliases)
        ;

        // nur Parameter mit filter[], search und sort_by erlaubt
        $request->only(['filter', 'search', 'sort_by']);
        // only Request Filter Parameter mit Feldern aus $fields erlaubt
        $query_filter_fields = $request->query('filter');

        $allowed_fields = [];
        foreach($query_filter_fields as $key => $value) {
            if(in_array($key, str_replace('.', '-', $fields))) {
                $allowed_fields[$key] = $value;
            }
        }

        foreach ($allowed_fields as $key => $value) {
            foreach ($request->input('filter.' . $key, []) as $op => $value) {

            $key = str_replace('-', '.', $key);

                match ($op) {
                    'gt'  => $query->where($key, '>', $value),
                    'gte' => $query->where($key, '>=', $value),
                    'lt'  => $query->where($key, '<', $value),
                    'lte' => $query->where($key, '<=', $value),
                    default => $query->where($key, '=', $value),
                };
            }
        }
        // die($query->toSql());


        // $fields = [
        //     'ratings.*', 'offers.id as offers-id', 'offers.user_id as offers-user_id', 'offers.offer_title as offers-offer_title', 'offers.reward_total_cents as offers-reward_total_cents', 'offers.reward_offerer_percent as offers-reward_offerer_percent', 'offers.status as offers-status', 'users_from.name as users_from-name', 'users_to.name as users_to-name'
        // ];



        // $fields = ['filter[offers-offer_title][]'];

        // nur Parameter mit filter[] erlaubt
        // $filters = $request->only(['filter']); //$fields);
        // // foreach ($filters as $field => $value) {
        // //     if ($request->has('filter.' . $field)) {
        // //         $query->whereIn('category_id', $request->input('filter.' . $field));
        // //     }
        // // }

        // $filters = $request->query('filter');

        // ;

        // only Request Filter Parameter mit Feldern aus $fields erlaubt
        // dd(array_intersect($fields, array_keys($filters)));

        // if(array_keys($filters) == $fields) {

        // }


        // Search Parameter
        $columns = array_keys($query->first()->toArray());
        [$key, $value] = $this->getFirstSearchParam($request->query->all());
        if($key && $value) {
            if(array_search($key, $columns))
            if (str_contains($key, '-')) {
                $key = str_replace('-', '.', $key);
            };
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        // Filter-Parameter
        // $filters = $request->only($fields);
        // foreach ($filters as $field => $value) {
        //     // Felder aus fremden Tabellen werden mit "tabelle-feld_name" angesprochen. Hier wird - (Bindestrich) als Trenner genommen und im folgenden durch . ersetzt, da . (Punkt) im URL-Paramter nicht vorkommen darf.
        //     if (str_contains($field, '-')) {
        //         $field = str_replace('-', '.', $field);
        //     };
        //     $query->where($field, '=', $value);
        // }

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
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        //
    }
}
