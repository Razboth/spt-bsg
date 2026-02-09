<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Models\PSA\NplSetting;
use App\Models\PSA\Product;
use App\Models\PSA\ProductMonthlySetting;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductMonthlySettingController extends Controller
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
    public function index(Request $request)
    {
        $filterYear = $request->get('filterYear') ?? date('Y');
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'filterYear' => $filterYear,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function data(Request $request)
    {
        $settings = ProductMonthlySetting::get();
        if ($request->ajax()) {
            $query = Product::where('type','p');

            for ($i = 1; $i <= 12; $i++){
                $data[] = [
                    'name' => 'weight_' . $i,
                    'type' => 'custom',
                    'value' => function ($row) use ($i, $settings) {
                        return $settings->where('month', $i)->where('product_id', $row->id)->first()?->weight ?? '';
                    }
                ];
            }

            $data[] = [
                'name' => 'control',
                'type' => 'custom',
                'value' => function ($row) {
                    return view('components.views.action', [
                        'url_slot' => route('psa.setting-products.edit', ['id' => $row->id, 'year' => date('Y')]),
                        'isModalSlot' => true,
                        'icon_slot' => 'fas fa-edit',
                    ]);
                }
            ];

            return generateDatatable($query, [], $data, isModal: true, editable: false, deleteable: false);
        }

        return null;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @param $year
     * @return Application|Factory|View
     */
    public function edit($id, $year)
    {
        $product = Product::find($id);
        $settings = ProductMonthlySetting::where('product_id', $product->id)->where('year', $year)->get();
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'product' => $product,
            'year' => $year,
            'settings' => $settings,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @param $year
     * @return RedirectResponse
     */
    public function update(Request $request, $id, $year)
    {
        $product = Product::findOrFail($id);
        return submitDataHelper(function () use ($request, $product, $year) {
            foreach ($request->get('amount') as $k => $amount) {
                ProductMonthlySetting::updateOrCreate([
                    'product_id' => $product->id,
                    'year' => $year,
                    'month' => $k,
                ], [
                    'weight' => $amount,
                ]);
            }
        }, true);
    }
}
