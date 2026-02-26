<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ParentStudentController extends Controller
{
    public function index(User $parent): JsonResponse
    {
        Gate::authorize('viewChildren', $parent);

        return new JsonResponse([
            'data' => $parent->children()->paginate(15),
        ]);
    }

    public function link(User $parent, User $student): JsonResponse
    {
        Gate::authorize('linkChild', [$parent, $student]);

        $parent->children()->syncWithoutDetaching([$student->id => ['club_id' => $parent->club_id]]);

        return new JsonResponse(status: 204);
    }

    public function unlink(User $parent, User $student): JsonResponse
    {
        Gate::authorize('unlinkChild', [$parent, $student]);

        $parent->children()->detach($student->id);

        return new JsonResponse(status: 204);
    }

    public function myChildren(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        Gate::authorize('viewChildren', $user);

        return new JsonResponse([
            'data' => $user->children()->paginate(15),
        ]);
    }
}
