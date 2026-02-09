<?php

namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ActivityRequest;
use App\Models\PSA\Activity;
use App\Models\PSA\ActivityDetail;
use App\Models\PSA\SettingPositionProduct;
use App\Models\PSA\Type;
use App\Repositories\PSA\ActivityRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityService extends Controller
{
    private ActivityRepository $activityRepository;
    private ActivityDetailService $activityDetailService;

    public function __construct()
    {
        $this->activityRepository = new ActivityRepository(
            new Activity()
        );
        $this->activityDetailService = new ActivityDetailService();
    }

    public function getActivities($columns = []): Builder
    {
        $query = $this->activityRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getActivityById(string $id): Activity
    {
        return $this->activityRepository->getById($id);
    }

    public function getActivitiesForArray(): array
    {
        return $this->getActivities(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getActivities();
            if (Auth::user()->role_id == 9) {
                $query->where('agent_id', \Auth::user()->model_id);
            }
            if (Auth::user()->role->name == 'Manager') {
                $query->whereHas('agent', function (Builder $query) {
                    $query->where('leader_id', \Auth::user()->model_id);
                });
            }

            if (Auth::user()->role->name == 'Pemimpin Cabang') {
                $query->whereHas('agent', function (Builder $query) {
                    $query->where('unit_id', \Auth::user()->model->unit_id);
                });
            }

            $filterMonth = $request->get('combo_1') ?? date('m');
            $filterYear = $request->get('combo_2') ?? date('Y');
            $filterSetting = $request->get('combo_3');
            $filterAgent = $request->get('combo_4');
            $filterProduct = $request->get('combo_5');
            if ($filterMonth != '' && $filterMonth != 'All') {
                $query->whereMonth('date', $filterMonth);
            }

            if ($filterSetting != '') {
                $settingProduct = SettingPositionProduct::find($filterSetting);
                $arrPositions = explode(",", $settingProduct->positions);
                $query->whereHas('agent', function (Builder $query) use ($arrPositions) {
                    $query->whereIn('position_id', $arrPositions);
                });
            }

            if ($filterAgent != '') {
                $query->where('agent_id', $filterAgent);
            }

            if ($filterProduct != '') {
                $query->where('product_id', $filterProduct);
            }

            if ($filterYear != '' && $filterYear != 'All') {
                $query->whereYear('date', $filterYear);
            }
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('number', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'date', 'type' => 'date'],
                ['name' => 'product_id', 'type' => 'master_relationship', 'masters' => 'product'],
                ['name' => 'agent_id', 'type' => 'master_relationship', 'masters' => 'agent'],
                ['name' => 'unit_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->agent->unit->name ?? '';
                }],
                ['name' => 'customer_id', 'type' => 'master_relationship', 'masters' => 'customer'],
                ['name' => 'recent_activity', 'type' => 'custom', 'value' => function ($row) {
                    return $row->details
                        ->whereNotNull('realization_date')
                        ->where('realization_date', '!=', '0000-00-00')
                        ->last()
                        ->typeActivity
                        ->name ?? '';
                }],
                ['name' => 'updated_at', 'type' => 'custom', 'value' => function ($row) {
                    return setDateTime($row->details->last()->updated_at ?? '');
                }],
            ], isShowRoute: true);
        }
    }

    public function save(ActivityRequest $request, string $id = ''): string
    {
        $fields = [
            'date' => resetDate($request->date),
            'activity_type' => $request->activity_type,
            'type' => $request->type,
            'number' => $request->number,
            'agent_id' => $request->agent_id,
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'sub_product_id' => $request->sub_product_id,
        ];

        if ($id != '') {
            $activity = $this->activityRepository->update($fields, $id);
        } else {
            $activity = $this->activityRepository->create($fields);

            $types = Type::orderBy('order')->get();
            foreach ($types as $type) {
                ActivityDetail::create([
                    'activity_id' => $activity->id,
                    'type_id' => $type->id,
                    'type' => 1,
                ]);
            }
        }

        return $activity->id;
    }

    public function saveActivity(ActivityRequest $request, string $id = '')
    {
        $fields = [
            'number' => $request->number,
            'agent_id' => $request->agent_id,
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
        ];

        if ($id != '') {
            $activity = $this->activityRepository->update($fields, $id);
        } else {
            $activity = $this->activityRepository->create($fields);
        }

        return $activity;
    }

    public function delete(string $id): void
    {
        $activity = $this->activityRepository->getById($id);
        $activity->details()->delete();
        $activity->delete();
    }

    public function getLastNumber(): string
    {
        return generateFormatNumber('FPA', 'activities');
    }

    public function dataApproval(Request $request): JsonResponse|null
    {
        if ($request->ajax()) {
            $query = $this->getActivities()
                ->where(function (Builder $query) {
                    $query->where('approved_status', '!=', 't')
                        ->orWhere('approved_2_status', '!=', 't');
                })
                ->whereHas('details', function (Builder $query) {
                    $query->where('realization_date', '!=', '0000-00-00');
                });
            $filter = $request->get('search')['value'];
            $filterMonth = $request->get('combo_1') ?? date('m');
            $filterYear = $request->get('combo_2') ?? date('Y');
            $filterSetting = $request->get('combo_3');
            $filterAgent = $request->get('combo_4');
            $filterProduct = $request->get('combo_5');
            if ($filterMonth != '' && $filterMonth != 'All') {
                $query->whereMonth('created_at', $filterMonth);
            }

            if (\Auth::user()->role->name == 'Manager') {
                $query->whereHas('agent', function (Builder $query) {
                    $query->where('leader_id', \Auth::user()->model_id);
                });
            }

            if (\Auth::user()->role->name == 'Pemimpin Cabang') {
                $query->whereHas('agent', function (Builder $query) {
                    $query->where('unit_id', \Auth::user()->model->unit_id);
                });
            }

            if ($filterYear != '' && $filterYear != 'All') {
                $query->whereYear('created_at', $filterYear);
            }

            if ($filterSetting != '') {
                $settingProduct = SettingPositionProduct::find($filterSetting);
                $arrPositions = explode(",", $settingProduct->positions);
                $query->whereHas('agent', function (Builder $query) use ($arrPositions) {
                    $query->whereIn('position_id', $arrPositions);
                });
            }

            if ($filterAgent != '') {
                $query->where('agent_id', $filterAgent);
            }

            if ($filterProduct != '') {
                $query->where('product_id', $filterProduct);
            }

            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('number', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'check', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    return empty($row->approved_status) || empty($row->approved_2_status) ? view('components.datatables.checkbox', [
                        'id' => $row->id,
                    ]) : null;
//                    return "-";
                }],
                ['name' => 'date', 'type' => 'date'],
                ['name' => 'agent_id', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    return $row->agent->name;
                }],
                ['name' => 'product_id', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    return $row->product->name;
                }],
                ['name' => 'customer_id', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    return $row->customer->name;
                }],
                ['name' => 'approved_status', 'type' => 'approved_status'],
                ['name' => 'approved_2_status', 'type' => 'approved_status'],
                ['name' => 'realization_date', 'type' => 'date'],
                ['name' => 'detail', 'type' => 'custom', 'value' => function ($row) {
                    return view('components.views.action', [
                        'url_slot' => route('psa.approvals.detail', ['id' => $row->id]),
                        'icon_slot' => 'fa fa-edit',
                        'isModalSlot' => false,
                    ]);
                }]
            ], isModal: true, editable: false, deleteable: false);
        }

        return null;
    }

    public function updateApproval(Request $request, string $id): void
    {
        $fields = [
            'approved_by' => $request->get('approved_by'),
            'approved_status' => $request->get('approved_status'),
            'approved_date' => resetDate($request->get('approved_date')),
            'approved_note' => $request->get('approved_note'),
        ];

        $this->activityRepository->update($fields, $id);
    }

    public function updateApproval2(Request $request, string $id): void
    {
        $fields = [
            'approved_2_by' => $request->get('approved_by'),
            'approved_2_status' => $request->get('approved_status'),
            'approved_2_date' => resetDate($request->get('approved_date')),
            'approved_2_note' => $request->get('approved_note'),
        ];

        $this->activityRepository->update($fields, $id);
    }
}
