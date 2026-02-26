<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Support\TenantContext;
use Tests\TestCase;

class ParentStudentPolicyTest extends TestCase
{
    public function test_parent_can_view_only_linked_student(): void
    {
        $club = Club::query()->create([
            'name' => 'Alpha Club',
            'slug' => 'alpha',
        ]);

        app(TenantContext::class)->setTenant((int) $club->id, (string) $club->slug);

        $parent = User::query()->create([
            'name' => 'Parent One',
            'email' => 'parent@example.com',
            'password' => bcrypt('password123'),
            'role' => 'parent',
        ]);

        $linkedStudent = User::query()->create([
            'name' => 'Linked Student',
            'email' => 'linked@example.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $otherStudent = User::query()->create([
            'name' => 'Other Student',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $parent->children()->attach($linkedStudent->id, ['club_id' => $club->id]);

        $policy = new UserPolicy();

        $this->assertTrue($policy->view($parent, $linkedStudent));
        $this->assertFalse($policy->view($parent, $otherStudent));
    }
}
