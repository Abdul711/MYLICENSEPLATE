<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicensePlateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
       public function stopOnFirstFailure(): bool
    {
        return true; // Stop validation after the first failure
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plate_number' => 'required|string|max:10|unique:licenseplates,plate_number',
         
            'region' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'status' => 'in:Available,Pending,Sold',
            'price' => 'required|numeric|between:1000,5000', // Example price range
        ];
    }
    public function messages(): array
    {
        return [
            'plate_number.required' => 'The plate number is required.',
            'plate_number.unique' => 'The plate number already taken.',
            'region.string' => 'The region must be a string.',
            'city.string' => 'The city must be a string.',
            'status.in' => 'The status must be one of the following: Available, Pending, Sold.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.between' => 'The price must be between 1000 and 5000.',
        ];
    }
    
    
}
