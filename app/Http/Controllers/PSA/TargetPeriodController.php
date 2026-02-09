<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetPeriodRequest;
use App\Services\PSA\ProductService;
use App\Services\PSA\TargetPeriodService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TargetPeriodController extends Controller
{
    private TargetPeriodService $targetPeriodService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->targetPeriodService = new TargetPeriodService();

        \View::share('arrApprove', defaultStatusApproval());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->targetPeriodService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TargetPeriodRequest $request
     * @return JsonResponse
     */
    public function store(TargetPeriodRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->targetPeriodService->save($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $period = $this->targetPeriodService->getTargetPeriodById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'period' => $period,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TargetPeriodRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(TargetPeriodRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->targetPeriodService->save($request, $id);
        }, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->targetPeriodService->delete($id);
        });
    }

    public function approve($id)
    {
        $data['data'] = $this->targetPeriodService->getTargetPeriodById($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()) . '.update-approve', [$id]);
        $data['title'] = 'Approve Booster';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, $id)
    {
        $targetPeriod = $this->targetPeriodService->getTargetPeriodById($id);
        return submitDataHelper(function () use ($request, $targetPeriod) {
            $targetPeriod->update([
                'approved_date' => resetDate($request->approved_date),
                'approved_by' => \Auth::user()->id,
                'approved_status' => $request->approved_status,
                'approved_note' => $request->approved_note,
            ]);
        }, true);
    }
}
