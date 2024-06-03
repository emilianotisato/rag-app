<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement([DocumentType::PDF, DocumentType::WEB_PAGE]),
            'path' => $this->faker->url,
            'status' => DocumentStatus::PENDING,
            'content' => null,
            'error' => null,
            'processed_at' => null,
        ];
    }
}
