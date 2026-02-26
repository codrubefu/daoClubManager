<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Group;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_sees_only_assigned_groups(): void
    {
        $club = Club::query()->create([
            'name' => 'Club One',
            'slug' => 'club-one',
        ]);

        app(TenantContext::class)->setTenant($club->id, $club->slug);

        $coach = User::query()->create([
            'name' => 'Assigned Coach',
            'email' => 'coach@club-one.test',
            'password' => bcrypt('password123'),
            'role' => 'coach',
        ]);

        Sanctum::actingAs($coach);

        $groupVisible = Group::query()->create([
            'name' => 'Visible Group',
        ]);

        $groupHidden = Group::query()->create([
            'name' => 'Hidden Group',
        ]);

        $groupVisible->coaches()->attach($coach->id);

        $response = $this->withHeader('X-Tenant', $club->slug)
            ->getJson('/api/groups');

        $response->assertOk();
        $response->assertJsonPath('data.total', 1);
        $response->assertJsonFragment(['id' => $groupVisible->id, 'name' => 'Visible Group']);
        $response->assertJsonMissing(['id' => $groupHidden->id, 'name' => 'Hidden Group']);
    }
}
