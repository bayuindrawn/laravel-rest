<?php

namespace Database\Factories;

use App\Models\Companies;
use App\Models\FavouriteCompanies;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavouriteCompaniesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FavouriteCompanies::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => rand(1, 10),
            'companies_id' => rand(1, 10)
        ];
    }
}
