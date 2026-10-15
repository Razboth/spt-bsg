<?php

namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentRequest;
use App\Http\Requests\PSA\ProductRequest;
use App\Models\PSA\Customer;
use App\Models\PSA\Product;
use App\Repositories\PSA\AgentRepository;
use App\Models\PSA\Agent;
use App\Repositories\PSA\CustomerRepository;
use App\Services\Setting\AppParameterService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentService extends Controller
{
    private AgentRepository $agentRepository;
    private CustomerRepository $customerRepository;
    private AppParameterService $appParameterService;

    public function __construct()
    {
        $this->agentRepository = new AgentRepository(
            new Agent()
        );
        $this->customerRepository = new CustomerRepository(
            new Customer()
        );
        $this->appParameterService = new AppParameterService();
    }

    public function getAgent($columns = [], $isSalesOnly = false): Builder
    {
        $query = $this->agentRepository->query()
            ->when($isSalesOnly, function ($query) {
                $query->where('rank_id', $this->appParameterService->getParameterByCode('PS')->value);
            });

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getAgentById(?string $id): ?Agent
    {
        if (empty($id)) {
            return null;
        }

        return $this->agentRepository->getById($id);
    }

    public function getAgentForArray($isSalesOnly = false): array
    {
        return $this->getAgent(['id', 'name'])
            ->when($isSalesOnly, function ($query) {
                $query->where('rank_id', $this->appParameterService->getParameterByCode('PS')->value);
            })
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getAgent();
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterUnit = $request->get('combo_2');
            $queryFilter = function ($query) use ($filter, $filterUnit, $filterPosition) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('employee_number', 'like', '%' . $filter . '%');
                }

                if(\Auth::user()->role->name == 'Manager'){
                    $query->where('leader_id', \Auth::user()->model_id)->orWhere('id', \Auth::user()->model_id);
                }

                if (isset($filterPosition)) {
                    $query->where('rank_id', $filterPosition);
                }

                if (isset($filterUnit)) {
                    $query->where('unit_id', $filterUnit);
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'photo', 'type' => 'photo'],
                ['name' => 'unit_id', 'type' => 'master_relationship', 'masters' => 'unit'],
                ['name' => 'status_id', 'type' => 'master_relationship', 'masters' => 'status'],
                ['name' => 'position_id', 'type' => 'master_relationship', 'masters' => 'position'],
                ['name' => 'type_id', 'type' => 'master_relationship', 'masters' => 'type'],
                ['name' => 'join_date', 'type' => 'date'],
            ]);
        }
    }

     public function dataCoaching(Request $request, $editable = true, $deletable = true): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getAgent();
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $queryFilter = function ($query) use ($filter, $filterPosition) {
                if (isset($filter)) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('participants.name', 'like', '%' . $filter . '%')
                            ->orWhere('participants.code', 'like', '%' . $filter . '%');
                    });
                }
                if(isset($filterPosition)){
                    $query->where('position_id', $filterPosition);
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'city_id', 'type' => 'master_relationship', 'masters' => 'city'],
                ['name' => 'education_id', 'type' => 'master_relationship', 'masters' => 'education'],
                ['name' => 'position_id', 'type' => 'master_relationship', 'masters' => 'position'],
                ['name' => 'gender', 'type' => 'gender'],
                ['name' => 'd1', 'type' => 'custom', 'value' => function ($row) {
                    $d1 = $row->documents->first();
                    return !empty($d1->filename) ? view('components.datatables.download', [
                        'url' => $d1->filename,
                    ]) : '';
                }],
                ['name' => 'age', 'type' => 'custom', 'value' => function ($row) {
                    return $row->age;
                }],
            ], editable: $editable, deleteable: $deletable);
        }
    }

    /**
     * @throws Exception
     */
    public function dataMapping(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getAgent()->where('rank_id', $this->appParameterService->getParameterByCode('PS')->value);
            if(\Auth::user()->role->name == 'Manager'){
                $query->where('leader_id', \Auth::user()->model_id);
            }
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('employee_number', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'photo', 'type' => 'photo'],
                ['name' => 'unit_id', 'type' => 'master_relationship', 'masters' => 'unit'],
                ['name' => 'status_id', 'type' => 'master_relationship', 'masters' => 'status'],
                ['name' => 'position_id', 'type' => 'master_relationship', 'masters' => 'position'],
                ['name' => 'rank_id', 'type' => 'master_relationship', 'masters' => 'rank'],
                ['name' => 'customer', 'type' => 'custom', 'value' => function ($row) {
                    return view('components.datatables.on-click-redirect', [
                        'route' => route(Str::replace('/', '.', $this->menu_path()) . '.edit', $row->id),
                        'text' => $row->customers->count(),
                    ]);
                }]
            ], editable: false, deleteable: false);
        }
    }

    public function save(AgentRequest $request, string $id = ''): void
    {
        $fields = [
            'name' => $request->name,
            'nickname' => $request->nickname,
            'employee_number' => $request->employee_number,
            'identity_number' => $request->identity_number,
            'birth_place' => $request->birth_place,
            'birth_date' => resetDate($request->birth_date),
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address' => $request->address,
            'gender' => $request->gender,
            'status_id' => $request->status_id,
            'type_id' => $request->type_id,
            'rank_id' => $request->rank_id,
            'position_id' => $request->position_id,
            'join_date' => resetDate($request->join_date),
            'leave_date' => resetDate($request->leave_date),
            'leader_id' => $request->leader_id,
            'unit_id' => $request->unit_id,
            'activity_type' => $request->activity_type,
        ];

        if ($id != '') {
            $employee = $this->agentRepository->update($fields, $id);
        } else {
            $employee = $this->agentRepository->create($fields);
        }

        defaultUploadFile($employee, $request, Agent::FILE_PATH, 'identity_file_' . Str::slug($employee->name) . '_' . time(), "identity_file");
        defaultUploadFile($employee, $request, Agent::FILE_PATH, 'photo_' . Str::slug($employee->name) . '_' . time(), "photo");
    }

    public function delete(string $id): void
    {
        $this->agentRepository->destroy($id);
    }

    public function saveCustomer(Request $request, string $id): void
    {
        if ($request->get('checks') !== null) {
            foreach ($request->get('checks') as $key => $customerId) {
                $this->customerRepository->update([
                    'agent_id' => $id
                ], $customerId);
            }
        }
    }
}
