<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            // Note: We are temporarily hardcoding IDs 1 and 2. 
            // This assumes you have at least two categories and brands in your database!
            'category_id' => $this->faker->randomElement([1, 2]),
            'brand_id' => $this->faker->randomElement([1, 2]),
            
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 2500), // Random price between 50.00 and 2500.00
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['active', 'active', 'draft']), // Weighted to 'active'
            'main_image' => 'https://via.placeholder.com/640x480.png/002233?text=Tech-tak+Hardware',
        ];
    }
}