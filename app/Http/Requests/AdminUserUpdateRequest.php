<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminUserUpdateRequest extends FormRequest
{
    protected $userRepository;
    protected $userId;

    public function __construct(Request $request, UserRepository $userRepository)
    {
        $uuid = $request->segment(5);
        $this->userRepository = $userRepository;
        $user = $this->userRepository->getByFieldSingleRecord('uuid', $uuid);
        $this->userId = $user->id;
    }
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->userId,
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'address' => 'required',
            'phone_number' => 'required|digits_between:9,11',
            'avatar' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => __('custom-validation.first_name.required'),
            'last_name.required' => __('custom-validation.last_name.required'),
            'email.required' => __('custom-validation.email.required'),
            'email.email' => __('custom-validation.email.email'),
            'password.required' => __('custom-validation.password.required'),
            'password.min' => __('custom-validation.password.min', ['min' => 8]),
            'confirm_password.required' => __('custom-validation.confirm_password.required'),
            'confirm_password.same' => __('custom-validation.confirm_password.same'),
            'address.required' => __('custom-validation.address.required'),
            'phone_number.required' => __('custom-validation.phone_number.required'),
            'phone_number.digits_between' => __('custom-validation.phone_number.digits_between'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), HTTP_UNPROCESSABLE_ENTITY));
    }
}
