<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Academic\StoreDeliberationSessionRequest;
use Illuminate\Http\Request;
use App\Models\DeliberationSession;
use App\Services\DeliberationService;
use App\Services\MinutesGeneratorService;
use Illuminate\Http\Response;


class DeliberationSessionController extends BaseApiController
{
    public function __construct(
        protected DeliberationService $deliberationService,
        protected MinutesGeneratorService $minutesService
    ) {
        // Add middleware here if needed, specially for permissions
    }

    /**
     * Display a listing of deliberation sessions !
     *
     * @response 200 {"data": [DeliberationSessionResource]}
     */
    public function index()
    {
        return response()->json($this->deliberationService->getAll());
    }

    /**
     * Create a new deliberation session (Admin only)
     *
     * @response 201 {"data": DeliberationSessionResource}
     */
    public function store(StoreDeliberationSessionRequest $request)
    {
        $result = $this->deliberationService->create($request->validated());

        return response()->json($result, 201);
    }

    /**
     * Display the specified deliberation session.
     *
     * @response 200 {"data": DeliberationSessionResource}
     */
    public function show($id)
    {
        $result = $this->deliberationService->getById($id);
        return $result ? response()->json($result) : response()->json(['message' => 'Not found'], 404);
    }

    /**
     * Update the specified deliberation session.
     *
     * @response 200 {"data": DeliberationSessionResource}
     */
    public function update(Request $request, $id)
    {
        $result = $this->deliberationService->update($id, $request->validated());

        return $result ? response()->json($result) : response()->json(['message' => 'Not found'], 404);
    }

    /**
     * Remove the specified deliberation session.
     *
     * @response 200 {"data": null, "message": "Deliberation session deleted"}
     */
    public function destroy($id)
    {
        return $this->deliberationService->delete($id)
            ? response()->json(['message' => 'Deleted'])
            : response()->json(['message' => 'Not found'], 404);
    }

    /**
     * Start a deliberation session
     * POST /api/deliberations/{id}/start
     */
    public function start(DeliberationSession $deliberation_session)
    {
        if ($deliberation_session->status !== DeliberationSession::STATUS_SCHEDULED) {
            return $this->error('Session must be in SCHEDULED status', Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->deliberationService->startDeliberation($deliberation_session);

            return response()->json($result, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Complete a deliberation session
     * POST /api/deliberations/{id}/complete
     */
    public function complete(DeliberationSession $deliberation_session)
    {
        if ($deliberation_session->status !== DeliberationSession::STATUS_IN_PROGRESS) {
            return $this->error('Session must be in IN_PROGRESS status', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->deliberationService->completeDeliberation($deliberation_session);

            return response()->json([
                'message' => 'Deliberation session completed successfully',
                'session' => $deliberation_session->fresh(),
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get eligible students for deliberation
     * GET /api/deliberations/{id}/students
     */
    public function getStudents(DeliberationSession $deliberation_session)
    {
        $students = $this->deliberationService->fetchEligibleStudents($deliberation_session);

        return response()->json($students);
    }

    /**
     * Generate minutes for a deliberation session
     * GET /api/deliberations/{id}/minutes
     */
    public function generateMinutes(DeliberationSession $deliberation_session)
    {
        if ($deliberation_session->status !== DeliberationSession::STATUS_COMPLETED) {
            return $this->error('Session must be COMPLETED to generate minutes', Response::HTTP_BAD_REQUEST);
        }

        try {
            $path = $this->minutesService->generate($deliberation_session);

            return response()->download($path, "minutes_{$deliberation_session->id}.pdf");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
