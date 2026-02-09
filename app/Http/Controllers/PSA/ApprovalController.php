<?php
namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Models\PSA\Activity;
use App\Models\PSA\ActivityDetail;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    private ActivityService $activityService;
    private AppMasterDataService $appMasterDataService;
    private AgentService $agentService;
    private ProductService $productService;
    private SettingPositionProductService $settingPositionProductService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->activityService               = new ActivityService();
        $this->appMasterDataService          = new AppMasterDataService();
        $this->agentService                  = new AgentService();
        $this->productService                = new ProductService();
        $this->settingPositionProductService = new SettingPositionProductService();

        \View::share('arrApprove', defaultStatusApproval());
        \View::share('typeActivities', Activity::ACTIVITY_TYPES);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $units           = $this->appMasterDataService->getMasterForArray('PMUK');
        $sales           = $this->agentService->getAgentForArray(true);
        $products        = $this->productService->getProductForArray(type: 'p');
        $settingProducts = $this->settingPositionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'units'           => $units,
            'sales'           => $sales,
            'products'        => $products,
            'settingProducts' => $settingProducts,
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->activityService->dataApproval($request);
    }

    public function approve(Request $request, ActivityDetail $detail)
    {
        $type = $request->get('type') ?? 1;
        if ($type == '2') {
            $detail->approved_status = $detail->approved_2_status;
            $detail->approved_date   = $detail->approved_2_date;
            $detail->approved_by     = $detail->approved_2_by;
            $detail->approved_note   = $detail->approved_2_note;
        } else if ($type == '3') {
            $detail->approved_status = $detail->approved_3_status;
            $detail->approved_date   = $detail->approved_3_date;
            $detail->approved_by     = $detail->approved_3_by;
            $detail->approved_note   = $detail->approved_3_note;
        }

        return view('psa.approvals.form', [
            'detail' => $detail,
            'type'   => $request->get('type') ?? 1,
        ]);
    }

    public function updateApprove(Request $request, Activity $activity)
    {
        return submitDataHelper(function () use ($request, $activity) {
            $activity->update([
                'approved_date'   => $request->approved_date,
                'approved_by'     => \Auth::user()->id,
                'approved_status' => $request->approved_status,
                'approved_note'   => $request->approved_note,
            ]);
        }, true);
    }

    public function updateApprove2(Request $request, Activity $activity)
    {
        return submitDataHelper(function () use ($request, $activity) {
            $activity->update([
                'approved_2_date'   => $request->approved_date,
                'approved_2_by'     => \Auth::user()->id,
                'approved_2_status' => $request->approved_status,
                'approved_2_note'   => $request->approved_note,
            ]);
        }, true);
    }

    public function updateApprove3(Request $request, Activity $activity)
    {
        return submitDataHelper(function () use ($request, $activity) {
            $activity->update([
                'approved_3_date'   => $request->approved_date,
                'approved_3_by'     => \Auth::user()->id,
                'approved_3_status' => $request->approved_status,
                'approved_3_note'   => $request->approved_note,
            ]);
        }, true);
    }

    public function approveBatch(Request $request)
    {
        $ids  = $request->ids;
        $data = explode(',', $ids);

        foreach ($data as $id) {
            $activity = Activity::find($id);
            if ($request->get('type') == 1) {
                $activity->update([
                    'approved_date'   => now(),
                    'approved_by'     => \Auth::user()->id,
                    'approved_status' => 't',
                ]);

                $details = $activity->details;
                foreach ($details as $detail) {
                    $detail->update([
                        'approved_date'   => now(),
                        'approved_by'     => \Auth::user()->id,
                        'approved_status' => 't',
                    ]);
                }

                $this->checkTotalApproved($detail, 1);
            } else {
                $activity->update([
                    'approved_2_date'   => now(),
                    'approved_2_by'     => \Auth::user()->id,
                    'approved_2_status' => 't',
                ]);

                $details = $activity->details;
                foreach ($details as $detail) {
                    $detail->update([
                        'approved_2_date'   => now(),
                        'approved_2_by'     => \Auth::user()->id,
                        'approved_2_status' => 't',
                    ]);
                }

                $this->checkTotalApproved($detail, 2);
            }
        }
    }

    public function updateDetailApprove(Request $request, ActivityDetail $detail)
    {
        return submitDataHelper(function () use ($request, $detail) {
            $detail->update([
                'approved_date'   => $request->approved_date,
                'approved_by'     => \Auth::user()->id,
                'approved_status' => $request->approved_status,
                'approved_note'   => $request->approved_note,
            ]);

            $this->checkTotalApproved($detail, 1);
        }, true, 'detail', ['id' => $detail->activity_id]);
    }

    public function updateDetailApprove2(Request $request, ActivityDetail $detail)
    {
        return submitDataHelper(function () use ($request, $detail) {
            $detail->update([
                'approved_2_date'   => $request->approved_date,
                'approved_2_by'     => \Auth::user()->id,
                'approved_2_status' => $request->approved_status,
                'approved_2_note'   => $request->approved_note,
            ]);

            $this->checkTotalApproved($detail, 2);

        }, true, 'detail', ['id' => $detail->activity_id]);
    }

    public function updateDetailApprove3(Request $request, ActivityDetail $detail)
    {
        return submitDataHelper(function () use ($request, $detail) {
            $detail->update([
                'approved_3_date'   => $request->approved_date,
                'approved_3_by'     => \Auth::user()->id,
                'approved_3_status' => $request->approved_status,
                'approved_3_note'   => $request->approved_note,
            ]);

        }, true, 'detail', ['id' => $detail->activity_id]);
    }

    public function approveDetailBatch(Request $request)
    {
        $ids  = $request->ids;
        $data = explode(',', $ids);
        $type = $request->get('type');

        foreach ($data as $id) {
            $detail = ActivityDetail::find($id);

            if ($type == 1) {
                $detail->update([
                    'approved_date'   => now(),
                    'approved_by'     => \Auth::user()->id,
                    'approved_status' => 't',
                ]);
            } else if ($type == 2) {
                $detail->update([
                    'approved_2_date'   => now(),
                    'approved_2_by'     => \Auth::user()->id,
                    'approved_2_status' => 't',
                ]);
            } else {
                $detail->update([
                    'approved_3_date'   => now(),
                    'approved_3_by'     => \Auth::user()->id,
                    'approved_3_status' => 't',
                ]);
            }
        }

        $this->checkTotalApproved($detail, $type);
    }

    private function checkTotalApproved($detail, $type)
    {
        $totalActivities = ActivityDetail::where('activity_id', $detail->activity_id)
            ->whereNotNull('realization_date')
            ->where('realization_date', '!=', '0000-00-00')
            ->count();
        $totalApproved = ActivityDetail::where('activity_id', $detail->activity_id)
            ->whereNotNull('realization_date')
            ->where('realization_date', '!=', '0000-00-00')
            ->where($type == 1 ? 'approved_status' : 'approved_2_status', 't')
            ->count();

        if ($totalActivities == $totalApproved) {
            $activity = Activity::find($detail->activity_id);

            if ($type == 1) {
                $activity->update([
                    'approved_date'   => now(),
                    'approved_by'     => \Auth::user()->id,
                    'approved_status' => 't',
                ]);
            } else if ($type == 2) {
                $activity->update([
                    'approved_2_date'   => now(),
                    'approved_2_by'     => \Auth::user()->id,
                    'approved_2_status' => 't',
                ]);
            } else {
                $activity->update([
                    'approved_3_date'   => now(),
                    'approved_3_by'     => \Auth::user()->id,
                    'approved_3_status' => 't',
                ]);
            }
        }
    }

    public function detail($id)
    {
        $activity = $this->activityService->getActivityById($id);
        return view('psa.approvals.detail', [
            'activity' => $activity,
        ]);
    }
}
