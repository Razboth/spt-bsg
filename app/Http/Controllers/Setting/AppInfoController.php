<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppInfoRequest;
use App\Services\Setting\AppInfoService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AppInfoController extends Controller
{
    private AppInfoService $appInfoService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->appInfoService = new AppInfoService();

        \View::share('statusOption', defaultStatus());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $info = $this->appInfoService->getInfo()->first();

        return view('settings.app-info.form', [
            'info' => $info,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppInfoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AppInfoRequest $request)
    {
        $this->appInfoService->saveInfo($request);

        return redirect()->back();
    }
}
