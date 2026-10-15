<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentBoosterRequest;
use App\Models\PSA\AgentBooster;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentBoosterService extends Controller
{
    public function getAgentBoosters($columns = []): Builder
    {
        $query = AgentBooster::query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getAgentBoosterById(string $id): AgentBooster
    {
        return AgentBooster::find($id);
    }

    public function getAgentBoosterForArray(): array
    {
        return $this->getAgentBoosters(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse|null
    {
        if ($request->ajax()) {
            $query = $this->getAgentBoosters()->with(['agent', 'product', 'customer'])
                ->when(\Auth::user()->role->name == 'Manager', function ($query){
                    $query->whereHas('agent', function ($query) {
                        $query->where('leader_id', \Auth::user()->model_id);
                    });
                })
                ->when(\Auth::user()->role->name == 'Pemimpin Cabang', function ($query){
                    $query->whereHas('agent', function ($query) {
                        $query->where('unit_id', \Auth::user()->employee()?->unit_id);
                    });
                })
                ->when(\Auth::user()->role->name == 'Sales', function ($query){
                    $query->where('agent_id', \Auth::user()->employee()?->id);
                });
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'agent_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->agent->name;
                }],
                ['name' => 'product_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->product->name;
                }],
                ['name' => 'customer_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->customer?->name;
                }],
                ['name' => 'approved_status', 'type' => 'approved_status', 'isCanApprove' => true],
                ['name' => 'month', 'type' => 'custom', 'value' => function ($row) {
                    return numToMonth($row->month)." ".$row->year;
                }],
            ], true);
        }

        return null;
    }

    public function save(AgentBoosterRequest $request, string $id = ''): void
    {
        $fields = [
            'agent_id' => $request->agent_id,
            'product_id' => $request->product_id,
            'customer_id' => $request->customer_id,
            'month' => $request->month,
            'year' => $request->year,
            'value' => $request->value,
            'description' => $request->description,
        ];

        if ($id != '') {
            $agentBooster = AgentBooster::find($id);
            $agentBooster->update($fields);
        } else {
            AgentBooster::create($fields);
        }
    }

    public function delete(string $id): void
    {
        $agentBooster = AgentBooster::find($id);
        //delete position product
        $agentBooster?->delete();
    }

    public function updateApproval(Request $request, string $id): void
    {
        $fields = [
            'approved_by' => $request->get('approved_by'),
            'approved_status' => $request->get('approved_status'),
            'approved_date' => resetDate($request->get('approved_date')),
            'approved_note' => $request->get('approved_note'),
        ];

        AgentBooster::find($id)->update($fields);
    }
}
