<?php

namespace Database\Factories;

use App\Models\CardboardProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardboardProductFactory extends Factory
{
    protected $model = CardboardProduct::class;

    public function definition()
    {
        $sizes = ['Kecil', 'Sedang', 'Besar', 'Extra Besar', 'Jumbo'];
        $types = ['Standard', 'Premium', 'Ekonomi', 'Super'];
        
        return [
            'name' => 'Kardus ' . $this->faker->randomElement($types) . ' ' . $this->faker->randomElement($sizes),
            'price' => $this->faker->numberBetween(5000, 20000),
        ];
    }
}