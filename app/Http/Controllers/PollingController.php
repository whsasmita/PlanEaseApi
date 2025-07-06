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
use Illuminate\Support\Str;

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
        try {
            $requestData = $request->validated();
            $requestData['user_id'] = auth()->id();

            if ($request->hasFile('polling_image')) {
                $image = $request->file('polling_image');
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('polling_images');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $image->move($destinationPath, $imageName);

                $requestData['polling_image'] = 'polling_images/' . $imageName;
            }

            $polling = Polling::create($requestData);

            foreach ($request->options as $optionData) {
                $polling->options()->create(['option' => $optionData['option']]);
            }

            $polling->load(['user', 'options']);

            return (new PollingResource($polling))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating polling',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            $requestData = $request->validated();

            if ($request->hasFile('polling_image')) {
                if ($polling->polling_image && file_exists(public_path($polling->polling_image))) {
                    if (!Str::contains($polling->polling_image, 'images/default_polling.png')) {
                        unlink(public_path($polling->polling_image));
                    }
                }

                $image = $request->file('polling_image');
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('polling_images');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $image->move($destinationPath, $imageName);

                $requestData['polling_image'] = 'polling_images/' . $imageName;
            }

            $polling->update($requestData);

            if ($request->has('options')) {
                foreach ($request->options as $optionData) {
                    if (isset($optionData['id_option']) && $optionData['id_option'] !== null) {
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating polling',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            if ($polling->polling_image && file_exists(public_path($polling->polling_image))) {
                unlink(public_path($polling->polling_image));
            }

            $polling->delete();

            return response()->json([
                'message' => 'Polling deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting polling',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error casting vote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            'polling_image' => ($polling->polling_image && file_exists(public_path($polling->polling_image)))
                ? asset($polling->polling_image)
                : null,
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

    /**
     * Delete polling image only.
     * Menghapus gambar polling saja tanpa menghapus polling.
     *
     * @param Polling $polling
     * @return JsonResponse
     */
    public function deleteImage(Polling $polling): JsonResponse
    {
        try {
            if ($polling->polling_image && file_exists(public_path($polling->polling_image))) {
                unlink(public_path($polling->polling_image));
                $polling->update(['polling_image' => null]);

                return response()->json([
                    'message' => 'Polling image deleted successfully'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'message' => 'No image to delete'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting image',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
