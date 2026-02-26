<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AssignCoachToGroupRequest;
use App\Http\Requests\AssignStudentToGroupRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Group::class);

        $groups = Group::query()->with(['coaches', 'students']);

        if ($request->user()?->role === 'coach') {
            $groups->whereHas('coaches', fn ($query) => $query->where('users.id', $request->user()?->id));
        }

        return new JsonResponse([
            'data' => $groups->paginate((int) $request->query('per_page', 15)),
        ]);
    }

    public function show(Group $group): JsonResponse
    {
        Gate::authorize('view', $group);

        $group->load(['coaches', 'students']);

        return new JsonResponse(['data' => $group]);
    }

    public function store(StoreGroupRequest $request): JsonResponse
    {
        Gate::authorize('create', Group::class);

        $group = Group::query()->create([
            'name' => $request->string('name')->value(),
            'description' => $request->input('description'),
        ]);

        return new JsonResponse(['data' => $group], 201);
    }

    public function update(UpdateGroupRequest $request, Group $group): JsonResponse
    {
        Gate::authorize('update', $group);

        $group->fill($request->validated());
        $group->save();

        return new JsonResponse(['data' => $group]);
    }

    public function destroy(Group $group): JsonResponse
    {
        Gate::authorize('delete', $group);

        $group->delete();

        return new JsonResponse(status: 204);
    }

    public function assignCoach(AssignCoachToGroupRequest $request, Group $group): JsonResponse
    {
        Gate::authorize('assignCoach', $group);

        $group->coaches()->syncWithoutDetaching([(int) $request->integer('coach_id')]);

        return new JsonResponse(['data' => $group->load(['coaches', 'students'])]);
    }

    public function assignStudent(AssignStudentToGroupRequest $request, Group $group): JsonResponse
    {
        Gate::authorize('assignStudent', $group);

        $group->students()->syncWithoutDetaching([(int) $request->integer('student_id')]);

        return new JsonResponse(['data' => $group->load(['coaches', 'students'])]);
    }
}
