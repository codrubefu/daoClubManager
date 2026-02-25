<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\RefreshToken;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use RuntimeException;

class TokenService
{
    public function issueAccessToken(User $user): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']) ?: '{}');
        $payload = $this->base64UrlEncode(json_encode([
            'sub' => $user->id,
            'jti' => (string) Str::uuid(),
            'tid' => $user->club_id,
            'exp' => CarbonImmutable::now()->addMinutes(15)->timestamp,
        ]) ?: '{}');

        $signature = hash_hmac('sha256', $header . '.' . $payload, (string) config('app.key'), true);

        return sprintf('%s.%s.%s', $header, $payload, $this->base64UrlEncode($signature));
    }

    public function issueRefreshToken(User $user, ?string $familyId = null): array
    {
        $plainToken = Str::random(64);

        $record = RefreshToken::query()->create([
            'user_id' => $user->id,
            'club_id' => $user->club_id,
            'family_id' => $familyId ?? (string) Str::uuid(),
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => CarbonImmutable::now()->addDays(14),
        ]);

        return [$plainToken, $record];
    }

    public function rotateRefreshToken(string $refreshToken): array
    {
        $hash = hash('sha256', $refreshToken);

        /** @var RefreshToken|null $record */
        $record = RefreshToken::query()
            ->where('token_hash', $hash)
            ->first();

        if (!$record || $record->expires_at->isPast() || $record->revoked_at !== null) {
            throw new RuntimeException('Invalid refresh token.');
        }

        if ($record->rotated_at !== null) {
            RefreshToken::query()
                ->where('family_id', $record->family_id)
                ->update(['revoked_at' => now()]);

            throw new RuntimeException('Refresh token reuse detected.');
        }

        $record->update(['rotated_at' => now()]);

        $user = $record->user;
        [$nextRefresh, $nextRecord] = $this->issueRefreshToken($user, $record->family_id);

        return [$this->issueAccessToken($user), $nextRefresh, $nextRecord];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
