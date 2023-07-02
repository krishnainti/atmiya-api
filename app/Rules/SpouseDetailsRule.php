<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use Illuminate\Http\Request;

class SpouseDetailsRule implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $requestData = request()->all();

        $membershipCategoryIds = array(2, 3,4);

        if ($requestData["marital_status"] === 'married' && in_array($requestData["membership_category"], $membershipCategoryIds) && !$value) {
            $fail('The :attribute required.');
        }
    }
}
