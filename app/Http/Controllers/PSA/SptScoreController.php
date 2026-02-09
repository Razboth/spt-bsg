<?php
namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\SptScoreRequest;
use App\Models\PSA\SptScore;
use App\Services\PSA\SettingPositionProductService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SptScoreController extends Controller
{
    private SettingPositionProductService $positionProductService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->positionProductService = app(SettingPositionProductService::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $settingPositions = $this->positionProductService->getSettingPositionProducts()
            ->orderBy('name')
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        $filterPosition = $request->get('filterPosition', array_key_first($settingPositions));
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'settingPositions' => $settingPositions,
            'filterPosition'   => $filterPosition,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function data(Request $request, $filterPosition)
    {
        if ($request->ajax()) {
            $query = SptScore::where('setting_id', $filterPosition);

            return generateDatatable($query, [], [
                ['name' => 'minimum_value', 'type' => 'custom', 'value' => function ($row) {
                    if (empty($row->minimum_value)) {
                        return '>= ' . $row->maximum_value . " %";
                    } else if (empty($row->maximum_value)) {
                        return '< ' . $row->minimum_value . " %";
                    } else {
                        return $row->minimum_value . " % - < " . $row->maximum_value . " %";
                    }
                }],
            ], isModal: true);
        }

        return null;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'filterPosition' => $request->get('id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SptScoreRequest $request
     * @return JsonResponse
     */
    public function store(SptScoreRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            SptScore::create([
                'setting_id'   => $request->setting_id,
                'score'         => $request->score,
                'minimum_value' => $request->minimum_value,
                'maximum_value' => $request->maximum_value,
                'note'          => $request->note,
            ]);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $score = SptScore::findOrFail($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'score' => $score,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SptScoreRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(SptScoreRequest $request, $id)
    {
        $score = SptScore::findOrFail($id);
        return submitDataHelper(function () use ($request, $score) {
            $score->update([
                'score'         => $request->score,
                'minimum_value' => $request->minimum_value,
                'maximum_value' => $request->maximum_value,
                'note'          => $request->note,
            ]);
        }, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SptScore $score
     * @return RedirectResponse
     */
    public function destroy(SptScore $score)
    {
        return deleteDataHelper(function () use ($score) {
            $score->delete();
        });
    }
}
