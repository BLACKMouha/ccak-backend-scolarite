<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Academic\StoreDeliberationResultRequest;
use App\Http\Requests\Academic\UpdateDeliberationResultRequest;
use App\Http\Resources\Academic\DeliberationResultResource;
use App\Models\DeliberationResult;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DeliberationResultController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('permission:deliberation_results.view')->only(['index', 'show']);
        $this->middleware('permission:deliberation_results.create')->only('store');
        $this->middleware('permission:deliberation_results.update')->only('update');
        $this->middleware('permission:deliberation_results.delete')->only('destroy');
    }

    public function index()
    {
        $results = QueryBuilder::for(DeliberationResult::query())
            ->with(['deliberationSession'])
            ->allowedIncludes(['deliberationSession'])
            ->allowedFilters([
                AllowedFilter::exact('deliberation_session_id'),
                AllowedFilter::exact('student_id'),
                AllowedFilter::exact('decision'),
                AllowedFilter::callback('is_with_honors', function ($query, $value) {
                    $isWithHonors = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($isWithHonors === null) {
                        return;
                    }

                    $query->where('is_with_honors', $isWithHonors);
                }),
            ])
            ->allowedSorts(['created_at', 'decision'])
            ->defaultSort('-created_at')
            ->get();

        return $this->success(DeliberationResultResource::collection($results));
    }

    public function store(StoreDeliberationResultRequest $request)
    {
        $result = DeliberationResult::create($request->validated());

        return $this->success(
            new DeliberationResultResource($result->load(['deliberationSession'])),
            'Deliberation result created',
            Response::HTTP_CREATED
        );
    }

    public function show(DeliberationResult $deliberationResult)
    {
        return $this->success(
            new DeliberationResultResource($deliberationResult->load(['deliberationSession']))
        );
    }

    public function update(UpdateDeliberationResultRequest $request, DeliberationResult $deliberationResult)
    {
        $deliberationResult->update($request->validated());

        return $this->success(
            new DeliberationResultResource($deliberationResult->refresh()->load(['deliberationSession'])),
            'Deliberation result updated'
        );
    }

    public function destroy(DeliberationResult $deliberationResult)
    {
        $deliberationResult->delete();

        return $this->success(null, 'Deliberation result deleted');
    }
}
