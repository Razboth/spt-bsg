<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMasterCategoryRequest;
use App\Http\Requests\Setting\AppMasterDataRequest;
use App\Models\Setting\AppMasterCategory;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class AppMasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(AppMasterCategory::select(['id', 'code', 'name', 'description', 'order']))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_show' => route(str_replace('/', '.', $this->menu_path()).'.show', $model->id),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('settings.app-master-data.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $categories = AppMasterCategory::pluck('name', 'id')->toArray();
        $lastOrder = AppMasterCategory::orderBy('order', 'desc')->value('order') + 1;

        return view('settings.app-master-data.form', [
            'categories' => $categories,
            'lastOrder' => $lastOrder,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppMasterCategoryRequest $request
     * @return JsonResponse
     */
    public function store(AppMasterCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $checkExist = AppMasterCategory::where('code', $request->get('code'))->first();
            if($checkExist) {
                return response()->json([
                    'success'=>'Gagal, kode sudah terpakai',
                    'url'=> route('settings.app-master-datas.index')
                ]);
            }

            AppMasterCategory::create($request->all());

            DB::commit();

            return response()->json([
                'success'=>'Kategori berhasil disimpan',
                'url'=> route('settings.app-master-datas.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'=> $e->getMessage(),
                'url'=> route('settings.app-master-datas.index')
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $data['category'] = AppMasterCategory::findOrFail($id);
        $data['categories'] = AppMasterCategory::whereNot('id', $data['category']->id)->pluck('name', 'id')->toArray();

        return view('settings.app-master-data.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppMasterCategoryRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(AppMasterCategoryRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $checkExist = AppMasterCategory::where('code', $request->get('code'))->whereNot('id', $id)->first();
            if($checkExist) {
                return response()->json([
                    'success'=>'Gagal, kode sudah terpakai',
                    'url'=> route('settings.app-master-datas.index')
                ]);
            }

            $category = AppMasterCategory::findOrFail($id);
            $category->update($request->all());

            DB::commit();

            return response()->json([
                'success'=>'Kategori berhasil diubah',
                'url'=> route('settings.app-master-datas.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'=> $e->getMessage(),
                'url'=> route('settings.app-master-datas.index')
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $category = AppMasterCategory::findOrFail($id);
            AppMasterData::where('app_master_category_code', $category->code)->delete();
            $category->delete();

            DB::commit();

            Alert::success('Kategori berhasil dihapus');

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();

            Alert::error('Kategori gagal dihapus : ' . $e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function show(Request $request, string $id)
    {
        $data['category'] = AppMasterCategory::findOrFail($id);
        $data['parent'] = AppMasterCategory::find($data['category']->parent_id);
        if($data['parent']) $data['parents'] = AppMasterData::where('app_master_category_code', $data['parent']->code)->pluck('name', 'id')->toArray();

        if($request->ajax()){
            $filterParent = $request->get('combo_1'); //GET FILTER
            $filter = $request->get('search')['value'];
            return DataTables::of(AppMasterData::whereAppMasterCategoryCode($data['category']->code)->select(['id', 'code', 'name', 'description', 'order']))
                ->filter(function ($query) use ($filter, $filterParent) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                    if (isset($filterParent)) $query->where('parent_id', $filterParent);
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route('settings.app-master-datas.edit-master', $model->id),
                        'url_destroy' => route('settings.app-master-datas.destroy-master', $model->id)
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('settings.app-master-data.detail', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function createMaster($id)
    {
        $data['category'] = AppMasterCategory::find($id);
        $data['parent'] = AppMasterCategory::find($data['category']->parent_id);
        if($data['parent']) $data['parents'] = AppMasterData::where('app_master_category_code', $data['parent']->code)->orderBy('order')->pluck('name', 'id')->toArray();
        $data['lastOrder'] = AppMasterData::whereAppMasterCategoryCode($data['category']->code)->orderBy('order', 'desc')->value('order') + 1;
        $data['lastCode'] = $data['category']->code.str_pad($data['lastOrder'], 3, "0", STR_PAD_LEFT);

        return view('settings.app-master-data.form-detail', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppMasterDataRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function storeMaster(AppMasterDataRequest $request, string $id)
    {
        DB::beginTransaction();

        $category = AppMasterCategory::find($id);

        try {
            AppMasterData::create($request->all() + ['app_master_category_code' => $category->code]);

            DB::commit();

            return response()->json([
                'success'=>'Master Data berhasil disimpan',
                'url'=> route('settings.app-master-datas.show', [$id])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'=> "Gagal :". $e->getMessage(),
                'url'=> route('settings.app-master-datas.show', [$id])
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function editMaster(string $id)
    {
        $data['master'] = AppMasterData::findOrFail($id);
        $data['category'] = AppMasterCategory::whereCode($data['master']->app_master_category_code)->first();
        $data['parent'] = AppMasterCategory::find($data['category']->parent_id);
        if($data['parent']) $data['parents'] = AppMasterData::whereAppMasterCategoryCode($data['parent']->code)->orderBy('order')->pluck('name', 'id')->toArray();

        return view('settings.app-master-data.form-detail', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppMasterDataRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateMaster(AppMasterDataRequest $request, string $id)
    {
        $master = AppMasterData::findOrFail($id);
        $category = AppMasterCategory::whereCode($master->app_master_category_code)->first();

        DB::beginTransaction();

        try {
            $master->update($request->all());

            DB::commit();

            return response()->json([
                'success'=>'Kategori berhasil diubah',
                'url'=> route('settings.app-master-datas.show', [$category->id])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'=> 'Gagal : '.$e->getMessage(),
                'url'=> route('settings.app-master-datas.show', [$category->id])
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyMaster(string $id)
    {
        $master = AppMasterData::findOrFail($id);

        DB::beginTransaction();

        try {
            $master->delete();

            DB::commit();

            Alert::success('Data master berhasil dihapus');

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();

            Alert::error('Data master gagal dihapus : ' . $e->getMessage());

            return redirect()->back();
        }
    }
}
