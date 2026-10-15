<?php

namespace Database\Factories;

use App\Models\Download;
use App\Support\Icon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    /**
     * File APK yang sudah diunggah (file fisiknya dibuat sendiri oleh test bila perlu).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'name' => strtoupper(fake()->words(2, true)),
            'description' => fake()->sentence(4),
            'version' => fake()->numerify('#.#.#'),
            'platform' => 'android',
            'file_path' => 'downloads/'.$slug.'.apk',
            'file_name' => $slug.'.apk',
            'file_size' => fake()->numberBetween(1, 50) * 1024 * 1024,
            'external_url' => null,
            'icon' => 'smartphone',
            'color' => fake()->randomElement(Icon::COLORS),
            'is_active' => true,
        ];
    }

    /**
     * Tanpa file; pengunjung diarahkan ke tautan luar (mis. Play Store).
     */
    public function externalLink(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'external_url' => 'https://play.google.com/store/apps/details?id=id.sch.contoh',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
