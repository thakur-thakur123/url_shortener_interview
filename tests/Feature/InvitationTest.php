<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_superadmin_invites_an_admin_into_a_new_company(): void
    {
        $this->actingAs($this->makeSuperAdmin())
            ->post('/invitations', [
                'email' => 'boss@newco.test',
                'company_name' => 'New Co',
            ])
            ->assertRedirect(route('invitations.create'));

        $company = Company::where('name', 'New Co')->firstOrFail();

        $this->assertDatabaseHas('invitations', [
            'email' => 'boss@newco.test',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);
    }

    public function test_superadmin_cannot_invite_a_member_only_admins(): void
    {
        $this->actingAs($this->makeSuperAdmin())
            ->post('/invitations', [
                'email' => 'someone@newco.test',
                'company_name' => 'New Co',
                'role' => 'Member', // ignored: SuperAdmin can only invite Admins
            ]);

        $this->assertDatabaseHas('invitations', ['email' => 'someone@newco.test', 'role' => 'Admin']);
        $this->assertDatabaseMissing('invitations', ['email' => 'someone@newco.test', 'role' => 'Member']);
    }

    public function test_admin_can_invite_admin_and_member_into_own_company_only(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser($company, Role::Admin, 'admin@acme.test');

        $this->actingAs($admin)->post('/invitations', ['email' => 'a2@acme.test', 'role' => 'Admin'])
            ->assertRedirect(route('invitations.create'));
        $this->actingAs($admin)->post('/invitations', ['email' => 'm1@acme.test', 'role' => 'Member'])
            ->assertRedirect(route('invitations.create'));

        $this->assertDatabaseHas('invitations', ['email' => 'a2@acme.test', 'role' => 'Admin', 'company_id' => $company->id]);
        $this->assertDatabaseHas('invitations', ['email' => 'm1@acme.test', 'role' => 'Member', 'company_id' => $company->id]);
        $this->assertDatabaseCount('companies', 1);
    }

    public function test_admin_cannot_invite_superadmin_role(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser($company, Role::Admin, 'admin@acme.test');

        $this->actingAs($admin)
            ->post('/invitations', ['email' => 'x@acme.test', 'role' => 'SuperAdmin'])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_member_cannot_invite_anyone(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $member = $this->makeUser($company, Role::Member, 'member@acme.test');

        $this->actingAs($member)->get('/invitations')->assertForbidden();
        $this->actingAs($member)
            ->post('/invitations', ['email' => 'x@acme.test', 'role' => 'Member'])
            ->assertForbidden();
    }

    public function test_invited_user_can_accept_and_gets_correct_role_and_company(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser($company, Role::Admin, 'admin@acme.test');
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $admin->id,
            'email' => 'new@acme.test',
            'role' => Role::Member,
        ]);

        $this->post('/invitations/'.$invitation->token, [
            'name' => 'New Person',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ])->assertRedirect('/');

        $user = User::where('email', 'new@acme.test')->firstOrFail();
        $this->assertSame(Role::Member, $user->role);
        $this->assertSame($company->id, $user->company_id);
        $this->assertNotNull($invitation->fresh()->accepted_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_accepted_invitation_cannot_be_reused(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser($company, Role::Admin, 'admin@acme.test');
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $admin->id,
            'email' => 'new@acme.test',
            'role' => Role::Member,
            'accepted_at' => now(),
        ]);

        $this->get('/invitations/'.$invitation->token)->assertNotFound();
    }
}
