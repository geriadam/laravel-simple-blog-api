<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence();

        return [
            'title' => $this->faker->name(),
            'author_id' => function() {
                return User::factory()->create()->id;
            },
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}
