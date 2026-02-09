<?php

namespace App\Http\Controllers\PSA;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ActivityDetailRequest;
use App\Http\Requests\PSA\ActivityRequest;
use App\Models\PSA\Activity;
use App\Models\PSA\ActivityDetail;
use App\Models\PSA\Type;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    private ActivityService $activityService;
    private ActivityDetailService $activityDetailService;
    private AgentService $agentService;
    private CustomerService $customerService;
    private ProductService $productService;
    private TypeService $typeService;
    private SettingPositionProductService $settingPositionProductService;
    private AppMasterDataService $appMasterDataService;
    private array $activityTypes;

    public function __construct()
    {
        $this->middleware('auth');
        $this->activityService = new ActivityService();
        $this->activityDetailService = new ActivityDetailService();
        $this->agentService = new AgentService();
        $this->typeService = new TypeService();
        $this->customerService = new CustomerService();
        $this->settingPositionProductService = new SettingPositionProductService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->productService = new ProductService();
        $this->activityTypes = [
            '1' => 'Agenda',
            '2' => 'Aktifitas Langsung',
        ];

        \View::share('activityTypes', $this->activityTypes);
        \View::share('typeActivities', Activity::ACTIVITY_TYPES);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $units = $this->appMasterDataService->getMasterForArray('PMUK');
        $sales = $this->agentService->getAgentForArray(true);
        $products = $this->productService->getProductForArray(type: 'p');
        $settingProducts = $this->settingPositionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'sales' => $sales,
            'units' => $units,
            'products' => $products,
            'settingProducts' => $settingProducts,
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->activityService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $products = [];
        $customers = $this->customerService->getCustomerForArray();
        if (\Auth::user()->role_id == 9) {
            $agent = \Auth::user()->model;
            $positionProducts = $this->settingPositionProductService->getProductsByPosition($agent->position_id);

            $products = $this->productService->getProduct()
                ->whereIn('id', $positionProducts)
                ->where('type', 'p')
                ->pluck('name', 'id')
                ->toArray();

            $customers = $this->customerService->getCustomers()
                ->when(\Auth::user()->role_id == 9, function (Builder $query) {
                    $query->where('agent_id', \Auth::user()->model_id);
                })
                ->pluck('name', 'id')
                ->toArray();
        }
        $agents = $this->agentService->getAgent()
            ->when(\Auth::user()->role->name == 'Manager', function (Builder $query) {
//                $query->where('leader_id', \Auth::user()->model_id)->orWhere('id', \Auth::user()->model_id);
                $query->where('unit_id', \Auth::user()->model_id)->orWhere('id', \Auth::user()->model->unit_id);
            })
            ->pluck('name', 'id')
            ->toArray();
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'lastNumber' => $this->activityService->getLastNumber(),
            'types' => $this->typeService->getTypeForArray(),
            'agents' => $agents,
            'customers' => $customers,
            'products' => $products,
            'subProducts' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ActivityRequest $request
     * @return RedirectResponse
     */
    public function store(ActivityRequest $request)
    {
        $id = $this->activityService->save($request);

        Alert::success('Success', 'Data berhasil disimpan');

        return redirect()->route(Str::replace('/', '.', menu_path()) . '.' . 'edit', ['activity' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $activity = $this->activityService->getActivityById($id);
        $details = $this->activityDetailService->getDetails()
            ->select('activity_details.*')
            ->join('types', 'types.id', '=', 'activity_details.type_id')
            ->where('activity_id', $id)
            ->orderBy('types.order')
            ->get();
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'activity' => $activity,
            'details' => $details,
            'types' => $this->typeService->getTypeForArray(),
            'agents' => $this->agentService->getAgentForArray(),
            'customers' => $this->customerService->getCustomerForArray(),
            'products' => $this->productService->getProductForArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ActivityRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(ActivityRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->activityService->save($request, $id);
        });
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
            $this->activityService->delete($id);
        });
    }

    public function show(string $id)
    {
        $activity = $this->activityService->getActivityById($id);
        $details = $this->activityDetailService->getDetails()
            ->select('activity_details.*')
            ->join('types', 'types.id', '=', 'activity_details.type_id')
            ->where('activity_id', $id)
            ->orderBy('types.order')
            ->get();
        return view(Str::replace('/', '.', $this->menu_path()) . '.detail', [
            'activity' => $activity,
            'details' => $details,
            'types' => $this->typeService->getTypeForArray(),
            'agents' => $this->agentService->getAgentForArray(),
            'customers' => $this->customerService->getCustomerForArray(),
            'products' => $this->productService->getProductForArray(),
        ]);
    }

    public function createDetail(string $id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-detail', [
            'types' => $this->typeService->getTypeForArray(),
            'activity' => $this->activityService->getActivityById($id),
        ]);
    }

    public function storeDetail(ActivityDetailRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->activityDetailService->save($request);
        }, true, 'edit', [$request->activity_id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function editDetail(string $id)
    {
        $detail = $this->activityDetailService->getDetailById($id);
        $activity = $this->activityService->getActivityById($detail->activity_id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-detail', [
            'activity' => $activity,
            'detail' => $detail,
            'types' => $this->typeService->getTypeForArray(),
        ]);
    }

    public function updateDetail(ActivityDetailRequest $request, string $id)
    {
        $detail = $this->activityDetailService->getDetailById($id);
        return submitDataHelper(function () use ($request, $id) {
            $this->activityDetailService->save($request, $id);
        }, true, 'edit', [$detail->activity_id]);
    }

    public function destroyDetail(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->activityDetailService->delete($id);
        });
    }

    public function showDetail(string $id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.show-detail', [
            'detail' => $this->activityDetailService->getDetailById($id),
        ]);
    }

    public function editBatch(string $id)
    {
        $activity = $this->activityService->getActivityById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-batch', [
            'activity' => $activity,
            'types' => $this->typeService->getTypeForArray(),
        ]);
    }

    public function updateBatch(Request $request, string $id)
    {
        $activity = $this->activityService->getActivityById($id);
        $closingAmount = $request->closing_amount;
        $customerBalance = $request->customer_balance;
        $accountNumber = $request->account_number;
        $cif = $request->cif;
        return submitDataHelper(function () use ($request, $id, $activity, $closingAmount, $customerBalance, $accountNumber, $cif) {
            $types = Type::get();
            foreach ($types as $type){
                //delete existing detail with type
                $activity->details()->where('type_id', $type->id)->delete();

                $request->merge(['type_id' => $type->id]);
                if($type->name != 'Closing'){
                    //unset request closing amount
                    $request->merge(['closing_amount' => null]);
                    $request->merge(['customer_balance' => null]);
                    $request->merge(['account_number' => null]);
                    $request->merge(['cif' => null]);
                }else{
                    $request->merge(['closing_amount' => $closingAmount]);
                    $request->merge(['customer_balance' => $customerBalance]);
                    $request->merge(['account_number' => $accountNumber]);
                    $request->merge(['cif' => $cif]);
                }

                $detailRequest = new ActivityDetailRequest();
                $detailRequest->merge($request->all());

                $this->activityDetailService->save($detailRequest);
            }

        }, true, 'edit', [$activity->id]);
    }


    public function products($id)
    {
        $agent = $this->agentService->getAgentById($id);
        $positionProducts = $this->settingPositionProductService->getProductsByPosition($agent->position_id);
        return $this->productService->getProduct()
            ->whereIn('id', $positionProducts)
            ->where('type', 'p')
            ->get();
    }

    public function subProducts($id)
    {
        return $this->productService->getProduct()->where('parent_id', $id)->get();
    }

    public function productBySetting($id)
    {
        $productIds = $this->settingPositionProductService->getSettingPositionProductById($id)->details->pluck('product_id');
        return $this->productService->getProduct()
            ->whereIn('id', $productIds)
            ->where('type', 'p')
            ->get();
    }

    public function salesBySetting($id)
    {
        $positions = $this->settingPositionProductService->getSettingPositionProductById($id)->positions;
        $arrPositions = explode(',', $positions);

        return $this->agentService->getAgent(isSalesOnly: true)
            ->whereIn('position_id', $arrPositions)
            ->get();
    }
}
