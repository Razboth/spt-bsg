<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\CustomerRequest;
use App\Imports\PSA\BoosterImport;
use App\Imports\PSA\CustomerImport;
use App\Services\PSA\CustomerService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public array $statusOption;
    private CustomerService $customerService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->customerService = new CustomerService();
        $this->appMasterDataService = new AppMasterDataService();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'categories' => $this->appMasterDataService->getMasterForArray('PMKC'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->customerService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'lastNumber' => $this->customerService->getLastNumber(),
            'categories' => $this->appMasterDataService->getMasterForArray('PMKC'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CustomerRequest $request
     * @return JsonResponse
     */
    public function store(CustomerRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->customerService->save($request);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'customer' => $customer,
            'categories' => $this->appMasterDataService->getMasterForArray('PMKC'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CustomerRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(CustomerRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->customerService->save($request, $id);
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
            $this->customerService->delete($id);
        });
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Customer',
            'url' => asset('storage/templates/format-upload-customer-new.xlsx'),
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new CustomerImport(), $request);
    }
}
