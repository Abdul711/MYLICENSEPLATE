<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8',
            "package_id"=>"nullable|exists:packages,id",
            'mobile' => ['required','unique:users,mobile', 'regex:/^(0|\+92)[0-9]{10}$/'],
        ];
    }
       public function stopOnFirstFailure(): bool
    {
        return true; // Stop validation after the first failure
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The name  is required.',
            'email.required' => 'The email  is required.',
            'email.email' => 'The email must be a valid email address.',
            'password.required' => 'The password  is required.',
            'mobile.required' => 'The mobile number is required.',
            'mobile.regex' => 'The mobile number must be in the format 0XXXXXXXXXX or +92XXXXXXXXXX.',
        ];
    }
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        // Example: format mobile number after validation
        if (isset($data['mobile']) && str_starts_with($data['mobile'], '0')) {
            $data['mobile'] = '+92' . substr($data['mobile'], 1);
        }
        if (isset($data['name'])) {
            $data['name'] = ucwords($data['name']) ; 
        }
           // ✅ Extract email domain
     if (!empty($data['email']) && str_contains($data['email'], '@')) {
        $domain = explode('@', $data['email'])[1] ?? '';

        // Remove .com if it exists
        $cleanedDomain = str_replace('.com', '', $domain);

        $data['email_domain'] = $cleanedDomain;
    }

        return $data;
    }
}
