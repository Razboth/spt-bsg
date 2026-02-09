<?php
namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Services\PSA\SalesRatioService;
use App\Services\PSA\SettingPositionProductService;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SalesAverageRatioController extends Controller
{
    private SalesRatioService $salesRatioService;
    private SettingPositionProductService $settingPositionProductService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->salesRatioService             = new SalesRatioService();
        $this->settingPositionProductService = new SettingPositionProductService();

        \View::share('arrApprove', defaultStatusApproval());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $settingProducts = $this->settingPositionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        $settingId = $request->get('setting_id') ?? array_key_first($settingProducts);
        if (Auth::user()->role->name != 'Manager' && Auth::user()->role->name != 'Sales') {
            $setting = $this->settingPositionProductService->getSettingPositionProductById($settingId);
        } else {
            $position  = Auth::user()->model->position_id;
            $setting   = $this->settingPositionProductService->getSettingProductByPosition($position);
            $settingId = $setting->id;
        }

        return view('psa.sales-average-ratio.index', [
            'settingProducts' => $settingProducts,
            'setting'         => $setting,
            'settingId'       => $settingId,
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request, $id)
    {
        return $this->salesRatioService->dataAverage($request, $id);
    }
}
