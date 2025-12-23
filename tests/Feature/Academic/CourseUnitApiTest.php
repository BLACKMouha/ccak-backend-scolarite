<?php

namespace Tests\Feature\Academic;

use App\Http\Middleware\KeycloakAuthenticate;
use App\Models\AcademicProgram;
use App\Models\CourseUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseUnitApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(KeycloakAuthenticate::class);
    }

    public function test_can_crud_course_units(): void
    {
        $program = AcademicProgram::factory()->create();

        $payload = [
            'academic_program_id' => $program->id,
            'code' => 'UE101',
            'name' => 'Algorithms',
            'semester_number' => 1,
            'credits' => 6,
            'type' => CourseUnit::TYPES[0],
            'is_active' => true,
        ];

        $createResponse = $this->postJson('/api/course-units', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonPath('name', 'Algorithms')
            ->assertJsonPath('code', 'UE101')
            ->assertJsonPath('academic_program_id', $program->id);

        $unitId = $createResponse->json('id');

        $this->assertDatabaseHas('course_units', [
            'id' => $unitId,
            'code' => 'UE101',
        ]);

        $this->getJson('/api/course-units')
            ->assertOk()
            ->assertJsonCount(1);

        $this->getJson("/api/course-units/{$unitId}")
            ->assertOk()
            ->assertJsonPath('id', $unitId);

        $this->putJson("/api/course-units/{$unitId}", [
            'credits' => 9,
        ])->assertOk()
            ->assertJsonPath('credits', 9);

        $this->deleteJson("/api/course-units/{$unitId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('course_units', [
            'id' => $unitId,
        ]);
    }

    public function test_course_unit_requires_valid_type(): void
    {
        $program = AcademicProgram::factory()->create();

        $this->postJson('/api/course-units', [
            'academic_program_id' => $program->id,
            'code' => 'UE102',
            'name' => 'Invalid Type',
            'semester_number' => 1,
            'credits' => 4,
            'type' => 'ELECTIVE',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}
