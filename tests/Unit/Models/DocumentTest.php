<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_belongs_to_student(): void
    {
        $student = Student::factory()->create();
        $document = Document::factory()->create(['student_id' => $student->id]);

        $this->assertInstanceOf(Student::class, $document->student);
        $this->assertEquals($student->id, $document->student->id);
    }

    public function test_document_belongs_to_reviewer(): void
    {
        $admin = Admin::factory()->create();
        $document = Document::factory()->approved()->create(['reviewed_by' => $admin->id]);

        $this->assertInstanceOf(Admin::class, $document->reviewer);
        $this->assertEquals($admin->id, $document->reviewer->id);
    }

    public function test_pending_scope(): void
    {
        Document::factory()->pending()->create();
        Document::factory()->approved()->create();

        $pendingDocuments = Document::pending()->get();

        $this->assertCount(1, $pendingDocuments);
        $this->assertEquals('PENDING', $pendingDocuments->first()->status);
    }

    public function test_approved_scope(): void
    {
        Document::factory()->pending()->create();
        Document::factory()->approved()->create();

        $approvedDocuments = Document::approved()->get();

        $this->assertCount(1, $approvedDocuments);
        $this->assertEquals('APPROVED', $approvedDocuments->first()->status);
    }

    public function test_rejected_scope(): void
    {
        Document::factory()->pending()->create();
        Document::factory()->rejected()->create();

        $rejectedDocuments = Document::rejected()->get();

        $this->assertCount(1, $rejectedDocuments);
        $this->assertEquals('REJECTED', $rejectedDocuments->first()->status);
    }

    public function test_of_type_scope(): void
    {
        Document::factory()->create(['type' => 'CNI']);
        Document::factory()->create(['type' => 'BIRTH_CERT']);

        $cniDocuments = Document::ofType('CNI')->get();

        $this->assertCount(1, $cniDocuments);
        $this->assertEquals('CNI', $cniDocuments->first()->type);
    }

    public function test_is_pending_method(): void
    {
        $pendingDoc = Document::factory()->pending()->create();
        $approvedDoc = Document::factory()->approved()->create();

        $this->assertTrue($pendingDoc->isPending());
        $this->assertFalse($approvedDoc->isPending());
    }

    public function test_is_approved_method(): void
    {
        $pendingDoc = Document::factory()->pending()->create();
        $approvedDoc = Document::factory()->approved()->create();

        $this->assertFalse($pendingDoc->isApproved());
        $this->assertTrue($approvedDoc->isApproved());
    }

    public function test_is_rejected_method(): void
    {
        $pendingDoc = Document::factory()->pending()->create();
        $rejectedDoc = Document::factory()->rejected()->create();

        $this->assertFalse($pendingDoc->isRejected());
        $this->assertTrue($rejectedDoc->isRejected());
    }

    public function test_approve_method(): void
    {
        $admin = Admin::factory()->create();
        $document = Document::factory()->pending()->create();

        $document->approve($admin, 'Approved successfully');

        $this->assertEquals('APPROVED', $document->status);
        $this->assertEquals($admin->id, $document->reviewed_by);
        $this->assertEquals('Approved successfully', $document->notes);
        $this->assertNotNull($document->reviewed_at);
    }

    public function test_reject_method(): void
    {
        $admin = Admin::factory()->create();
        $document = Document::factory()->pending()->create();

        $document->reject($admin, 'Document incomplete');

        $this->assertEquals('REJECTED', $document->status);
        $this->assertEquals($admin->id, $document->reviewed_by);
        $this->assertEquals('Document incomplete', $document->notes);
        $this->assertNotNull($document->reviewed_at);
    }

    public function test_type_labels(): void
    {
        $expectedLabels = [
            'CNI' => 'Carte Nationale d\'Identité',
            'BIRTH_CERT' => 'Acte de Naissance',
            'BAC_DIPLOMA' => 'Diplôme du Baccalauréat',
            'TRANSCRIPT' => 'Relevé de Notes',
            'PHOTO' => 'Photo d\'Identité',
            'MEDICAL' => 'Certificat Médical',
        ];

        $this->assertEquals($expectedLabels, Document::typeLabels());
    }

    public function test_type_label_attribute(): void
    {
        $document = Document::factory()->create(['type' => 'CNI']);

        $this->assertEquals('Carte Nationale d\'Identité', $document->type_label);
    }

    public function test_fillable_attributes(): void
    {
        $fillable = [
            'student_id',
            'type',
            'file_path',
            'file_name',
            'status',
            'reviewed_by',
            'notes',
            'uploaded_at',
            'reviewed_at',
        ];

        $this->assertEquals($fillable, (new Document)->getFillable());
    }

    public function test_casts(): void
    {
        $document = Document::factory()->create([
            'uploaded_at' => '2023-01-01 10:00:00',
            'reviewed_at' => '2023-01-02 10:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $document->uploaded_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->reviewed_at);
    }
}
