<?php 
namespace App\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class OfferUserMatchRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $offerUserId = DB::table('offers')->where('id', $value)->value('user_id');
        $fromUserId = request('from_user_id');
        $toUserId = request('to_user_id');
        
        if ($offerUserId != $fromUserId && $offerUserId != $toUserId) {
            $fail('Einer der beteiligten User muss dem Angebotsersteller entsprechen.');
        }
    }
}