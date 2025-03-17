<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email"=> "required|email:dns,rfc",
            "password"=> "required",
        ];
    }

    public function messages(){
        return [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Veuillez fournir une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ];
    }

    protected function failedValidation(Validator $validator){
        return new HttpResponseException(response()->json([
            "status" => "error",
            "message" => "Validation échouée",
            "errors" => $validator->errors()
        ], 422));
    }
}