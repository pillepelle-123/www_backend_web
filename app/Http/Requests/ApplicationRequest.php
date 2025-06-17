<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationRequest extends FormRequest
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
            'offer_id' => 'required|exists:offers,id',
            'applicant_id' => 'required|exists:users,id',
            'offer_owner_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
            'status' => [
                'required',
                Rule::in(['pending', 'approved', 'rejected']),
            ],
            'is_read_by_applicant' => 'boolean',
            'is_read_by_owner' => 'boolean',
            'responded_at' => 'nullable|date',
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
            'offer_id' => 'Offer',
            'applicant_id' => 'Applicant',
            'offer_owner_id' => 'Offer Owner',
            'message' => 'Message',
            'status' => 'Status',
            'is_read_by_applicant' => 'Read by Applicant',
            'is_read_by_owner' => 'Read by Owner',
            'responded_at' => 'Responded At',
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
            'offer_id.required' => 'Please select an offer.',
            'offer_id.exists' => 'The selected offer does not exist.',
            'applicant_id.required' => 'Please select an applicant.',
            'applicant_id.exists' => 'The selected applicant does not exist.',
            'offer_owner_id.required' => 'Please select an offer owner.',
            'offer_owner_id.exists' => 'The selected offer owner does not exist.',
            'message.max' => 'The message may not be greater than :max characters.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid.',
            'responded_at.date' => 'The responded at must be a valid date.',
        ];
    }
}