<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TypeRequest;
use App\Models\PSA\Type;
use App\Services\PSA\TypeService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TypeController extends Controller
{
    public array $statusOption;
    private TypeService $typeService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->typeService = new TypeService();

        \View::share('statusOption', $this->statusOption);
        \View::share('activityTypeOptions', Type::ACTIVITY_TYPES);
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
        return $this->typeService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'lastOrder' => $this->typeService->lastOrder(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TypeRequest $request
     * @return JsonResponse
     */
    public function store(TypeRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->typeService->save($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $type = $this->typeService->getTypeById($id);
        $activityTypes = explode(',', $type->activity_type);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'type' => $type,
            'activityTypes' => $activityTypes ?? [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TypeRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(TypeRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->typeService->save($request, $id);
        }, true);
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
            $this->typeService->delete($id);
        });
    }
}
