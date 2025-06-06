<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'logoUrl' => $this->logo_url,
            'website' => $this->website,
            'referralProgramUrl' => $this->referral_program_url,
            'description' => $this->description,

        ];
    }
}




