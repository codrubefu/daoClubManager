<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
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
}
