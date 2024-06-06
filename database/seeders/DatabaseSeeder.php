<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Ragnar',
            'email' => 'test@test.com',
        ]);

        Chat::factory(10)->has(Message::factory()->times(10))->create([
            'user_id' => $user->id,
        ]);

    }
}
