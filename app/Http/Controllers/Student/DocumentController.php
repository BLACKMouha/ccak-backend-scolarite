<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Student\ReviewDocumentRequest;
use App\Http\Requests\Student\StoreDocumentRequest;
use App\Models\Admin;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('permission:documents.create')->only('store');
        $this->middleware('permission:documents.view')->only(['index', 'show']);
        $this->middleware('permission:documents.update')->only('update');
        $this->middleware('permission:documents.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $studentId): JsonResponse
    {
        try {
            $student = Student::findOrFail($studentId);
            $documents = $student->documents()->with('reviewer')->get();

            return $this->success($documents, 'Documents récupérés avec succès.');
        } catch (\Exception $e) {
            return $this->error('Étudiant non trouvé.', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request, string $studentId): JsonResponse
    {
        try {
            // Vérifier que l'étudiant existe
            $student = Student::findOrFail($studentId);

            // Générer un nom de fichier unique
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;

            // Stocker le fichier de manière sécurisée
            $path = $file->storeAs('documents', $fileName, 'local');

            // Créer l'enregistrement du document
            $document = Document::create([
                'student_id' => $student->id,
                'type' => $request->type,
                'file_path' => $path,
                'file_name' => $originalName,
                'status' => 'PENDING',
                'uploaded_at' => now(),
            ]);

            return $this->success(
                $document->load('student'),
                'Document uploadé avec succès.',
                201
            );
        } catch (\Exception $e) {
            // Supprimer le fichier en cas d'erreur
            if (isset($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            return $this->error('Erreur lors de l\'upload du document.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $studentId, string $id): JsonResponse
    {
        try {
            $student = Student::findOrFail($studentId);
            $document = $student->documents()->with('reviewer')->findOrFail($id);

            return $this->success($document, 'Document récupéré avec succès.');
        } catch (\Exception $e) {
            return $this->error('Document non trouvé.', 404);
        }
    }

    /**
     * Review the specified document.
     */
    public function review(ReviewDocumentRequest $request, string $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);

            // Vérifier que le document est en attente
            if (!$document->isPending()) {
                return $this->error('Ce document a déjà été revu.', 400);
            }

            // Obtenir ou créer l'admin
            $admin = $request->user()->getOrCreateAdmin();

            // Approuver ou rejeter selon le statut
            if ($request->status === 'APPROVED') {
                $document->approve($admin, $request->notes);
            } else {
                $document->reject($admin, $request->notes);
            }

            return $this->success(
                $document->load('student', 'reviewer'),
                'Document revu avec succès.'
            );
        } catch (\Exception $e) {
            return $this->error('Erreur lors de la revue du document.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
