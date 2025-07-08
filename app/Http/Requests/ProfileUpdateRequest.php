<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Added for potential future use with Rule::unique
use Illuminate\Validation\Rule; // Added for potential future use with Rule::unique

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Keeping it true as per your provided code
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the profile ID from the route parameters (if needed for unique rules)
        // $profileId = $this->route('profile');
        // $profile = \App\Models\Profile::find($profileId);
        // $userId = $profile ? $profile->user_id : null;

        return [
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'division' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'full_name' => 'required|string|max:255', // Added for User model update
            'phone' => 'required|string|max:20', // Added for User model update
            // Uncomment the 'email' rule if you decide to allow email updates via this endpoint
            // 'email' => [
            //     'required',
            //     'string',
            //     'email',
            //     'max:255',
            //     // Ensure email is unique, but ignore the current user's email
            //     // Rule::unique('users')->ignore($userId, 'id_user'), // Assuming 'id_user' is the primary key of your User model
            // ],
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'photo_profile.image' => 'Photo profile must be an image.',
            'photo_profile.mimes' => 'Photo profile must be a file of type: jpeg, png, jpg, gif, svg.',
            'photo_profile.max' => 'Photo profile may not be greater than 2048 kilobytes.',

            'division.required' => 'Division is required.',
            'division.string' => 'Division must be a string.',
            'division.max' => 'Division may not be greater than 255 characters.',
            
            'position.required' => 'Position is required.',
            'position.string' => 'Position must be a string.',
            'position.max' => 'Position may not be greater than 255 characters.',

            'full_name.required' => 'Nama lengkap wajib diisi.', // Added message
            'full_name.string' => 'Nama lengkap harus berupa teks.', // Added message
            'full_name.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.', // Added message

            'phone.required' => 'Nomor telepon wajib diisi.', // Added message
            'phone.string' => 'Nomor telepon harus berupa teks.', // Added message
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.', // Added message

            // Uncomment these if you enable email validation
            // 'email.required' => 'Email wajib diisi.',
            // 'email.email' => 'Format email tidak valid.',
            // 'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
        ];
    }
}