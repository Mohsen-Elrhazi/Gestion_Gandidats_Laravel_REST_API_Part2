<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OffreRequest extends FormRequest
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
            "title" => "required|string",
            "description" => "required|string",
            'location' =>'required|string',
            'contract_type' =>'required|string',
        ];
    }

    public function messages(){
        return [
            "title.required" => "veuillez entrer le nom de competence",
            "name.string" => "le nom doit une mot",
            'description.required' => 'La description de l\'offre est obligatoire.',
            'description.string' => 'La description de l\'offre doit être une chaîne de caractères.',
            'contract_type.required' => 'Le contract est obligatoire.',
            'location.required' => 'La localisation de l\'offre est obligatoire.',
            'location.string' => 'La localisation de l\'offre doit être une chaîne de caractères.',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            "status" => "error",
            "message" => "Validation échouée",
            "errors" => $validator->errors()
        ], 422));
    }
}