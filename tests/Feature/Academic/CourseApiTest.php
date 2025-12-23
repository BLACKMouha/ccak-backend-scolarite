<?php

namespace Tests\Feature\Academic;

use App\Http\Middleware\KeycloakAuthenticate;
use App\Models\Course;
use App\Models\CourseUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(KeycloakAuthenticate::class);
    }

    public function test_can_crud_courses(): void
    {
        $courseUnit = CourseUnit::factory()->create();
        $prerequisite = Course::factory()->create();

        $payload = [
            'course_unit_id' => $courseUnit->id,
            'code' => 'CS101',
            'name' => 'Intro to CS',
            'credits' => 5,
            'hours_lecture' => 20,
            'hours_td' => 10,
            'hours_tp' => 0,
            'coefficient' => 1.5,
            'prerequisites' => [$prerequisite->id],
            'is_active' => true,
        ];

        $createResponse = $this->postJson('/api/courses', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonPath('name', 'Intro to CS')
            ->assertJsonPath('code', 'CS101')
            ->assertJsonPath('course_unit_id', $courseUnit->id)
            ->assertJsonPath('prerequisites.0', $prerequisite->id);

        $courseId = $createResponse->json('id');

        $this->assertDatabaseHas('courses', [
            'id' => $courseId,
            'code' => 'CS101',
        ]);

        $this->getJson('/api/courses')
            ->assertOk()
            ->assertJsonCount(2);

        $this->getJson("/api/courses/{$courseId}")
            ->assertOk()
            ->assertJsonPath('id', $courseId);

        $this->putJson("/api/courses/{$courseId}", [
            'name' => 'Intro to Computing',
            'prerequisites' => [],
        ])->assertOk()
            ->assertJsonPath('name', 'Intro to Computing');

        $this->deleteJson("/api/courses/{$courseId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('courses', [
            'id' => $courseId,
        ]);
    }

    public function test_course_requires_course_unit_and_valid_prerequisites(): void
    {
        $this->postJson('/api/courses', [
            'code' => 'CS201',
            'name' => 'Data Structures',
            'credits' => 4,
            'prerequisites' => ['not-a-uuid'],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['course_unit_id', 'prerequisites.0']);
    }
}
