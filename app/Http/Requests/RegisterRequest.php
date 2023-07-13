<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

use App\Rules\SpouseDetailsRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        $rules =  [
             //Personal Details
             'reference_by' => 'bail|required|string|max:50',
             'reference_phone' => 'bail|required|string',
             'first_name' => 'bail|required|string|max:50',
             'last_name' => 'bail|required|string|max:50',

             'marital_status' => 'bail|required|in:single,married',
             'gender' => 'bail|required|in:male,female',

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

        ];

        if (Request::isMethod('patch')) {

            Log::info('profile update: '.json_encode($this->all()));

            $rules['id'] = 'bail|required|min:1|exists:users,id';
            $rules['email'] = 'bail|required|email|max:100|unique:users,email,'.$this->get('id');
            $rules['phone'] = 'bail|required|string';

            if (!empty($this->get('password'))) {
                $rules['password'] = 'required|string|min:6|max:50';
                $rules['confirm_password'] = 'required|same:password';
            }

        } else {

            $rules['id'] = "nullable";

            if ($this->id) {
                Log::info('Update Exiting user registration request: '.json_encode($this->all()));
                $rules['email'] = ['required', 'email', Rule::unique('users')->ignore($this->id)];
            } else {
                Log::info('New registration request: '.json_encode($this->all()));
                $rules['email'] = 'bail|required|email|max:100|unique:users,email';
            }


            $rules['phone'] = 'bail|required|string';
            $rules['payment_mode'] = 'bail|required|in:paypal,zelle,card|max:25';
            $rules['membership_category'] = 'bail|required|integer|exists:membership_categories,id';

            $rules['password'] = 'required|string|min:6|max:50';
            $rules['confirm_password'] = 'required|same:password';
        }

        return $rules;
    }

}
