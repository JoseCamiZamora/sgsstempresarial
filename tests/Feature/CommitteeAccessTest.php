<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CommitteeAccessTest extends TestCase
{
    public function test_guest_cannot_access_committee_module(): void
    {
        $this->get('/comites')->assertRedirect('/login');
    }

    public function test_authenticated_user_without_permission_cannot_access_committee_module(): void
    {
        $user = new User(['name' => 'Sin permiso', 'email' => 'no-permission@example.test']);
        $user->id = 999999999;
        $this->actingAs($user)->get('/comites')->assertForbidden();
    }
}
