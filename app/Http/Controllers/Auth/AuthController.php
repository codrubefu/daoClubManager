<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Models\User;
use App\Services\Auth\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if (!$user || !Hash::check($request->string('password')->value(), $user->password)) {
            return new JsonResponse(['message' => 'Invalid credentials.'], 401);
        }

        [$refreshToken] = $this->tokenService->issueRefreshToken($user);

        return new JsonResponse([
            'access_token' => $this->tokenService->issueAccessToken($user),
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 900,
        ]);
    }

    public function refresh(RefreshRequest $request): JsonResponse
    {
        try {
            [$accessToken, $refreshToken] = $this->tokenService->rotateRefreshToken(
                $request->string('refresh_token')->value(),
            );

            return new JsonResponse([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => 900,
            ]);
        } catch (RuntimeException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 401);
        }
    }
}
