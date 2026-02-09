<?php

namespace App\Http\Controllers\Setting;

use App\Exports\GlobalExport;
use App\Exports\Setting\MenuChecklistExport;
use App\Http\Controllers\Controller;
use App\Models\Setting\AppModul;
use App\Services\Setting\AppModulService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MenuChecklistController extends Controller
{
    private AppModulService $appModulService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->appModulService = new AppModulService();
    }

    public function index(Request $request)
    {
        $modules = AppModul::select(['id', 'name']);
        $filterSearch = $request->get('filter') ?? '';
        if ($filterSearch)
            $modules->where('name', 'like', "%{$filterSearch}%");
        $modules = $modules->orderBy('order')->get();

        return view(Str::replace('/', '.', $this->menu_path()) . '.index', ['modules' => $modules, 'filterSearch' => $filterSearch]);
    }

    /**
     * @throws Exception
     */
    public function export(AppModul $module): BinaryFileResponse
    {
        $data = [];

        $no = 1;
        foreach ($module->subModul->sortBy('order') as $key => $subModule) {
            $data[] = [
                $no,
                $subModule->name,
            ];
            $noChild = 1;
            foreach ($subModule->appMenu as $_ => $menu) {
                $data[] = [
                    "",
                    "         ". Str::lower(numToAlpha($noChild - 1)).". " . $menu->name,
                ];
                $noChild++;
            }
            $no++;
        }

        $title = 'Menu Checklist '.$module->name.' - '.date('YmdHis');

        return Excel::download(new MenuChecklistExport([
            'data' => $data,
            'module' => $module->name,
        ]
    ), $title . '.xlsx');
    }
}
