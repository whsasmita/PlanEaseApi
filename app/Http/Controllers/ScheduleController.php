<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleCreateRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class ScheduleController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $schedule = Schedule::all();

        return response()->json([
            'message' => 'Schedule retrieved successfully',
            'data' => ScheduleResource::collection($schedule)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ScheduleCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $schedule = Schedule::create($validatedData);

            return response()->json([
                'message' => 'Schedule created successfully',
                'data' => new ScheduleResource($schedule)
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
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json([
                'message' => 'Schedule not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Schedule retrieved successfully',
            'data' => new ScheduleResource($schedule)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScheduleUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();

        try {
            $schedule = Schedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'message' => 'Schedule not found',
                    'data' => []
                ], 404);
            }

            $schedule->update($validatedData);

            return response()->json([
                'message' => 'Schedule updated successfully',
                'data' => new ScheduleResource($schedule->fresh())
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the Schedule.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json([
                'message' => 'Schedule not found',
                'data' => []
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully'
        ], 200);
    }
}
