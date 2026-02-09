<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Models\PSA\Agent;
use App\Models\PSA\SettingBoom;
use App\Services\PSA\AgentService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BoomSettingController extends Controller
{
    private AgentService $agentService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->agentService = new AgentService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $filterYear = $request->get('filterYear') ?? date('Y');
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'filterYear' => $filterYear,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function data(Request $request)
    {
        $settings = SettingBoom::get();
        if ($request->ajax()) {
            $query = $this->agentService->getAgent(isSalesOnly: true);

            $filter = $request->get('search')['value'];

            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            for ($i = 1; $i <= 12; $i++){
                $data[] = [
                    'name' => 'target_' . $i,
                    'type' => 'custom',
                    'value' => function ($row) use ($i, $settings) {
                        return $settings->where('month', $i)->where('agent_id', $row->id)->first()?->target ?? '';
                    }
                ];

                $data[] = [
                    'name' => 'realization_' . $i,
                    'type' => 'custom',
                    'value' => function ($row) use ($i, $settings) {
                        return $settings->where('month', $i)->where('agent_id', $row->id)->first()?->realization ?? '';
                    }
                ];
            }

            $data[] = [
                'name' => 'control',
                'type' => 'custom',
                'value' => function ($row) {
                    return view('components.views.action', [
                        'url_slot' => route('psa.setting-boom.edit', ['id' => $row->id, 'year' => date('Y')]),
                        'isModalSlot' => true,
                        'icon_slot' => 'fas fa-edit',
                    ]);
                }
            ];

            return generateDatatable($query, $queryFilter, $data, isModal: true, editable: false, deleteable: false);
        }

        return null;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @param $year
     * @return Application|Factory|View
     */
    public function edit($id, $year)
    {
        $agent = Agent::find($id);
        $settings = SettingBoom::where('agent_id', $agent->id)->where('year', $year)->get();
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'agent' => $agent,
            'year' => $year,
            'settings' => $settings,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @param $year
     * @return RedirectResponse
     */
    public function update(Request $request, $id, $year)
    {
        $agent = Agent::findOrFail($id);
        return submitDataHelper(function () use ($request, $agent, $year) {
            foreach ($request->get('amount') as $k => $amount) {
                SettingBoom::updateOrCreate([
                    'agent_id' => $agent->id,
                    'year' => $year,
                    'month' => $k,
                ], [
                    'target' => $amount['target'],
                    'realization' => $amount['realization'],
                ]);
            }
        }, true);
    }
}
