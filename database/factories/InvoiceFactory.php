<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Invoice>
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'invoice_number' => 'INV-'.fake()->unique()->numerify('######'),
            'total_amount' => fake()->randomFloat(4, 50, 1000),
            'commission_total' => fake()->randomFloat(4, 5, 200),
            'pts' => fake()->randomFloat(4, 10, 150),
            'status' => 'approved',
            'description' => 'Compra registrada mediante factory',
        ];
    }
}
