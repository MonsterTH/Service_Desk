<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),

            'status' => $this->faker->randomElement([
                'open',
                'in_progress',
                'resolved',
                'closed'
            ]),

            'priority' => $this->faker->randomElement([
                'low',
                'medium',
                'high',
                'urgent'
            ]),

            'created_by' => User::factory(),
            // 'assigned_to' => User::factory(),
            // 'category_id' => Category::factory(),

            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
