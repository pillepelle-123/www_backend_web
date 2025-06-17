<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'admin_status' => [
                'required',
                Rule::in(['active', 'inactive', 'review', 'archived'])
            ],
        ];
        
        // Only require password on create
        if (!$this->id) {
            $rules['password'] = 'required|min:8|confirmed';
        } else if ($this->filled('password')) {
            $rules['password'] = 'min:8|confirmed';
        }
        
        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Name',
            'email' => 'E-Mail',
            'password' => 'Passwort',
            'average_rating' => 'Durchschnittliche Bewertung',
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
            'name.required' => 'Bitte geben Sie einen Namen an.',
            'name.max' => 'Der Name darf maximal :max Zeichen lang sein.',
            
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse an.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.',
            'email.max' => 'Die E-Mail-Adresse darf maximal :max Zeichen lang sein.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            
            'password.required' => 'Bitte geben Sie ein Passwort an.',
            'password.min' => 'Das Passwort muss mindestens :min Zeichen lang sein.',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
            
            'average_rating.numeric' => 'Die Bewertung muss eine Zahl sein.',
            'average_rating.min' => 'Die Bewertung muss mindestens :min sein.',
            'average_rating.max' => 'Die Bewertung darf maximal :max sein.',
            
            'admin_status.required' => 'Bitte einen Admin-Status auswählen.',
            'admin_status.in' => 'Ungültiger Admin-Status ausgewählt.',
        ];
    }
}