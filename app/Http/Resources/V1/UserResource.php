<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // alles in CamelCase -> API naming convention !!!!!!!!!!!!!!!
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
