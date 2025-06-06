<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotulaCreateRequest;
use App\Http\Requests\NotulaUpdateRequest;
use App\Http\Resources\NotulaResource;
use App\Models\Notula;
use Illuminate\Http\JsonResponse;

class NotulaController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $notulas = Notula::all();

        return response()->json([
            'message' => 'Notula retrieved successfully',
            'data' => NotulaResource::collection($notulas)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotulaCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $notula = Notula::create($validatedData);

            return response()->json([
                'message' => 'Schedule created successfully',
                'data' => new NotulaResource($notula)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the Schedule.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $notula = Notula::find($id);

        if (!$notula) {
            return response()->json([
                'message' => 'Notula not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Notula retrieved successfully',
            'data' => new NotulaResource($notula)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotulaUpdateRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $notula = Notula::find($id);

            if (!$notula) {
                return response()->json([
                    'message' => 'Notula not found',
                    'data' => []
                ], 404);
            }

            $notula->update($validatedData);

            return response()->json([
                'message' => 'Notula updated successfully',
                'data' =>  new NotulaResource($notula->fresh())
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the Notula.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $notula = Notula::find($id);

        if (!$notula) {
            return response()->json([
                'message' => 'Notula not found',
                'data' => []
            ], 404);
        }

        $notula->delete();

        return response()->json([
            'message' => 'Notula deleted successfully'
        ], 200);
    }
}
