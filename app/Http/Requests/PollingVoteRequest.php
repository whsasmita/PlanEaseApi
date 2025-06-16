<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Polling; // Import model Polling
use App\Models\PollingVote; // Import model PollingVote

class PollingVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pollingId = $this->input('polling_id') ?? $this->route('polling');

        $polling = Polling::find($pollingId);

        if (!$polling) {
            return false;
        }

        if ($polling->deadline->isPast()) {
            return false;
        }

        if (auth()->guest()) { 
            return false; 
        } else { 
            $hasVoted = PollingVote::where('polling_id', $pollingId)
                                   ->where('user_id', auth()->id())
                                   ->exists();
            if ($hasVoted) {
                return false;
            }
            return true; 
        }

        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Mendapatkan ID polling dari request untuk digunakan dalam Rule::exists
        $pollingId = $this->input('polling_id') ?? $this->route('polling');

        return [
            'polling_id' => [
                'required',
                'integer',
                Rule::exists('pollings', 'id_polling'),
            ],
            'polling_option_id' => [
                'required',
                'integer',
                Rule::exists('polling_options', 'id_option'),
                Rule::exists('polling_options', 'id_option')->where(function ($query) use ($pollingId) {
                    $query->where('polling_id', $pollingId);
                }),
            ],
            // user_id bersifat opsional karena di migrasi Anda bisa null
            // Jika dikirim, harus ada di tabel users, tapi biasanya ini diisi dari auth()->id() di controller
            // 'user_id' => [
            //     'nullable',
            //     'integer',
            //     Rule::exists('users', 'id_user'),
            // ],
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
            'polling_id.required' => 'The polling ID is required.',
            'polling_id.integer' => 'The polling ID must be an integer.',
            'polling_id.exists' => 'The selected polling does not exist.',
            'polling_option_id.required' => 'The polling option ID is required.',
            'polling_option_id.integer' => 'The polling option ID must be an integer.',
            'polling_option_id.exists' => 'The selected polling option is invalid or does not belong to this polling.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->route('polling')) {
            $this->merge([
                'polling_id' => $this->route('polling'),
            ]);
        }
    }
}