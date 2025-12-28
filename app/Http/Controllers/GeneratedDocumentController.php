<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use App\Http\Requests\GeneratedDocument\StoreGeneratedDocumentRequest;
use App\Http\Requests\GeneratedDocument\UpdateGeneratedDocumentRequest;
use App\Http\Resources\GeneratedDocumentResource;
use App\Http\Resources\GeneratedDocumentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;

class GeneratedDocumentController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('permission:document_requests.view')->only(['index', 'show']);
        $this->middleware('permission:document_requests.create')->only('store');
        $this->middleware('permission:document_requests.update')->only('update');
        $this->middleware('permission:document_requests.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $generatedDocuments = QueryBuilder::for(GeneratedDocument::query())
            ->with(['student', 'generator'])
            ->allowedIncludes(['student', 'generator'])
            ->allowedFilters(['type', 'status', 'student_id', 'generated_by'])
            ->allowedSorts(['generated_at', 'issued_at', 'created_at'])
            ->defaultSort('-generated_at')
            ->get();

        return $this->success(new GeneratedDocumentCollection($generatedDocuments));
    }

    public function store(StoreGeneratedDocumentRequest $request): JsonResponse
    {
        //dd($request->validated());
        $generatedDocument = GeneratedDocument::create($request->validated());
        // Log pour vérifier ce qui a réellement été enregistré
        Log::info('GeneratedDocument created', $generatedDocument->toArray());
        return $this->success($generatedDocument->load(['student', 'generator']), 'Generated document', Response::HTTP_CREATED);
    }

    public function show(GeneratedDocument $generatedDocument)
    {
        return $this->success($generatedDocument->load(['student', 'generator']));
    }

    public function update(UpdateGeneratedDocumentRequest $request, GeneratedDocument $generatedDocument): JsonResponse
    {
        $generatedDocument->update($request->validated());
        return $this->success($generatedDocument->refresh()->load(['student', 'generator']));
    }

    public function destroy(GeneratedDocument $generatedDocument): JsonResponse
    {
        $generatedDocument->delete();
        return $this->success();
    }

    public function verify(string $documentNumber)
    {
        $document = GeneratedDocument::where('document_number', $documentNumber)->firstOrFail();
        return $this->success($document, 'Document verified successfully');
    }
}
