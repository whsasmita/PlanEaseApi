<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Polling;
use App\Models\PollingVote;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PollingVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            throw new HttpResponseException(
                response()->json(['message' => 'Authentication required to cast a vote.'], Response::HTTP_UNAUTHORIZED)
            );
        }

        $pollingId = $this->input('polling_id') ?? $this->route('polling');

        $polling = Polling::find($pollingId);

        if (!$polling) {
            throw new HttpResponseException(
                response()->json(['message' => 'Polling not found.'], Response::HTTP_NOT_FOUND)
            );
        }

        if ($polling->deadline->isPast()) {
            throw new HttpResponseException(
                response()->json(['message' => 'This polling has ended and no longer accepts votes.'], Response::HTTP_FORBIDDEN)
            );
        }

        // Check if the authenticated user has already voted on this specific poll
        $hasVoted = PollingVote::where('polling_id', $pollingId)
                               ->where('user_id', auth()->id())
                               ->exists();

        if ($hasVoted) {
            throw new HttpResponseException(
                response()->json(['message' => 'You have already voted on this polling.'], Response::HTTP_FORBIDDEN)
            );
        }

        return true; // If all checks pass, the user is authorized
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
                // Memastikan opsi yang dipilih adalah milik polling yang benar
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
