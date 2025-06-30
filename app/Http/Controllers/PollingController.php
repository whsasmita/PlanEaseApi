<?php

namespace App\Http\Controllers;

use App\Models\Polling;
use App\Models\PollingOption;
use App\Models\PollingVote;
use App\Http\Requests\PollingCreateRequest;
use App\Http\Requests\PollingUpdateRequest;
use App\Http\Requests\PollingVoteRequest;
use App\Http\Resources\PollingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PollingController
{
    /**
     * Display a listing of the pollings.
     * Mengambil daftar semua polling yang tersedia.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $pollings = Polling::with(['user', 'options'])
            ->latest()
            ->paginate(10);

        return PollingResource::collection($pollings)->response();
    }

    /**
     * Store a newly created polling in storage.
     * Menyimpan polling baru ke database.
     *
     * @param PollingCreateRequest $request
     * @return JsonResponse
     */
    public function store(PollingCreateRequest $request): JsonResponse
    {
        $requestData = $request->validated();
        $requestData['user_id'] = auth()->id();

        $polling = Polling::create($requestData);

        foreach ($request->options as $optionData) {
            $polling->options()->create(['option' => $optionData['option']]);
        }

        $polling->load(['user', 'options']);

        return (new PollingResource($polling))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified polling.
     * Menampilkan detail satu polling berdasarkan ID.
     *
     * @param Polling $polling (Route Model Binding)
     * @return JsonResponse
     */
    public function show(Polling $polling): JsonResponse
    {
        $polling->load(['user', 'options.votes', 'votes']);

        return (new PollingResource($polling))->response();
    }

    /**
     * Update the specified polling in storage.
     * Memperbarui data polling yang sudah ada.
     *
     * @param PollingUpdateRequest $request
     * @param Polling $polling (Route Model Binding)
     * @return JsonResponse
     */
    public function update(PollingUpdateRequest $request, Polling $polling): JsonResponse
    {
        $polling->update($request->validated());

        if ($request->has('options')) {
            foreach ($request->options as $optionData) {
                if (isset($optionData['id_option'])) {
                    $option = $polling->options()->where('id_option', $optionData['id_option'])->first();
                    if ($option) {
                        $option->update(['option' => $optionData['option']]);
                    }
                } else {
                    $polling->options()->create(['option' => $optionData['option']]);
                }
            }
        }

        if ($request->has('options_to_delete') && is_array($request->options_to_delete)) {
            $polling->options()
                ->whereIn('id_option', $request->options_to_delete)
                ->delete();
        }

        $polling->load(['user', 'options']);

        return (new PollingResource($polling))->response();
    }

    /**
     * Remove the specified polling from storage.
     * Menghapus polling dari database.
     *
     * @param Polling $polling (Route Model Binding)
     * @return JsonResponse
     */
    public function destroy(Polling $polling): JsonResponse
    {
        if (!$polling) {
            return response()->json([
                'message' => 'Polling not found',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        $polling->delete();

        return response()->json([
            'message' => 'Polling deleted successfully'
        ], Response::HTTP_OK);
    }


    /**
     * Store a vote for a polling option.
     * Menyimpan suara pengguna pada opsi polling.
     *
     * @param PollingVoteRequest $request
     * @return JsonResponse
     */
    public function vote(PollingVoteRequest $request): JsonResponse
    {
        $voteData = [
            'polling_id' => $request->polling_id,
            'polling_option_id' => $request->polling_option_id,
        ];

        if (auth()->check()) {
            $voteData['user_id'] = auth()->id();
        } else {
            $voteData['user_id'] = null;
        }

        $vote = PollingVote::create($voteData);

        return response()->json([
            'message' => 'Your vote has been cast successfully!',
            'vote_id' => $vote->id_vote,
            'polling_id' => $vote->polling_id,
            'option_id' => $vote->polling_option_id,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the results of a specific polling.
     * Menampilkan hasil dari sebuah polling, termasuk jumlah suara untuk setiap opsi.
     *
     * @param Polling $polling (Route Model Binding)
     * @return JsonResponse
     */
    public function results(Polling $polling): JsonResponse
    {
        $polling->load(['options' => function ($query) {
            $query->withCount('votes');
        }]);

        $totalVotes = $polling->options->sum('votes_count');

        $results = [
            'id' => $polling->id_polling,
            'title' => $polling->title,
            'description' => $polling->description,
            'deadline' => $polling->deadline->format('Y-m-d H:i:s'),
            'is_open' => $polling->deadline->isFuture(),
            'total_votes_cast' => $totalVotes,
            'options' => $polling->options->map(function ($option) use ($totalVotes) {
                $percentage = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100, 2) : 0;
                return [
                    'id' => $option->id_option,
                    'option_text' => $option->option,
                    'vote_count' => $option->votes_count,
                    'percentage' => $percentage,
                ];
            }),
        ];

        return response()->json($results);
    }
}
