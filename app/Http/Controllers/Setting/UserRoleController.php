<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UserRoleRequest;
use App\Models\Setting\AppModul;
use App\Models\Setting\AppParameter;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserRoleController extends Controller
{
    public array $statusOption;

    public function __construct()
    {
        $this->middleware('auth');

        $this->statusOption = defaultStatus();

        \View::share('statusOption', $this->statusOption);
    }
    /**
     * Display a listing of the resource.
     *
     * @return ApplicationAlias|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $isSuperadmin = Auth::user()->hasRole('Superadmin');
        $superAdminId = AppParameter::whereCode('SUPERADMIN')->first()->value;
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $role = Role::select(['id', 'name', 'description']);
            if (!$isSuperadmin) $role->where('id', '!=', $superAdminId);
            return DataTables::of($role)
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('settings.user-role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return ApplicationAlias|Factory|View
     */
    public function create()
    {
        $arrModul = AppModul::orderBy('order')->pluck('name', 'target')->toArray();

        return view('settings.user-role.form', compact('arrModul'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRoleRequest $request
     * @return JsonResponse
     */
    public function store(UserRoleRequest $request)
    {
        DB::beginTransaction();

        try {
            $role = Role::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
            ]);

            $moduls = AppModul::pluck('target', 'id')->toArray();

            foreach ($moduls as $key => $value) {
                if($role->hasPermissionTo($value)) $role->revokePermissionTo($value);

                if (isset($request->input('modul')[$value])) {
                    $role->givePermissionTo($value);
                }
            }

            DB::commit();

            return response()->json([
                'success' => 'Role berhasil disimpan',
                'url' => route('settings.user-roles.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => $e->getMessage(),
                'url' => route('settings.user-roles.index')
            ]);
        }



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return ApplicationAlias|Factory|View
     */
    public function edit(int $id)
    {
        $data['role'] = Role::findOrFail($id);
        $data['arrModul'] = AppModul::orderBy('order')->pluck('name', 'target')->toArray();
        $data['moduls'] = $data['role']->permissions->where('type', 'modul')->pluck('name')->toArray();

        return view('settings.user-role.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRoleRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserRoleRequest $request, int $id)
    {

        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
            ]);

            $moduls = AppModul::pluck('target', 'id')->toArray();

            foreach ($moduls as $key => $value) {
                if($role->hasPermissionTo($value)) $role->revokePermissionTo($value);

                if (isset($request->input('modul')[$value])) {
                    $role->givePermissionTo($value);
                }
            }

            DB::commit();

            return response()->json([
                'success' => 'Role berhasil diupdate',
                'url' => route('settings.user-roles.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => $e->getMessage(),
                'url' => route('settings.user-roles.index')
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $role = Role::find($id);
        $role->delete();

        Alert::success('Success', 'Data Role berhasil dihapus!');

        return redirect()->back();
    }
}
