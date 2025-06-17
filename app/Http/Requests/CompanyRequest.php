<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $id = $this->id ?? 'NULL';
        return [
            'name' => 'required|min:2|max:100|unique:companies,name,'.$id,
            'domain' => 'nullable|string|max:255',
            'logo_url' => 'nullable|url|max:255',
            'referral_program_url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'industry' => 'nullable|string|max:100',
            'admin_status' => [
                'required',
                Rule::in(['pending', 'active', 'inactive', 'review', 'archived'])
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
            'id' => 'Unternehmens-ID',
            'name' => 'Firmenname',
            'domain' => 'Domain',
            'logo_url' => 'Logo URL',
            'referral_program_url' => 'Empfehlungsprogramm URL',
            'description' => 'Beschreibung',
            'is_active' => 'Aktiv',
            'industry' => 'Branche',
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

            'name.required' => 'Bitte geben Sie einen Firmennamen an.',
            'name.min' => 'Der Firmenname muss mindestens :min Zeichen lang sein.',
            'name.max' => 'Der Firmenname darf maximal :max Zeichen lang sein.',
            'name.unique' => 'Dieser Firmenname existiert bereits.',

            'domain.max' => 'Die Domain darf maximal :max Zeichen lang sein.',

            'logo_url.url' => 'Bitte geben Sie eine gültige URL für das Logo an.',
            'logo_url.max' => 'Die Logo-URL darf maximal :max Zeichen lang sein.',

            'referral_program_url.url' => 'Bitte geben Sie eine gültige URL für das Empfehlungsprogramm an.',
            'referral_program_url.max' => 'Die Empfehlungsprogramm-URL darf maximal :max Zeichen lang sein.',

            'description.max' => 'Die Beschreibung darf maximal :max Zeichen lang sein.',
            
            'industry.max' => 'Die Branche darf maximal :max Zeichen lang sein.',
            
            'admin_status.required' => 'Bitte einen Admin-Status auswählen.',
            'admin_status.in' => 'Ungültiger Admin-Status ausgewählt.',
        ];
    }
}