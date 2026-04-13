<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //USERS
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $agent = User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'agent@example.com',
        ]);
        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
        ]);

        User::factory(5)->create();

        //CATEGORIES
        $categories = Category::factory(5)->create();

        //TICKETS
        Ticket::factory(20)->create([
            'created_by' => $employee->id,
            'assigned_to' => $agent->id,
            'category_id' => $categories->random()->id,
        ]);

        //COMMENTS
        Comment::factory(50)->create();
    }
}
