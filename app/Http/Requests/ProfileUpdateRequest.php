<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'division' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ];
    }

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
        ];
    }
}
