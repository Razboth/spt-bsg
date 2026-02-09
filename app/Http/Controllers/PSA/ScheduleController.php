<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Services\PSA\ActivityService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    private ActivityService $activityService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->activityService = new ActivityService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $activities = $this->activityService->getActivities()
            ->when(\Auth::user()->role_id == 9, function ($query){
                $query->where('agent_id', \Auth::user()->model_id);
            })
            ->with(['details'])->get();
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'activities' => $activities
        ]);
    }
}
