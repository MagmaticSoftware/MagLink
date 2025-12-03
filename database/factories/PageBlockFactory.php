<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageBlock>
 */
class PageBlockFactory extends Factory
{
    /**
     * Counter per tracciare la posizione y per ogni pagina
     */
    protected static array $pagePositions = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['text', 'image', 'video', 'html', 'link']);
        
        return [
            'page_id' => Page::inRandomOrder()->first()->id ?? 1,
            'type' => $type,
            'title' => $this->faker->sentence(4),
            'content' => $this->getContentForType($type),
            'position' => ['x' => 0, 'y' => 0], // Sarà impostato dal seeder
            'size' => ['width' => $this->faker->randomElement([1, 2]), 'height' => $this->faker->numberBetween(2, 4)],
            'style' => ['color' => $this->faker->hexColor, 'font_size' => $this->faker->numberBetween(14, 18)],
            'settings' => ['align' => $this->faker->randomElement(['left', 'center', 'right'])],
            'is_active' => true, // Default attivo per i blocchi di test
        ];
    }

    /**
     * Genera contenuto appropriato per il tipo di blocco
     */
    protected function getContentForType(string $type): string
    {
        return match ($type) {
            'text' => $this->faker->paragraph(3),
            'html' => '<p>' . $this->faker->paragraph(2) . '</p>',
            'image' => json_encode(['url' => 'https://picsum.photos/seed/' . $this->faker->word . '/800/600']),
            'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'link' => $this->faker->url,
            default => $this->faker->paragraph,
        };
    }

    /**
     * Resetta i contatori delle posizioni
     */
    public static function resetPositions(): void
    {
        self::$pagePositions = [];
    }
}
