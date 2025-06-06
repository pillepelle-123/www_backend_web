<?php

namespace App\Http\Requests;

use App\Http\Rules\OfferUserMatchRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RatingRequest extends FormRequest
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
            'offer_id' => [
                'required',
                'exists:offers,id', 
                new OfferUserMatchRule,
                // 'exists:offers,id', // Optional, falls die Bewertung an ein Angebot gebunden ist
            ],
            //'required|exists:offers,id',
            'from_user_id' => [
                'required',
                'exists:users,id',
                'different:to_user_id',
                //Rule::in([backpack_auth()->id()]) // Nur der eingeloggte User darf als from_user_id verwendet werden
            ],
            'to_user_id' => [
                'required',
                'exists:users,id',
                'different:from_user_id', // Der zu bewertende User darf nicht der bewertende User sein
                //Rule::notIn([backpack_auth()->id()]) // Der bewertete User darf nicht der eingeloggte User sein
            ],
            'score' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000'
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
            'id' => 'Rating ID',
            'offer_id' => 'Angebot',
            'from_user_id' => 'Bewertender',
            'to_user_id' => 'Bewerteter',
            'score' => 'Bewertung',
            'comment' => 'Kommentar'
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
            
            'offer_id.required' => 'Bitte wählen Sie ein Angebot aus.',
            'offer_id.exists' => 'Das ausgewählte Angebot existiert nicht.',
            
            'from_user_id.required' => 'Der bewertende Benutzer muss angegeben werden.',
            'from_user_id.exists' => 'Der bewertende Benutzer existiert nicht.',
            'from_user_id.in' => 'Sie können nur Bewertungen als eingeloggter Benutzer abgeben.',
            
            'to_user_id.required' => 'Der zu bewertende Benutzer muss angegeben werden.',
            'to_user_id.exists' => 'Der zu bewertende Benutzer existiert nicht.',
            'to_user_id.not_in' => 'Sie können sich nicht selbst bewerten.',
            
            'score.required' => 'Bitte geben Sie eine Bewertung ab.',
            'score.integer' => 'Die Bewertung muss eine ganze Zahl sein.',
            'score.between' => 'Die Bewertung muss zwischen 1 und 5 liegen.',
            
            'comment.max' => 'Der Kommentar darf maximal 1000 Zeichen lang sein.'
        ];
    }
}
