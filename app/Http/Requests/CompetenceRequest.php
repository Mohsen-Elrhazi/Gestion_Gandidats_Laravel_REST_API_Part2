<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompetenceRequest extends FormRequest
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
            "name" => "required|string",
        ];
    }

    public function messages(){
        return [
            "name.required" => "veuillez entrer le nom de competence",
            "name.string" => "le nom doit une mot",
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