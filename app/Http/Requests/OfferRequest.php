<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
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
            'id' => 'sometimes|integer|exists:offers,id',
            'offerer_type' => [
                'required',
                Rule::in(['referrer', 'referred'])
            ],
            'offerer_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'reward_total_cents' => 'required|integer|min:0',
            'reward_offerer_percent' => 'required|numeric|between:0.01,1.00',
            'status' => [
                'required',
                Rule::in(['draft', 'live', 'hidden', 'matched', 'deleted'])
            ],
            'admin_status' => [
                'required',
                Rule::in(['active', 'inactive', 'review', 'archived'])
            ],
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
            'id' => 'Angebot ID',
            'offerer_id' => 'Benutzer ID',
            'offerer_type' => 'Angebot von',
            'company_id' => 'Unternehmen',
            'title' => 'Angebotstitel',
            'description' => 'Angebotsbeschreibung',
            'reward_total_cents' => 'Belohnungsbetrag',
            'reward_offerer_percent' => 'Aufteilungsprozentsatz',
            'status' => 'Status',
            'admin_status' => 'Admin Status',
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
            'id.required' => 'Bitte geben Sie eine ID an',
            'id.exists' => 'Die angegebene ID existiert nicht.',

            'offerer_id.required' => 'Bitte eine Benutzer-ID angeben.',
            'offerer_id.exists' => 'Die angegebene Benutzer-ID existiert nicht.',

            'offerer_type.required' => 'Bitte angeben, von wem das Angebot kommt.',
            'offerer_type.in' => 'Ungültige Auswahl. Muss "Referrer" oder "Referred" sein.',

            'company_id.required' => 'Bitte ein Unternehmen auswählen.',
            'company_id.exists' => 'Das ausgewählte Unternehmen existiert nicht.',

            'title.required' => 'Ein Angebotstitel ist erforderlich.',
            'title.max' => 'Der Titel darf maximal 255 Zeichen lang sein.',

            'description.required' => 'Eine Beschreibung ist erforderlich.',
            'description.max' => 'Die Beschreibung darf maximal 5000 Zeichen lang sein.',

            'reward_total_cents.required' => 'Bitte den Belohnungsbetrag angeben.',
            'reward_total_cents.integer' => 'Der Betrag muss in ganzen Cent angegeben werden.',
            'reward_total_cents.min' => 'Der Betrag darf nicht negativ sein.',

            'reward_offerer_percent.required' => 'Bitte den Aufteilungsprozentsatz angeben.',
            'reward_offerer_percent.numeric' => 'Der Prozentsatz muss eine Zahl sein.',
            'reward_offerer_percent.between' => 'Der Prozentsatz muss zwischen 1% und 100% liegen.',

            'status.required' => 'Bitte einen Status auswählen.',
            'status.in' => 'Ungültiger Status ausgewählt.',

            'admin_status.required' => 'Bitte einen Admin-Status auswählen.',
            'admin_status.in' => 'Ungültiger Admin-Status ausgewählt.',
        ];
    }
}
