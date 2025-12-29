<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {


        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'student_number' => $this->faker->unique()->numerify('STU#######'),
            'full_name' => $this->faker->name(),
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'place_of_birth' => $this->faker->city(),
            'nationality' => $this->faker->countryCode(),
            'phone' => $this->faker->phoneNumber(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'photo_url' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(Student::getStatuses()),
        ];
    }
}
