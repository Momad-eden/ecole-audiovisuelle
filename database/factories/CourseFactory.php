<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title'          => ucfirst($title),
            'slug'           => Str::slug($title),
            'category'       => fake()->randomElement(['Audiovisuel', 'Son', 'Montage', 'Photographie']),
            'description'    => fake()->paragraph(),
            'duration'       => fake()->randomElement(['6 mois', '1 an', '2 ans']),
            'level'          => fake()->randomElement(['Débutant', 'Intermédiaire', 'Avancé']),
            'students_count' => fake()->numberBetween(5, 30),
            'price'          => fake()->randomFloat(2, 200000, 1500000),
            'image'          => null,
            'is_active'      => true,
        ];
    }
}
