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
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        $agent = User::firstOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Support Agent',
                'password' => bcrypt('password'),
            ]
        );
        $agent->assignRole('agent');

        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee User',
                'password' => bcrypt('password'),
            ]
        );
        $employee->assignRole('employee');

        User::factory(20)->create()->each(function ($user) {
            $user->assignRole('employee');
        });

        // CATEGORIES
        $categories = Category::factory(10)->create();

        // TICKETS
        $activeCategories = Category::where('is_active', true)->get();
        $users = User::all();


        Ticket::factory(100)->create([
            'created_by' => fn () => $users->random()->id,
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
