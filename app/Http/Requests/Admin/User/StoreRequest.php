<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
//            'password' => 'required|string',
            'role' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Необходимо заполнить',
            'name.string' => 'Имя должно быть строкой',
            'email.required' => 'Необходимо заполнить',
            'email.string' => 'Почта должна быть строкой',
            'email.email' => 'Почта должна соответствовать формату mail@some.com',
            'email.unique' => 'Пользователь с таким email уже существует',
//            'password.required' => 'Необходимо заполнить',
//            'password.string' => 'Пароль должен быть строкой',
        ];
    }
}
