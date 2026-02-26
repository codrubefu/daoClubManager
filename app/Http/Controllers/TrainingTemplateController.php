<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrainingTemplateRequest;
use App\Http\Requests\UpdateTrainingTemplateRequest;
use App\Models\TrainingTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrainingTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', TrainingTemplate::class);

        return new JsonResponse([
            'data' => TrainingTemplate::query()
                ->with('group')
                ->paginate((int) $request->query('per_page', 15)),
        ]);
    }

    public function show(TrainingTemplate $trainingTemplate): JsonResponse
    {
        Gate::authorize('view', $trainingTemplate);

        $trainingTemplate->load('group');

        return new JsonResponse(['data' => $trainingTemplate]);
    }

    public function store(StoreTrainingTemplateRequest $request): JsonResponse
    {
        Gate::authorize('create', TrainingTemplate::class);

        $trainingTemplate = TrainingTemplate::query()->create($request->validated());

        return new JsonResponse(['data' => $trainingTemplate], 201);
    }

    public function update(UpdateTrainingTemplateRequest $request, TrainingTemplate $trainingTemplate): JsonResponse
    {
        Gate::authorize('update', $trainingTemplate);

        $trainingTemplate->fill($request->validated());
        $trainingTemplate->save();

        return new JsonResponse(['data' => $trainingTemplate]);
    }

    public function destroy(TrainingTemplate $trainingTemplate): JsonResponse
    {
        Gate::authorize('delete', $trainingTemplate);

        $trainingTemplate->delete();

        return new JsonResponse(status: 204);
    }
}
