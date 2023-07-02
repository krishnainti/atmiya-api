<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\SpouseDetailsRule;

class RegisterRequest extends FormRequest
{

    public $validator = null;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        Log::info('New registration request: '.json_encode($this->all()));
        return [
             //Personal Details
             'reference_by' => 'bail|required|string|max:50',
             'reference_phone' => 'bail|required|regex:/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/|string',
             'first_name' => 'bail|required|string|max:50',
             'last_name' => 'bail|required|string|max:50',
             'email' => 'bail|required|email|max:100|unique:users,email',
             'phone' => 'bail|required|regex:/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/|unique:profiles,phone',
             'marital_status' => 'bail|required|in:single,married',
             'gender' => 'bail|required|in:male,female',

             'password' => 'required|string|min:6|max:50',
             'confirm_password' => 'required|same:password',

             //spouse details
             'spouse_first_name' => [new SpouseDetailsRule],
             'spouse_last_name' => [new SpouseDetailsRule],
             'spouse_email' => [new SpouseDetailsRule],
             'spouse_phone' => [new SpouseDetailsRule],
             'family_members' => 'bail|array|nullable',

             //Address
             'address_line_1' => 'bail|required|string|max:200',
             'address_line_2' => 'bail|sometimes|nullable|string|max:200',
             'city' => 'bail|required|string|max:100',
             'state' => 'bail|required|integer|min:1|exists:chapter_states,id',
             'metro_area' => 'bail|sometimes|nullable|integer|min:1|exists:metro_areas,id',
             'zip_code' => 'bail|required|string|max:25',
             'country' => 'bail|required|string|max:25',

             //Membership Category
             'membership_category' => 'bail|required|integer|exists:membership_categories,id',

             'payment_mode' => 'bail|required|string|max:25',
        ];
    }

}
