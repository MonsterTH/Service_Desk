<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Comment;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ROLES (OBRIGATÓRIO)
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'agent']);
        Role::firstOrCreate(['name' => 'employee']);

        // USERS
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $agent = User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'agent@example.com',
            'password' => bcrypt('password'),
        ]);
        $agent->assignRole('agent');

        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);
        $employee->assignRole('employee');

        // User::factory(5)->create()->each(function ($user) {
        //     $user->assignRole('employee');
        // });

        // CATEGORIES
        $categories = Category::factory(5)->create();

        // TICKETS
        Ticket::factory(20)->create([
            'created_by' => $employee->id,
            'assigned_to' => $agent->id,
            'category_id' => $categories->random()->id,
        ]);

        // COMMENTS
        Comment::factory(50)->create();
    }
}
