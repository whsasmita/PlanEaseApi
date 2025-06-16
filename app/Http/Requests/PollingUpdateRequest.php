<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Polling; // Import the Polling model

class PollingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check() && auth()->user()->role === 'admin') {
            return false;
        }

        $pollingId = $this->route('polling');

        $polling = Polling::find($pollingId);

        return $polling && $polling->user_id === auth()->user()->id_user;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // user_id might not be updated, or if it is, only by specific roles
            // 'user_id' => [
            //     'sometimes', // 'sometimes' means validate only if present
            //     'integer',
            //     Rule::exists('users', 'id_user'),
            // ],
            'title' => 'sometimes|string|max:100',
            'description' => 'nullable|string', 
            'polling_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deadline' => 'sometimes|date|after:now', 
            'options' => 'sometimes|array|min:2', 
            'options.*.id_option' => 'sometimes|integer|exists:polling_options,id_option',
            'options.*.option' => 'required|string|max:255',
            'options_to_delete' => 'nullable|array',
            'options_to_delete.*' => 'integer|exists:polling_options,id_option',
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
            'title.sometimes' => 'The polling title must be a string.',
            'title.max' => 'The polling title may not be greater than 100 characters.',
            'deadline.date' => 'The deadline must be a valid date and time.',
            'deadline.after' => 'The deadline must be a date and time in the future.',
            'polling_image.image' => 'The polling image must be an image.',
            'polling_image.mimes' => 'The polling image must be a file of type: :values.',
            'polling_image.max' => 'The polling image may not be greater than 2 MB.',
            'options.array' => 'The options must be an array.',
            'options.min' => 'Please provide at least 2 options for the polling.',
            'options.*.id_option.exists' => 'One or more option IDs provided for update do not exist.',
            'options.*.option.required' => 'Each polling option cannot be empty.',
            'options.*.option.string' => 'Each polling option must be a string.',
            'options.*.option.max' => 'Each polling option may not be greater than 255 characters.',
            'options_to_delete.*.exists' => 'One or more option IDs to delete do not exist.',
        ];
    }
}