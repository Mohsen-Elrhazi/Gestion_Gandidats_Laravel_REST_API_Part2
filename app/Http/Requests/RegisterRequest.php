<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
// use Illuminate\Support\Facades\Validator;

class RegisterRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email:dns,rfc|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'role' => 'required',
            'competences' =>'required|array'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Veuillez fournir une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'confirm_password.required' => 'Veuillez confirmer votre mot de passe.',
            'confirm_password.same' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'competences.required'=> 'competences est obligatoire'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation échouée',
            'errors' => $validator->errors(),
        ], 422));
    }
}