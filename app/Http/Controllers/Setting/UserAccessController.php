<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\AppModul;
use App\Models\Setting\AppParameter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit', '512M');

class UserAccessController extends Controller
{
    public array $arrPermission;

    public function __construct()
    {
        $this->middleware('auth');
        $this->arrPermission = array("view" => "View", "add" => "Create", "edit" => "Edit", "delete" => "Delete", "lvl1" => "Access Lvl 1", "lvl2" => "Access Lvl 2", "lvl3" => "Access Lvl 3");

        \View::share('arrPermission', $this->arrPermission);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
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
                ->filter(function ($query) use ($filter, $isSuperadmin, $superAdminId) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                    if(!$isSuperadmin) $query->where('id', '!=', $superAdminId);
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'isModal' => false,
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('settings.user-access.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $data['moduls'] = AppModul::with(['subModul'])->orderBy('order')->get();
        $data['permissions'] = Permission::where('type', 'menu')->pluck('name')->toArray();
        $data['role'] = Role::find($id);
        $data['rolePermissions'] = $data['role']->permissions->pluck('name')->toArray();

        return view('settings.user-access.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
     */
//    public function update(Request $request, int $id)
//    {
//        DB::beginTransaction();
//
//        try {
//            $role = Role::find($id);
//            $role->permissions->where('type', 'menu')->each(function ($permission) use ($role) {
//                $role->revokePermissionTo($permission);
//            });
//            foreach ($request->input('access') as $targetModul => $menus){
//                foreach ($menus as $targetMenu => $permissions){
//                    foreach ($permissions as $permission => $value){
//                        $role->givePermissionTo("$permission $targetModul/$targetMenu");
//                    }
//                }
//            }
//
//            DB::commit();
//
//            Alert::success('Success', 'User Access berhasil diupdate');
//
//            return redirect()->route('settings.user-access.index');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            Alert::error('Error', $e->getMessage());
//
//            return redirect()->back();
//        }
//    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $role = Role::with('permissions')->find($id);

            // Bulk delete old permissions
            $oldPermissions = $role->permissions->where('type', 'menu')->pluck('id');
            $role->permissions()->detach($oldPermissions);

            $newPermissions = [];
            foreach ($request->input('access') as $targetModul => $menus) {
                foreach ($menus as $targetMenu => $permissions) {
                    foreach ($permissions as $permission => $value) {
                        $perm = Permission::where("name","$permission $targetModul/$targetMenu")->first();
                        $newPermissions[] = $perm->id;
                    }
                }
            }

            // Bulk assign new permissions
            $role->permissions()->attach($newPermissions);

            DB::commit();

            \Artisan::call('cache:clear');

            Alert::success('Success', 'User Access berhasil diupdate');

            return redirect()->route('settings.user-access.index');
        } catch (\Throwable $e) { // Consider catching more specific exceptions
            DB::rollBack();
            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }

}
