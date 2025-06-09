<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

// use App\Http\Resources\V1\CompanyResource;
// use App\Http\Resources\V1\CompanyCollection;

class ApiController extends Controller {


    /**
     * Gibt das erste Key-Value-Paar zurück, bei dem der Key mit "search-" beginnt.
     *
     * @param array $queryParams Die Query-Parameter (z.B. $request->query->all())
     * @return array|null Assoziatives Array mit ['key' => key, 'value' => value] oder null, wenn nichts gefunden wurde
     */
    // function getFirstSearchParam(array $queryParams): ?array
    // {
    //     foreach ($queryParams as $key => $value) {
    //         if (str_starts_with($key, 'search-')) {
    //             $searchArray = explode('-', $key, 2);
    //             if (count($searchArray) == 2) {
    //                 return [$searchArray[1], $value];
    //                 // return [$searchArray[1] => $value];
    //             }
    //             // return [
    //             //     'key' => $key,
    //             //     'value' => $value
    //             // ];
    //         }
    //     }

    //     return null;
    // }
}
