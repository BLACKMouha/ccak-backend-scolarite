<?php

namespace Tests\Feature\Academic;

use App\Http\Middleware\KeycloakAuthenticate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultyApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(KeycloakAuthenticate::class);
    }

    public function test_can_crud_faculties(): void
    {
        $dean = User::factory()->create([
            'user_type' => 'STAFF',
        ]);

        $payload = [
            'name' => 'Engineering',
            'code' => 'ENG',
            'dean_id' => $dean->id,
            'is_active' => true,
        ];

        $createResponse = $this->postJson('/api/v1/faculties', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonPath('name', 'Engineering')
            ->assertJsonPath('code', 'ENG')
            ->assertJsonPath('dean_id', $dean->id);

        $facultyId = $createResponse->json('id');

        $this->assertDatabaseHas('faculties', [
            'id' => $facultyId,
            'code' => 'ENG',
        ]);

        $this->getJson('/api/v1/faculties')
            ->assertOk()
            ->assertJsonCount(1);

        $this->getJson("/api/v1/faculties/{$facultyId}")
            ->assertOk()
            ->assertJsonPath('id', $facultyId);

        $updatePayload = [
            'name' => 'Engineering & Technology',
            'code' => 'ENGT',
        ];

        $this->putJson("/api/v1/faculties/{$facultyId}", $updatePayload)
            ->assertOk()
            ->assertJsonPath('name', 'Engineering & Technology')
            ->assertJsonPath('code', 'ENGT');

        $this->deleteJson("/api/v1/faculties/{$facultyId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('faculties', [
            'id' => $facultyId,
        ]);
    }

    public function test_faculty_requires_name_and_code(): void
    {
        $this->postJson('/api/v1/faculties', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'code']);
    }
}
