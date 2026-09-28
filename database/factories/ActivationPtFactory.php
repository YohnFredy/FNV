<?php

namespace Database\Factories;

use App\Models\ActivationPt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivationPt>
 */
class ActivationPtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'min_pts_first' => 100.00,
            'min_pts_monthly' => 50.00,
        ];
    }
}
