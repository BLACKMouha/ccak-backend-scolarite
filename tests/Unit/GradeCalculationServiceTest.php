<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Services\GradeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private GradeCalculationService $service;
    private Student $student;
    private Course $course;
    private CourseEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GradeCalculationService();

        // Create test data
        $this->student = Student::create([
            'student_number' => 'STU001',
            'full_name' => 'Test Student',
        ]);

        $this->course = Course::create([
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
            'credits' => 3,
            'coefficient' => 2,
            'is_active' => true,
        ]);

        $this->enrollment = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    /** @test */
    public function it_calculates_course_average_with_weighted_grades()
    {
        // Create grades with different weights
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'CC',
            'score' => 15,
            'max_score' => 20,
            'weight' => 0.3,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 16,
            'max_score' => 20,
            'weight' => 0.5,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'TP',
            'score' => 18,
            'max_score' => 20,
            'weight' => 0.2,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->calculateCourseAverage($this->enrollment->id);

        // Expected: (15 * 0.3) + (16 * 0.5) + (18 * 0.2) = 4.5 + 8 + 3.6 = 16.1
        $this->assertEquals(16.1, $result['average']);
        $this->assertEquals(1.0, $result['total_weight']);
        $this->assertTrue($result['is_complete']);
        $this->assertCount(3, $result['grades_breakdown']);
    }

    /** @test */
    public function it_normalizes_scores_to_20_scale()
    {
        // Create a grade with max_score different from 20
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 80,
            'max_score' => 100,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->calculateCourseAverage($this->enrollment->id);

        // Expected: (80/100) * 20 = 16
        $this->assertEquals(16.0, $result['average']);
    }

    /** @test */
    public function it_returns_null_when_no_published_grades_exist()
    {
        // Create a draft grade
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'CC',
            'score' => 15,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'DRAFT',
        ]);

        $result = $this->service->calculateCourseAverage($this->enrollment->id);

        $this->assertNull($result['average']);
        $this->assertFalse($result['is_complete']);
    }

    /** @test */
    public function it_marks_incomplete_when_weights_dont_sum_to_one()
    {
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'CC',
            'score' => 15,
            'max_score' => 20,
            'weight' => 0.3,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->calculateCourseAverage($this->enrollment->id);

        $this->assertFalse($result['is_complete']);
        $this->assertEquals(0.3, $result['total_weight']);
    }

    /** @test */
    public function it_calculates_semester_average_with_course_coefficients()
    {
        // Create second course
        $course2 = Course::create([
            'code' => 'CS102',
            'name' => 'Data Structures',
            'credits' => 3,
            'coefficient' => 3,
            'is_active' => true,
        ]);

        $enrollment2 = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
        ]);

        // Course 1: Average 15/20, Coefficient 2
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 15,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        // Course 2: Average 12/20, Coefficient 3
        Grade::create([
            'course_enrollment_id' => $enrollment2->id,
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
            'type' => 'EXAM',
            'score' => 12,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->calculateSemesterAverage(
            $this->student->id,
            [$this->course->id, $course2->id]
        );

        // Expected: (15 * 2 + 12 * 3) / (2 + 3) = (30 + 36) / 5 = 13.2
        $this->assertEquals(13.2, $result['semester_average']);
        $this->assertEquals(5, $result['total_credits']);
        $this->assertCount(2, $result['courses']);
    }

    /** @test */
    public function it_calculates_gpa_on_4_scale()
    {
        $testCases = [
            ['average' => 17, 'expected_gpa' => 4.0, 'expected_letter' => 'A'],
            ['average' => 15, 'expected_gpa' => 3.5, 'expected_letter' => 'B+'],
            ['average' => 13, 'expected_gpa' => 3.0, 'expected_letter' => 'B'],
            ['average' => 11.5, 'expected_gpa' => 2.5, 'expected_letter' => 'C+'],
            ['average' => 10, 'expected_gpa' => 2.0, 'expected_letter' => 'C'],
            ['average' => 9, 'expected_gpa' => 1.0, 'expected_letter' => 'D'],
            ['average' => 7, 'expected_gpa' => 0.0, 'expected_letter' => 'F'],
        ];

        foreach ($testCases as $case) {
            $result = $this->service->calculateGPA($case['average']);

            $this->assertEquals($case['expected_gpa'], $result['gpa']);
            $this->assertEquals($case['expected_letter'], $result['letter_grade']);
        }
    }

    /** @test */
    public function it_applies_compensation_rules_correctly()
    {
        // Create 3 courses
        $course2 = Course::create([
            'code' => 'CS102',
            'name' => 'Math',
            'credits' => 3,
            'coefficient' => 2,
            'is_active' => true,
        ]);

        $course3 = Course::create([
            'code' => 'CS103',
            'name' => 'Physics',
            'credits' => 3,
            'coefficient' => 2,
            'is_active' => true,
        ]);

        $enrollment2 = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
        ]);

        $enrollment3 = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course3->id,
        ]);

        // Course 1: 14/20 (Pass)
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 14,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        // Course 2: 9/20 (Compensable - between 8 and 10)
        Grade::create([
            'course_enrollment_id' => $enrollment2->id,
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
            'type' => 'EXAM',
            'score' => 9,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        // Course 3: 7/20 (Not compensable - below 8)
        Grade::create([
            'course_enrollment_id' => $enrollment3->id,
            'student_id' => $this->student->id,
            'course_id' => $course3->id,
            'type' => 'EXAM',
            'score' => 7,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->applyCompensationRules(
            $this->student->id,
            [$this->course->id, $course2->id, $course3->id]
        );

        // Semester average: (14 + 9 + 7) / 3 = 10
        $this->assertEquals(10.0, $result['semester_average']);
        $this->assertTrue($result['can_compensate']);
        $this->assertCount(1, $result['compensated_courses']);
        $this->assertCount(1, $result['failed_courses']);

        // Course 2 should be compensated
        $this->assertEquals('CS102', $result['compensated_courses'][0]['course_code']);

        // Course 3 should fail
        $this->assertEquals('CS103', $result['failed_courses'][0]['course_code']);
    }

    /** @test */
    public function it_does_not_compensate_when_semester_average_is_below_10()
    {
        $course2 = Course::create([
            'code' => 'CS102',
            'name' => 'Math',
            'credits' => 3,
            'coefficient' => 1,
            'is_active' => true,
        ]);

        $enrollment2 = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
        ]);

        // Course 1: 8/20
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 8,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        // Course 2: 9/20
        Grade::create([
            'course_enrollment_id' => $enrollment2->id,
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
            'type' => 'EXAM',
            'score' => 9,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->applyCompensationRules(
            $this->student->id,
            [$this->course->id, $course2->id]
        );

        // Semester average: (8 + 9) / 2 = 8.5 (below 10)
        $this->assertEquals(8.5, $result['semester_average']);
        $this->assertFalse($result['can_compensate']);
        $this->assertCount(0, $result['compensated_courses']);
        $this->assertCount(2, $result['failed_courses']);
    }

    /** @test */
    public function it_determines_pass_fail_for_single_course()
    {
        // Passing grade
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 12,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->determinePassFail(
            $this->student->id,
            $this->enrollment->id
        );

        $this->assertEquals('course', $result['type']);
        $this->assertEquals('PASSED', $result['status']);
        $this->assertTrue($result['passed']);
        $this->assertEquals(12.0, $result['average']);
    }

    /** @test */
    public function it_determines_fail_for_course_below_10()
    {
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 8,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->determinePassFail(
            $this->student->id,
            $this->enrollment->id
        );

        $this->assertEquals('course', $result['type']);
        $this->assertEquals('FAILED', $result['status']);
        $this->assertFalse($result['passed']);
    }

    /** @test */
    public function it_determines_semester_pass_fail_with_compensation()
    {
        $course2 = Course::create([
            'code' => 'CS102',
            'name' => 'Math',
            'credits' => 3,
            'coefficient' => 1,
            'is_active' => true,
        ]);

        $enrollment2 = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
        ]);

        // Course 1: 12/20 (Pass)
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 12,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        // Course 2: 9/20 (Compensable)
        Grade::create([
            'course_enrollment_id' => $enrollment2->id,
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
            'type' => 'EXAM',
            'score' => 9,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->determinePassFail(
            $this->student->id,
            null,
            [$this->course->id, $course2->id]
        );

        // Semester average: (12 + 9) / 2 = 10.5 (Pass with compensation)
        $this->assertEquals('semester', $result['type']);
        $this->assertEquals('PASSED', $result['status']);
        $this->assertTrue($result['passed']);
        $this->assertTrue($result['compensation_applied']);
        $this->assertCount(1, $result['compensated_courses']);
    }

    /** @test */
    public function it_generates_complete_student_grade_report()
    {
        $course2 = Course::create([
            'code' => 'CS102',
            'name' => 'Math',
            'credits' => 3,
            'coefficient' => 2,
            'is_active' => true,
        ]);

        $enrollment2 = CourseEnrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
        ]);

        // Course 1: 15/20
        Grade::create([
            'course_enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'type' => 'EXAM',
            'score' => 15,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        // Course 2: 12/20
        Grade::create([
            'course_enrollment_id' => $enrollment2->id,
            'student_id' => $this->student->id,
            'course_id' => $course2->id,
            'type' => 'EXAM',
            'score' => 12,
            'max_score' => 20,
            'weight' => 1.0,
            'entered_by' => $this->student->id,
            'status' => 'PUBLISHED',
        ]);

        $result = $this->service->getStudentGradeReport(
            $this->student->id,
            [$this->course->id, $course2->id]
        );

        $this->assertArrayHasKey('student_id', $result);
        $this->assertArrayHasKey('semester_average', $result);
        $this->assertArrayHasKey('gpa', $result);
        $this->assertArrayHasKey('total_credits', $result);
        $this->assertArrayHasKey('courses', $result);
        $this->assertArrayHasKey('compensation', $result);
        $this->assertArrayHasKey('overall_status', $result);
        $this->assertArrayHasKey('passed', $result);

        // Semester average: (15 * 2 + 12 * 2) / 4 = 13.5
        $this->assertEquals(13.5, $result['semester_average']);
        $this->assertEquals(3.0, $result['gpa']['gpa']);
        $this->assertEquals('B', $result['gpa']['letter_grade']);
        $this->assertEquals('PASSED', $result['overall_status']);
        $this->assertTrue($result['passed']);
    }

    /** @test */
    public function it_handles_incomplete_grades_in_course_average()
    {
        $result = $this->service->calculateCourseAverage($this->enrollment->id);

        $this->assertNull($result['average']);
        $this->assertEquals(0, $result['total_weight']);
        $this->assertFalse($result['is_complete']);
        $this->assertEmpty($result['grades_breakdown']);
    }
}
