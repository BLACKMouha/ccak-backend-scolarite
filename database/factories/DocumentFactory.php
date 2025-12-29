<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'student_id' => $this->faker->uuid(),
            'reviewed_by' => $this->faker->uuid(),
            'file_path' => $this->faker->filePath(),
            'file_name' => $this->faker->sentence(),
            'type' => $this->faker->randomElement([
                'CNI',
                'BIRTH_CERT',
                'BAC_DIPLOMA',
                'TRANSCRIPT',
                'PHOTO',
                'MEDICAL',
            ]),
            'status' => $this->faker->randomElement([
                'PENDING',
                'APPROVED',
                'REJECTED',
            ]),
            'notes' => $this->faker->sentence(),
            'uploaded_at' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'reviewed_at' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
        ];
    }
}
