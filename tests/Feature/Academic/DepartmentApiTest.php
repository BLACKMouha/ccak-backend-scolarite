<?php

namespace Tests\Feature\Academic;

use App\Http\Middleware\KeycloakAuthenticate;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(KeycloakAuthenticate::class);
    }

    public function test_can_crud_departments(): void
    {
        $faculty = Faculty::factory()->create();
        $head = User::factory()->create([
            'user_type' => 'FACULTY',
        ]);

        $payload = [
            'faculty_id' => $faculty->id,
            'name' => 'Computer Science',
            'code' => 'CS',
            'head_id' => $head->id,
            'is_active' => true,
        ];

        $createResponse = $this->postJson('/api/departments', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonPath('name', 'Computer Science')
            ->assertJsonPath('code', 'CS')
            ->assertJsonPath('faculty_id', $faculty->id)
            ->assertJsonPath('head_id', $head->id);

        $departmentId = $createResponse->json('id');

        $this->assertDatabaseHas('departments', [
            'id' => $departmentId,
            'code' => 'CS',
        ]);

        $this->getJson('/api/departments')
            ->assertOk()
            ->assertJsonCount(1);

        $this->getJson("/api/departments/{$departmentId}")
            ->assertOk()
            ->assertJsonPath('id', $departmentId);

        $this->putJson("/api/departments/{$departmentId}", [
            'name' => 'Computing',
        ])->assertOk()
            ->assertJsonPath('name', 'Computing');

        $this->deleteJson("/api/departments/{$departmentId}")
            ->assertNoContent();

        $this->assertSoftDeleted('departments', [
            'id' => $departmentId,
        ]);
    }

    public function test_department_requires_faculty(): void
    {
        $this->postJson('/api/departments', [
            'name' => 'Mathematics',
            'code' => 'MATH',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['faculty_id']);
    }
}
