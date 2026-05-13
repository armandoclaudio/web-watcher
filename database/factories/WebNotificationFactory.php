<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\WebNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebNotification>
 */
class WebNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'alert_id' => Alert::factory(),
            'external_id' => fake()->uuid(),
        ];
    }
}
