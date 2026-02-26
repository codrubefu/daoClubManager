<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        return new JsonResponse([
            'data' => User::query()->paginate((int) $request->query('per_page', 15)),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        return new JsonResponse(['data' => $user]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $user = User::query()->create([
            'name' => $request->string('name')->value(),
            'email' => $request->string('email')->value(),
            'password' => Hash::make($request->string('password')->value()),
            'role' => $request->string('role')->value(),
        ]);

        return new JsonResponse(['data' => $user], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        $attributes = $request->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $attributes['password'] = Hash::make($request->string('password')->value());
        }

        $user->update($attributes);

        return new JsonResponse(['data' => $user->refresh()]);
    }

    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return new JsonResponse(status: 204);
    }
}
