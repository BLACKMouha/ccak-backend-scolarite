<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreDeliberationSessionRequest;
use App\Http\Requests\Academic\UpdateDeliberationSessionRequest;
use App\Http\Resources\Academic\DeliberationSessionResource;
use App\Models\DeliberationSession;
use Illuminate\Http\Request;

class DeliberationSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = DeliberationSession::query()
            ->when($request->get('academic_program_id'), fn($q, $v) => $q->where('academic_program_id', $v))
            ->when($request->get('academic_year_id'), fn($q, $v) => $q->where('academic_year_id', $v))
            ->when($request->get('status'), fn($q, $v) => $q->where('status', $v))
            ->orderByDesc('session_date')
            ->paginate(20);

        return DeliberationSessionResource::collection($sessions);
    }

    public function store(StoreDeliberationSessionRequest $request)
    {
        $session = DeliberationSession::create($request->validated());
        return new DeliberationSessionResource($session);
    }

    public function show(DeliberationSession $deliberation_session)
    {
        $deliberation_session->load(['academicProgram', 'academicYear', 'president', 'results.student']);
        return new DeliberationSessionResource($deliberation_session);
    }

    public function update(UpdateDeliberationSessionRequest $request, DeliberationSession $deliberation_session)
    {
        $deliberation_session->update($request->validated());
        return new DeliberationSessionResource($deliberation_session->refresh());
    }

    public function destroy(DeliberationSession $deliberation_session)
    {
        $deliberation_session->delete();
        return response()->noContent();
    }
}
