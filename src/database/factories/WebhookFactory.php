<?php

namespace StarEditions\WebhookEvent\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use StarEditions\WebhookEvent\Models\Webhook;

class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owner_id' => $this->faker->name(),
            'owner_type' => $this->faker->unique()->safeEmail(),
            'url' => $this->faker->url(),
            'enabled'=> 1,
            'topic' => 'something/random', // password
            'scope' => "scope",
        ];
    }

}
