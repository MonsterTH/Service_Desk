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
        // ROLES
        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        Role::firstOrCreate([
            'name' => 'agent',
            'guard_name' => 'web'
        ]);

        Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'web'
        ]);

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
        $categories = Category::factory(10)->create();

        // TICKETS
        $activeCategories = Category::where('is_active', true)->get();

        Ticket::factory(100)->create([
            'created_by' => $employee->id,
            'assigned_to' => $agent->id,
            'category_id' => fn () => $activeCategories->random()->id,
        ]);

        // COMMENTS
        $tickets = Ticket::all();
        $employees = User::role('employee')->get();

        Comment::factory(50)->make()->each(function ($comment) use ($tickets, $employees) {
            $comment->ticket_id = $tickets->random()->id;
            $comment->user_id = $employees->random()->id;
            $comment->save();
        });
    }
}
