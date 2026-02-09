<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\User;
use App\Services\Setting\AppDatasetService;
use App\Services\Setting\AppMasterDataService;
use App\Services\Setting\AppMasterTemplateService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class LogActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
        if ($request->ajax()) {
            $query = Activity::join('app_users', 'app_users.id', '=', 'app_activity_log.causer_id')
                ->select('app_activity_log.*', 'app_users.name as name');
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'event', 'type' => 'custom', 'value' => function ($row) {
                    return $row->event == 'created' ? 'Create Data' : 'Update Data';
                }],
                ['name' => 'ip', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    $encoded = json_decode($row->properties);
                    return $encoded->ip ?? '';
                }],
                ['name' => 'device', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    $encoded = json_decode($row->properties);
                    $device = $encoded->device ?? '';
                    $platform = $encoded->platform ?? '';
                    return $device . ' - ' . $platform;
                }],
                ['name' => 'browser', 'type' => 'custom', 'isAdd' => true, 'value' => function ($row) {
                    $encoded = json_decode($row->properties);
                    return $encoded->browser ?? '';
                }],
                ['name' => 'created_at', 'type' => 'datetime'],
                ['name' => 'detail', 'type' => 'custom', 'value' => function ($row) {
                    return \view('components.views.action', [
                        'icon_slot' => 'fa fa-solid fa-magnifying-glass',
                        'url_slot' => route('settings.log-activities.detail', $row->id),
                        'isModalSlot' => true,
                    ]);
                }]
            ], editable: false, deleteable: false);
        }

        return null;
    }

    public function detail(Activity $activity)
    {
        $user = User::find($activity->causer_id);
        $encoded = json_decode($activity->properties);
        $activity->ip = $encoded->ip ?? '';
        $device = $encoded->device ?? '';
        $platform = $encoded->platform ?? '';
        $browser = $encoded->browser ?? '';
        $activity->access_info = $device . ' - ' . $platform . ' - ' . $browser;
        $activity->event = $activity->event == 'created' ? 'Create Data' : 'Update Data';
        $activity->before = isset($encoded->old)
            ? json_encode($encoded->old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : '-';

        $activity->after = isset($encoded->attributes)
            ? json_encode($encoded->attributes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : '-';

        return view('settings.log-activities.detail', [
            'activity' => $activity,
            'user' => $user,
        ]);
    }
}
