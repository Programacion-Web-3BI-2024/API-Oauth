<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        \App\Models\User::factory(1)->create([
            "name" => "usuario",
            "email" => "usuario@email.com"
        ]);

        Client::create([
            'id' => 100,
            'name' => 'Tests',
            'secret' => "wsBa0mp4jwSTYssUGHX5xoqD9IC0X95Gfpg0w3uY",
            'redirect' => 'http://localhost',
            'provider' => 'users',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false
        ]);
    }
}
