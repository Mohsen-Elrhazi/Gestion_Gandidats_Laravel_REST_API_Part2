<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            "name" => "string",
            "email" => "email:dns,rfc|unique:users,email,".$this->user()->id,
            "password"=> "string|min:6",
            "image" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
            "telephone" => "nullable|string|max:20",
            "adresse" => "nullable|string",
            "date_naissance" => "nullable|date",
        ];
    }

    public function messages(){
        return [
            "name.string" => "Le nom doit être une chaîne de caractères.",
            "email.email" => "Veuillez fournir une adresse email valide.",
            "email.unique" => "Cet email est déjà utilisé.",
            "password.min" => "Le mot de passe doit contenir au moins 6 caractères.",
            "image.image" => "Le fichier doit être une image.",
            "image.mimes" => "Le fichier doit être une image de type: jpeg, png, jpg.",
            "image.max" => "Le fichier ne doit pas dépasser 2 Mo.",
            "telephone.string" => "Le numéro de téléphone doit être une chaîne de caractères.",
            "telephone.max" => "Le numéro de téléphone ne doit pas dépasser 20 caractères.",
            "adresse.string" => "L'adresse doit être une chaîne de caractères.",
            "date_naissance.date" => "La date de naissance doit être une date.",
        ];
    }
}