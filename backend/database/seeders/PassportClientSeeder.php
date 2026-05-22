<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class PassportClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::create([
            'id' => 'test-password-client-id',
            'secret' => 'test-password-client-secret',
            'name' => 'Test Password Client',
            'password_client' => true,
            'personal_access_client' => false,
            'redirect' => '',
            'revoked' => false,
        ]);

        Client::create([
            'id' => 'test-personal-client-id',
            'secret' => 'test-personal-client-secret',
            'name' => 'Test Personal Access Client',
            'password_client' => false,
            'personal_access_client' => true,
            'redirect' => '',
            'revoked' => false,
        ]);
    }
}
