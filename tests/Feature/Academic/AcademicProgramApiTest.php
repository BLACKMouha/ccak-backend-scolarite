<?php

namespace Tests\Feature\Academic;

use App\Http\Middleware\KeycloakAuthenticate;
use App\Models\AcademicProgram;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicProgramApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(KeycloakAuthenticate::class);
    }

    public function test_can_crud_academic_programs(): void
    {
        $department = Department::factory()->create();

        $payload = [
            'department_id' => $department->id,
            'name' => 'Computer Engineering',
            'level' => AcademicProgram::LEVELS[0],
            'duration_semesters' => 6,
            'total_credits_required' => 180,
            'is_active' => true,
        ];

        $createResponse = $this->postJson('/api/academic-programs', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonPath('name', 'Computer Engineering')
            ->assertJsonPath('level', AcademicProgram::LEVELS[0])
            ->assertJsonPath('department_id', $department->id);

        $programId = $createResponse->json('id');

        $this->assertDatabaseHas('academic_programs', [
            'id' => $programId,
            'level' => AcademicProgram::LEVELS[0],
        ]);

        $this->getJson('/api/academic-programs')
            ->assertOk()
            ->assertJsonCount(1);

        $this->getJson("/api/academic-programs/{$programId}")
            ->assertOk()
            ->assertJsonPath('id', $programId);

        $this->putJson("/api/academic-programs/{$programId}", [
            'level' => AcademicProgram::LEVELS[1],
        ])->assertOk()
            ->assertJsonPath('level', AcademicProgram::LEVELS[1]);

        $this->deleteJson("/api/academic-programs/{$programId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('academic_programs', [
            'id' => $programId,
        ]);
    }

    public function test_academic_program_requires_valid_level(): void
    {
        $department = Department::factory()->create();

        $this->postJson('/api/academic-programs', [
            'department_id' => $department->id,
            'name' => 'Invalid Program',
            'level' => 'PHD',
            'duration_semesters' => 6,
            'total_credits_required' => 180,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['level']);
    }
}
