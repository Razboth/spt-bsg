<?php

namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentBoosterRequest;
use App\Http\Requests\PSA\ChangeCustomerRequest;
use App\Models\PSA\AgentBooster;
use App\Repositories\PSA\ChangeCustomerRepository;
use App\Models\PSA\ChangeCustomer;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChangeCustomerService extends Controller
{
    public function getChangeCustomers($columns = []): Builder
    {
        $query = ChangeCustomer::query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getChangeCustomerById(string $id): ChangeCustomer
    {
        return ChangeCustomer::find($id);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse|null
    {
        if ($request->ajax()) {
            $query = $this->getChangeCustomers();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('number', 'like', '%' . $filter . '%')->orWhereHas('customer', function ($query) use ($filter) {
                        $query->where('name', 'like', '%' . $filter . '%');
                    });
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'date', 'type' => 'date'],
                ['name' => 'customer_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->customer?->name;
                }],
                ['name' => 'from_agent_id', 'type' => 'custom', 'value' => function ($row) {
                    return view('psa.change-customers.span-agent', [
                        'agent' => $row->fromAgent->name,
                        'unit' => $row->fromUnit->name,
                    ]);
                }],
                ['name' => 'to_agent_id', 'type' => 'custom', 'value' => function ($row) {
                    return view('psa.change-customers.span-agent', [
                        'agent' => $row->toAgent->name,
                        'unit' => $row->toUnit->name,
                    ]);
                }],
                ['name' => 'approved_status', 'type' => 'approved_status', 'isCanApprove' => true],
            ], true);
        }

        return null;
    }

    public function save(ChangeCustomerRequest $request, string $id = ''): void
    {
        $fields = [
            'number' => $request->number,
            'date' => resetDate($request->date),
            'from_agent_id' => $request->from_agent_id,
            'from_unit_id' => $request->from_unit_id,
            'to_agent_id' => $request->to_agent_id,
            'to_unit_id' => $request->to_unit_id,
            'customer_id' => $request->customer_id,
            'description' => $request->description,
        ];

        if ($id != '') {
            $changeCustomer = ChangeCustomer::find($id);
            $changeCustomer->update($fields);
        } else {
            ChangeCustomer::create($fields);
        }
    }

    public function delete(string $id): void
    {
        $agentBooster = AgentBooster::find($id);
        //delete position product
        $agentBooster?->delete();
    }

    public function getLastNumber(): string
    {
        return generateFormatNumber('FPPC', 'change_customers');
    }
}
