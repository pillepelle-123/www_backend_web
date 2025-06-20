<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'application_id' => 'required|exists:applications,id',
            'affiliate_link_id' => 'nullable|exists:affiliate_links,id',
            'link_clicked' => 'boolean',
            'status' => 'required|in:opened,closed',
            'success_status' => 'required|in:pending,successful,unsuccessful',
            'reason_unsuccessful_referrer' => 'nullable|string',
            'reason_unsuccessful_referred' => 'nullable|string',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}