<?php

namespace Database\Factories;

use App\Models\superadmin_tool_flag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\superadmin_tool_flags>
 */
class superadmin_tool_flagsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(Faker $faker): array
    {
        return [
            //
            'clientgroupid' => $faker->random(10),
            'networkid' => $faker->random(10),
            'branchid' => $faker->random(10),
            'terminalno' => $faker->random(10),
            'type' => $faker->random(10),
            'value' => rand(0,1),
        ];
    }
}
