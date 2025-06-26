<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleCreateRequest extends FormRequest
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
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The schedule title is required.',
            'title.string' => 'The schedule title must be a string.',
            'title.max' => 'The schedule title may not be greater than :max characters.',

            'description.string' => 'The description must be a string.',

            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',

            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->start_date && $this->end_date) {
                $startDate = \Carbon\Carbon::parse($this->start_date);
                $endDate = \Carbon\Carbon::parse($this->end_date);
                
                // Check if date range is not too long (optional validation)
                if ($startDate->diffInDays($endDate) > 365) {
                    $validator->errors()->add('end_date', 'The schedule duration cannot exceed 365 days.');
                }
            }
        });
    }
}