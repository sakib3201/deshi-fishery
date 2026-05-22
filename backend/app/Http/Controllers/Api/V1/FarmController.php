<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $farms = $request->user()->farms->map(function ($farm) {
            return [
                'id' => $farm->id,
                'name' => $farm->name,
                'location' => $farm->location,
                'role' => $farm->pivot->role,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $farms,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ValidationError',
                    'message' => 'The given data was invalid.',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        $user = $request->user();

        $existingFarm = $user->farms()->where('name', $request->input('name'))->first();
        if ($existingFarm) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DuplicateFarmName',
                    'message' => 'You already have a farm with this name.',
                    'details' => [],
                ],
            ], 422);
        }

        $farm = Farm::create([
            'name' => $request->input('name'),
            'location' => $request->input('location'),
        ]);

        $farm->users()->attach($user->id, ['role' => 'owner']);

        $user->update(['current_farm_id' => $farm->id]);

        return response()->json([
            'success' => true,
            'data' => $farm,
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $farm = $request->user()->farms()->where('farms.id', $id)->first();

        if (! $farm) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NotFound',
                    'message' => 'Farm not found.',
                    'details' => [],
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $farm,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $farm = $request->user()->farms()->where('farms.id', $id)->wherePivot('role', 'owner')->first();

        if (! $farm) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'Forbidden',
                    'message' => 'You do not have permission to update this farm.',
                    'details' => [],
                ],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ValidationError',
                    'message' => 'The given data was invalid.',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        $farm->update($request->only(['name', 'location']));

        return response()->json([
            'success' => true,
            'data' => $farm,
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $farm = $request->user()->farms()->where('farms.id', $id)->wherePivot('role', 'owner')->first();

        if (! $farm) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'Forbidden',
                    'message' => 'You do not have permission to delete this farm.',
                    'details' => [],
                ],
            ], 403);
        }

        $farm->delete();

        return response()->json([
            'success' => true,
        ], 204);
    }
}
