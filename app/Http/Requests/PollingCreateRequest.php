<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PollingCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya pengguna dengan peran 'ADMIN' yang diizinkan membuat polling
        return auth()->check() && auth()->user()->role === 'ADMIN';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Hapus aturan 'user_id' karena akan diisi otomatis di controller
            // 'user_id' => [
            //     'required',
            //     'integer',
            //     Rule::exists('users', 'id_user'),
            // ],
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'polling_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deadline' => 'required|date|after:now',
            'options' => 'required|array|min:2',
            'options.*.option' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // Hapus pesan kustom untuk 'user_id' juga
            // 'user_id.required' => 'The user ID is required.',
            // 'user_id.exists' => 'The selected user ID is invalid.',
            'title.required' => 'The polling title is required.',
            'title.max' => 'The polling title may not be greater than 100 characters.',
            'deadline.required' => 'The deadline is required.',
            'deadline.date' => 'The deadline must be a valid date and time.',
            'deadline.after' => 'The deadline must be a date and time in the future.',
            'polling_image.image' => 'The polling image must be an image.',
            'polling_image.mimes' => 'The polling image must be a file of type: :values.',
            'polling_image.max' => 'The polling image may not be greater than 2 MB.',
            'options.required' => 'At least two polling options are required.',
            'options.array' => 'The options must be an array.',
            'options.min' => 'Please provide at least 2 options for the polling.',
            'options.*.option.required' => 'Each polling option cannot be empty.',
            'options.*.option.string' => 'Each polling option must be a string.',
            'options.*.option.max' => 'Each polling option may not be greater than 255 characters.',
        ];
    }
}
