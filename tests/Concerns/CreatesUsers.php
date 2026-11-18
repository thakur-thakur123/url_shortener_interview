<?php

namespace Tests\Concerns;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;

trait CreatesUsers
{
    protected function makeSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => 'password',
            'role' => Role::SuperAdmin,
            'company_id' => null,
        ]);
    }

    protected function makeUser(Company $company, Role $role, string $email): User
    {
        return User::create([
            'name' => $role->value.' '.$email,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'company_id' => $company->id,
        ]);
    }
}
