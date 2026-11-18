<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_admin_can_create_short_url(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser($company, Role::Admin, 'admin@acme.test');

        $this->actingAs($admin)
            ->post('/urls', ['original_url' => 'https://example.com/admin-link'])
            ->assertRedirect(route('urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://example.com/admin-link',
            'user_id' => $admin->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_member_can_create_short_url(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $member = $this->makeUser($company, Role::Member, 'member@acme.test');

        $this->actingAs($member)
            ->post('/urls', ['original_url' => 'https://example.com/member-link'])
            ->assertRedirect(route('urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://example.com/member-link',
            'user_id' => $member->id,
        ]);
    }

    public function test_superadmin_cannot_create_short_url(): void
    {
        $super = $this->makeSuperAdmin();

        $this->actingAs($super)->get('/urls/create')->assertForbidden();

        $this->actingAs($super)
            ->post('/urls', ['original_url' => 'https://example.com/nope'])
            ->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_superadmin_sees_short_urls_of_every_company(): void
    {
        $companyA = Company::create(['name' => 'Company A']);
        $companyB = Company::create(['name' => 'Company B']);
        $memberA = $this->makeUser($companyA, Role::Member, 'member@a.test');
        $memberB = $this->makeUser($companyB, Role::Member, 'member@b.test');

        ShortUrl::create(['company_id' => $companyA->id, 'user_id' => $memberA->id,
            'original_url' => 'https://company-a-link.example/', 'code' => 'cmpa0001']);
        ShortUrl::create(['company_id' => $companyB->id, 'user_id' => $memberB->id,
            'original_url' => 'https://company-b-link.example/', 'code' => 'cmpb0001']);

        $this->actingAs($this->makeSuperAdmin())
            ->get('/urls')
            ->assertOk()
            ->assertSee('https://company-a-link.example/')
            ->assertSee('https://company-b-link.example/');
    }

    public function test_admin_sees_only_short_urls_of_their_own_company(): void
    {
        $companyA = Company::create(['name' => 'Company A']);
        $companyB = Company::create(['name' => 'Company B']);

        $adminA = $this->makeUser($companyA, Role::Admin, 'admin@a.test');
        $memberA = $this->makeUser($companyA, Role::Member, 'member@a.test');
        $memberB = $this->makeUser($companyB, Role::Member, 'member@b.test');

        ShortUrl::create(['company_id' => $companyA->id, 'user_id' => $adminA->id,
            'original_url' => 'https://a-admin.example/', 'code' => 'aaaa0001']);
        ShortUrl::create(['company_id' => $companyA->id, 'user_id' => $memberA->id,
            'original_url' => 'https://a-member.example/', 'code' => 'aaaa0002']);
        ShortUrl::create(['company_id' => $companyB->id, 'user_id' => $memberB->id,
            'original_url' => 'https://b-member.example/', 'code' => 'bbbb0001']);

        $this->actingAs($adminA)
            ->get('/urls')
            ->assertOk()
            ->assertSee('https://a-admin.example/')
            ->assertSee('https://a-member.example/')
            ->assertDontSee('https://b-member.example/');
    }

    public function test_member_sees_only_short_urls_created_by_themselves(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser($company, Role::Admin, 'admin@acme.test');
        $member = $this->makeUser($company, Role::Member, 'member@acme.test');
        $otherMember = $this->makeUser($company, Role::Member, 'other@acme.test');

        ShortUrl::create(['company_id' => $company->id, 'user_id' => $member->id,
            'original_url' => 'https://mine.example/', 'code' => 'mine0001']);
        ShortUrl::create(['company_id' => $company->id, 'user_id' => $otherMember->id,
            'original_url' => 'https://other-member.example/', 'code' => 'oth00001']);
        ShortUrl::create(['company_id' => $company->id, 'user_id' => $admin->id,
            'original_url' => 'https://admin-link.example/', 'code' => 'adm00001']);

        $this->actingAs($member)
            ->get('/urls')
            ->assertOk()
            ->assertSee('https://mine.example/')
            ->assertDontSee('https://other-member.example/')
            ->assertDontSee('https://admin-link.example/');
    }

    public function test_short_url_is_publicly_resolvable_and_redirects(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $member = $this->makeUser($company, Role::Member, 'member@acme.test');
        $short = ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member->id,
            'original_url' => 'https://example.com/destination',
            'code' => 'pub12345',
        ]);

        // No actingAs() -> guest / logged out
        $this->get('/s/pub12345')->assertRedirect('https://example.com/destination');

        $this->assertSame(1, $short->fresh()->hits);
    }

    public function test_unknown_short_code_returns_404(): void
    {
        $this->get('/s/doesnotexist')->assertNotFound();
    }

    public function test_guest_cannot_access_short_url_pages(): void
    {
        $this->get('/urls')->assertRedirect(route('login'));
        $this->post('/urls', ['original_url' => 'https://example.com'])->assertRedirect(route('login'));
    }
}
