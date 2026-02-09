<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ActivityDetailRequest;
use App\Http\Requests\PSA\ActivityRequest;
use App\Models\PSA\Activity;
use App\Models\PSA\ActivityDetail;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Str;

class ActivityController extends Controller
{
    private ActivityDetailService $activityDetailService;
    private ActivityService $activityService;
    public function __construct()
    {
        $this->activityDetailService = new ActivityDetailService();
        $this->activityService = new ActivityService();
    }

    public function get()
    {
        try {
            if(\Auth::check()) {
                $activities = $this->activityService->getActivities()
                    ->where('agent_id', \Auth::user()->model_id)
                    ->get();
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $activities
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getActivity(string $id)
    {
        try {
            if(\Auth::check()) {
                $activity = $this->activityService->getActivityById($id);
                $activity->load('customer', 'product', 'details', 'details.typeActivity');
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $activity
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if(\Auth::check()){
                $data = [
                    'number' => $this->activityService->getLastNumber(),
                    'agent_id' => \Auth::user()->model_id,
                    'customer_id' => $request->get('customer_id'),
                    'product_id' => $request->get('product_id'),
                ];

                $reqActivity = new ActivityRequest($data);

                $activity = $this->activityService->saveActivity($reqActivity);

                if($request->get('type') == '1') {
                    $data = [
                        'activity_id' => $activity->id,
                        'type_id' => $request->type_id,
                        'plan_date' => $request->date,
                        'plan_time' => $request->time,
                        'plan_note' => $request->note,
                    ];

                    $reqPlan = new ActivityDetailRequest($data);

                    $detail = $this->activityDetailService->save($reqPlan);

                    $picture = uploadFile(
                        $request->file('filename'),
                        Str::slug($detail->activity->customer->name).'_'.time(),
                        ActivityDetail::FILE_PATH);

                    $detail->plan_filename = $picture;
                }else{
                    $data = [
                        'activity_id' => $activity->id,
                        'type_id' => $request->type_id,
                        'realization_date' => $request->date,
                        'realization_time' => $request->time,
                        'realization_note' => $request->note,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'address' => $request->address,
                        'closing_amount' => $request->closing_amount,
                        'account_number' => $request->account_number,
                    ];

                    $reqRealization = new ActivityDetailRequest($data);

                    $detail = $this->activityDetailService->save($reqRealization);

                    $picture = uploadFile(
                        $request->file('filename'),
                        Str::slug($detail->activity->customer->name).'_'.time(),
                        ActivityDetail::FILE_PATH);

                    $detail->realization_filename = $picture;
                }
                $detail->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, string $id = '')
    {
        try {
            if(\Auth::check()){
                if($request->get('type') == '1') {
                    $data = [
                        'activity_id' => $id,
                        'type_id' => $request->type_id,
                        'plan_date' => $request->date,
                        'plan_time' => $request->time,
                        'plan_note' => $request->note,
                    ];

                    $reqPlan = new ActivityDetailRequest($data);

                    $detail = $this->activityDetailService->save($reqPlan);

                    $picture = uploadFile(
                        $request->file('filename'),
                        Str::slug($detail->activity->customer->name).'_'.time(),
                        ActivityDetail::FILE_PATH);

                    $detail->plan_filename = $picture;
                }else{
                    $data = [
                        'activity_id' => $id,
                        'type_id' => $request->type_id,
                        'realization_date' => $request->date,
                        'realization_time' => $request->time,
                        'realization_note' => $request->note,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'address' => $request->address,
                        'closing_amount' => $request->closing_amount,
                        'account_number' => $request->account_number,
                    ];

                    $reqRealization = new ActivityDetailRequest($data);

                    $detail = $this->activityDetailService->save($reqRealization);

                    $picture = uploadFile(
                        $request->file('filename'),
                        Str::slug($detail->activity->customer->name).'_'.time(),
                        ActivityDetail::FILE_PATH);

                    $detail->realization_filename = $picture;
                }
                $detail->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDetails(Request $request, string $id)
    {
        $month = $request->get('month') ?: date('m');
        $year = $request->get('year') ?: date('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        try {
            if(\Auth::check()) {
                $activities = $this->activityDetailService->getDetails()
                    ->whereHas('activity', function ($query) use ($id) {
                        $query->where('agent_id', $id);
                    })
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('plan_date', [$startDate, $endDate])
                            ->orWhereBetween('realization_date', [$startDate, $endDate]);
                    })
                    ->orderByRaw('
                        CASE
                            WHEN realization_date <> "0000-00-00" THEN realization_date
                            WHEN plan_date <> "0000-00-00" THEN plan_date
                            ELSE NULL
                        END
                    ')
//                    ->ddRawSql()
                    ->get();
                $activities->load(['typeActivity', 'activity.customer', 'activity.product']);
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $activities
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllDetails(Request $request)
    {
        try {
            if(\Auth::check()) {
                $activities = $this->activityDetailService->getDetails()
                    ->when($request->get('agent_id'), function ($query) use ($request) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('agent_id', $request->get('agent_id'));
                        });
                    })
                    ->get();
                $activities->load(['typeActivity', 'activity.customer', 'activity.product']);
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $activities
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDetail(string $id)
    {
        try {
            if(\Auth::check()) {
                $activity = $this->activityDetailService->getDetailById($id);
                $activity->load(['typeActivity', 'activity.customer', 'activity.product']);
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $activity
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storePlan(Request $request, string $id)
    {
        try {
            if(\Auth::check()){
                $data = [
                    'type_id' => $request->type_id,
                    'activity_id' => $id,
                    'plan_date' => $request->date,
                    'plan_time' => $request->time,
                    'plan_note' => $request->note,
                    'realization_date' => $request->realization_date,
                    'realization_time' => $request->realization_time,
                    'realization_note' => $request->realization_note,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address,
                    'closing_amount' => $request->closing_amount,
                    'account_number' => $request->account_number,
                ];

                $reqPlan = new ActivityDetailRequest($data);

                $detail = $this->activityDetailService->save($reqPlan);

                $picture = uploadFile(
                    $request->file('plan_filename'),
                    Str::slug($detail->activity->customer->name).'_plan_'.time(),
                    ActivityDetail::FILE_PATH);

                $pictureRealization = uploadFile(
                    $request->file('realization_filename'),
                    Str::slug($detail->activity->customer->name).'_realization_'.time(),
                    ActivityDetail::FILE_PATH);

                $detail->plan_filename = $picture;
                $detail->realization_filename = $pictureRealization;
                $detail->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updatePlan(Request $request, string $id)
    {
        try {
            if(\Auth::check()){
                $detail = $this->activityDetailService->getDetailById($id);
                $data = [
                    'type_id' => $request->type_id,
                    'activity_id' => $detail->activity_id,
                    'plan_date' => $request->date,
                    'plan_time' => $request->time,
                    'plan_note' => $request->note,
                    'realization_date' => $request->realization_date,
                    'realization_time' => $request->realization_time,
                    'realization_note' => $request->realization_note,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address,
                    'closing_amount' => $request->closing_amount,
                    'account_number' => $request->account_number,
                ];

                $reqPlan = new ActivityDetailRequest($data);

                $detail = $this->activityDetailService->save($reqPlan, $id);

                $picture = uploadFile(
                    $request->file('plan_filename'),
                    Str::slug($detail->activity->customer->name).'_'.time(),
                    ActivityDetail::FILE_PATH);

                $pictureRealization = uploadFile(
                    $request->file('realization_filename'),
                    Str::slug($detail->activity->customer->name).'_realization_'.time(),
                    ActivityDetail::FILE_PATH);

                $detail->plan_filename = $picture;
                $detail->realization_filename = $pictureRealization;
                $detail->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function storeRealization(Request $request, string $id)
    {
        try {
            if(\Auth::check()){
                $data = [
                    'type_id' => $request->type_id,
                    'activity_id' => $id,
                    'realization_date' => $request->date,
                    'realization_time' => $request->time,
                    'realization_note' => $request->note,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address,
                    'closing_amount' => $request->closing_amount,
                    'account_number' => $request->account_number,
                ];

                $reqRealization = new ActivityDetailRequest($data);

                $detail = $this->activityDetailService->save($reqRealization);

                $picture = uploadFile(
                    $request->file('realization_filename'),
                    Str::slug($detail->activity->customer->name).'_'.time(),
                    ActivityDetail::FILE_PATH);

                $detail->realization_filename = $picture;
                $detail->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateRealization(Request $request, string $id)
    {
        try {
            if(\Auth::check()){
                $detail = $this->activityDetailService->getDetailById($id);
                $data = [
                    'type_id' => $request->type_id,
                    'activity_id' => $detail->activity_id,
                    'realization_date' => $request->date,
                    'realization_time' => $request->time,
                    'realization_note' => $request->note,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address,
                    'closing_amount' => $request->closing_amount,
                    'account_number' => $request->account_number,
                ];

                $reqRealization = new ActivityDetailRequest($data);

                $detail = $this->activityDetailService->save($reqRealization, $id);

                $picture = uploadFile(
                    $request->file('realization_filename'),
                    Str::slug($detail->activity->customer->name).'_'.time(),
                    ActivityDetail::FILE_PATH);

                $detail->realization_filename = $picture;
                $detail->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete(string $id)
    {
        try {
            if(\Auth::check()){
                $this->activityDetailService->delete($id);
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function submitBatchApproval(Request $request)
    {
        try {
            if(\Auth::check()){
                if($request->get('ids') !== null){
                    foreach ($request->get('ids') as $id) {
                        $request->merge(['approved_status' => $request->get('approved_status')]);
                        $request->merge(['approved_by' => \Auth::user()->model_id]);
                        $request->merge(['approved_date' => date('d/m/Y')]);
                        $this->activityDetailService->updateApproval($request, $id);
                    }
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function submitApproval(Request $request, String $id)
    {
        try {
            if(\Auth::check()){
                $request->merge(['approved_status' => $request->get('approved_status')]);
                $request->merge(['approved_note' => $request->get('approved_note')]);
                $request->merge(['approved_by' => \Auth::user()->model_id]);
                $request->merge(['approved_date' => date('d/m/Y')]);
                $this->activityDetailService->updateApproval($request, $id);

                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
