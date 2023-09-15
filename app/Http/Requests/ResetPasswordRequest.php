<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
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
        return [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('custom-validation.email.required'),
            'email.email' => __('custom-validation.email.email'),
            'password.required' => __('custom-validation.password.required'),
            'password.min' => __('custom-validation.password.min', ['min' => 8]),
            'confirm_password.required' => __('custom-validation.confirm_password.required'),
            'confirm_password.same' => __('custom-validation.confirm_password.same'),
            'token.required' => __('custom-validation.token.required'),
            'token.string' => __('custom-validation.token.string'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), HTTP_UNPROCESSABLE_ENTITY));
    }
}
