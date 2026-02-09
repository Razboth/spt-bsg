<?php

namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ActivityDetailRequest;
use App\Http\Requests\PSA\ActivityRequest;
use App\Models\PSA\Activity;
use App\Models\PSA\ActivityDetail;
use App\Models\PSA\Agent;
use App\Models\PSA\SalesRatio;
use App\Models\PSA\TargetAgent;
use App\Models\PSA\TargetAgentDetail;
use App\Models\PSA\TargetUnit;
use App\Repositories\PSA\ActivityDetailRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;

class ActivityDetailService extends Controller
{
    private ActivityDetailRepository $activityDetailRepository;
    private TargetAgentDetailService $targetAgentDetailService;
    private TargetPeriodService $targetPeriodService;

    public function __construct()
    {
        $this->activityDetailRepository = new ActivityDetailRepository(
            new ActivityDetail()
        );
        $this->targetAgentDetailService = new TargetAgentDetailService();
        $this->targetPeriodService = new TargetPeriodService();
    }

    public function getDetails($columns = []): Builder
    {
        $query = $this->activityDetailRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getDetailById(string $id): ActivityDetail
    {
        return $this->activityDetailRepository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getDetails();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('number', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
//                ['name' => 'filename', 'type' => 'filename'],
                ['name' => 'product_id', 'type' => 'master_relationship', 'masters' => 'product'],
                ['name' => 'agent_id', 'type' => 'master_relationship', 'masters' => 'agent'],
                ['name' => 'customer_id', 'type' => 'master_relationship', 'masters' => 'customer'],
//                ['name' => 'type_id', 'type' => 'master_relationship', 'masters' => 'type'],
//                ['name' => 'date', 'type' => 'date'],
//                ['name' => 'time', 'type' => 'custom', 'value' => function ($row) {
//                    return setDateTime($row->date." ".$row->time);
//                }],
            ]);
        }
    }

    public function save(ActivityDetailRequest $request, string $id = ''): ActivityDetail|null
    {
        $fields = [
            'type' => $request->type == null ? !empty($request->realization_date) ? 2 : 1 : $request->type,
            'activity_id' => $request->activity_id,
            'type_id' => $request->type_id,
            'plan_date' => resetDate($request->plan_date),
            'plan_time' => $request->plan_time,
            'plan_note' => $request->plan_note,
            'realization_date' => resetDate($request->realization_date),
            'realization_time' => $request->realization_time,
            'realization_note' => $request->realization_note,
            'realization_filename' => $request->realization_filename,
            'closing_amount' => resetCurrency($request->closing_amount),
            'customer_balance' => resetCurrency($request->customer_balance),
            'account_number' => $request->account_number,
            'cif' => $request->cif,
            'approved_by' => $request->approved_by,
            'approved_status' => $request->approved_status,
            'approved_date' => resetDate($request->approved_date),
            'approved_note' => $request->approved_note,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ];

        if ($id != '') {
            $detail = $this->activityDetailRepository->update($fields, $id);
        } else {
            $detail = $this->activityDetailRepository->create($fields);
        }

        defaultUploadFile($detail, $request, ActivityDetail::FILE_PATH, 'plan_file_' . Str::slug($detail->activity->customer->name) . '_' . time(), 'plan_filename');
        defaultUploadFile($detail, $request, ActivityDetail::FILE_PATH, 'plan_document_' . Str::slug($detail->activity->customer->name) . '_' . time(), 'plan_document');
        defaultUploadFile($detail, $request, ActivityDetail::FILE_PATH, 'realization_file_' . Str::slug($detail->activity->customer->name) . '_' . time(), 'realization_filename');
        defaultUploadFile($detail, $request, ActivityDetail::FILE_PATH, 'realization_document_' . Str::slug($detail->activity->customer->name) . '_' . time(), 'realization_document');

        $detail->activity->update([
            'approved_status' => 'p',
            'approved_date' => null,
            'approved_by' => null,

            'approved_2_status' => 'p',
            'approved_2_date' => null,
            'approved_2_by' => null,
        ]);

        return $detail;
    }

    public function delete(string $id): void
    {
        $this->activityDetailRepository->destroy($id);
    }

    public function updateApproval(Request $request, string $id): void
    {
        $fields = [
            'approved_by' => $request->get('approved_by'),
            'approved_status' => $request->get('approved_status'),
            'approved_date' => resetDate($request->get('approved_date')),
            'approved_note' => $request->get('approved_note'),
        ];

        $this->activityDetailRepository->update($fields, $id);
    }

    public function updateApproval2(Request $request, string $id): void
    {
        $fields = [
            'approved_2_by' => $request->get('approved_by'),
            'approved_2_status' => $request->get('approved_status'),
            'approved_2_date' => resetDate($request->get('approved_date')),
            'approved_2_note' => $request->get('approved_note'),
        ];

        $this->activityDetailRepository->update($fields, $id);
    }
}
