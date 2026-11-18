<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the SuperAdmin account using RAW SQL (as required by the assignment).
     * Credentials can be overridden from .env:
     *   SUPERADMIN_NAME, SUPERADMIN_EMAIL, SUPERADMIN_PASSWORD
     */
    public function run(): void
    {
        $name = env('SUPERADMIN_NAME', 'Super Admin');
        $email = env('SUPERADMIN_EMAIL', 'superadmin@gmail.com');
        $password = Hash::make(env('SUPERADMIN_PASSWORD', 'Test@123'));
        $now = now()->toDateTimeString();

        $exists = DB::select('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);

        if (! empty($exists)) {
            return;
        }

        DB::insert(
            'INSERT INTO users (name, email, password, role, company_id, created_at, updated_at)
             VALUES (?, ?, ?, ?, NULL, ?, ?)',
            [$name, $email, $password, 'SuperAdmin', $now, $now]
        );
    }
}
