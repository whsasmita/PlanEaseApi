<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Polling;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PollingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 1. Ensure user is authenticated first
        if (!auth()->check()) {
            throw new HttpResponseException(
                response()->json(['message' => 'Authentication required to update a polling.'], Response::HTTP_UNAUTHORIZED)
            );
        }

        // Fix: Handle Route Model Binding properly
        $polling = $this->route('polling');
        
        // If it's already a Polling model (Route Model Binding)
        if ($polling instanceof Polling) {
            $pollingModel = $polling;
        } else {
            // If it's an ID, find the model
            $pollingModel = Polling::find($polling);
        }

        // 2. Check if the polling exists
        if (!$pollingModel) {
            throw new HttpResponseException(
                response()->json(['message' => 'Polling not found.'], Response::HTTP_NOT_FOUND)
            );
        }

        // 3. Check if the authenticated user is the owner OR an admin
        $user = auth()->user();
        if ($pollingModel->user_id === $user->id_user || $user->hasRole('ADMIN')) {
            return true; // Authorized if owner or admin
        }

        // If none of the above conditions are met, deny access
        throw new HttpResponseException(
            response()->json(['message' => 'You are not authorized to update this polling.'], Response::HTTP_FORBIDDEN)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:100',
            'description' => 'nullable|string', 
            'polling_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deadline' => 'sometimes|date|after:now', 
            'options' => 'sometimes|array|min:2', 
            // Fix: Make id_option nullable since it might not exist for new options
            'options.*.id_option' => 'nullable|integer|exists:polling_options,id_option',
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
