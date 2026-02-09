@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        @if (Auth::user()->role->name != 'Sales' && Auth::user()->role->name != 'Manager')
                            <x-form.select name="setting_id" :value="$settingId" :datas="$settingProducts" class="w-250px w-md-250px"
                                event="document.getElementById('form-filter').submit()" />
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <x-views.filter-month-year name-month="filter_1" value-month="{{ date('m') }}" name-year="filter_2"
                        value-year="{{ date('Y') }}" />
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables"
                    class="table table-rounded table-row-bordered custom-border-bottom border border-white gy-5 gs-7">
                    <thead class="border border-white">
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                            <th rowspan="2" class="min-w-50px text-center border-end ps-2">No</th>
                            @if (Auth::user()->role->name != 'Sales')
                                <th rowspan="2" class="min-w-250px w-100 text-center border-end">Sales</th>
                            @endif
                            <th rowspan="2" class="min-w-200px text-center border-end">Customer</th>
                            <th colspan="{{ $setting->details->count() }}" class="min-w-250px text-center border-end">
                                Average Sales Activity Ratio</th>
                            <th rowspan="2" class="min-w-150px text-center border-end">Total Average</th>
                        </tr>
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                            @foreach ($setting->details as $detail)
                                <th class="w-250px min-w-125px text-center border-end">{{ $detail->product->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path) . '.data', $settingId);
                    if (Auth::user()->role->name != 'Sales') {
                        $datas = ['name', 'customer_name'];
                    } else {
                        $datas = ['customer_name'];
                    }

                    foreach ($setting->details as $detail) {
                        $datas[] = 'average_' . $detail->product_id . "\ttext-end";
                    }
                    $datas[] = "average_total\ttext-end";
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1" def-order-status="desc"/>
                <x-views.delete-form />
            </div>
        </div>
    </div>
    <x-modal-form />
@endsection
