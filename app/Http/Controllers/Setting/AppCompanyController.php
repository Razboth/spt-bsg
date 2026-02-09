<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppCompanyRequest;
use App\Services\Setting\AppCompanyService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AppCompanyController extends Controller
{
    private AppCompanyService $appCompanyService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->appCompanyService = new AppCompanyService();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $company = $this->appCompanyService->getCompany();

        return view('settings.app-company.form', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppCompanyRequest $request
     * @return RedirectResponse
     */
    public function update(AppCompanyRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->appCompanyService->saveCompany($request);
        });
    }
}
