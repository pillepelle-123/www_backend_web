<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'logo_url' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
            'referral_program_url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:2000'
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
            'logo_url' => 'Logo URL',
            'website' => 'Webseite',
            'referral_program_url' => 'Empfehlungsprogramm URL',
            'description' => 'Beschreibung'
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

            'logo_url.url' => 'Bitte geben Sie eine gültige URL für das Logo an.',
            'logo_url.max' => 'Die Logo-URL darf maximal :max Zeichen lang sein.',

            'website.url' => 'Bitte geben Sie eine gültige Webseiten-URL an.',
            'website.max' => 'Die Webseiten-URL darf maximal :max Zeichen lang sein.',

            'referral_program_url.url' => 'Bitte geben Sie eine gültige URL für das Empfehlungsprogramm an.',
            'referral_program_url.max' => 'Die Empfehlungsprogramm-URL darf maximal :max Zeichen lang sein.',

            'description.max' => 'Die Beschreibung darf maximal :max Zeichen lang sein.'
        ];
    }
}
