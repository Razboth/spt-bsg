@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex flex-wrap gap-2">
                        <x-views.search class="w-200px w-md-200px"/>
                        <x-form.select name="combo_3" :datas="$settingProducts" option="- Semua Kelompok -"
                                       class="w-200px w-md-200px"
                                       event="resetFilter();getSub(this.value,'combo_5', 'products-by-setting');getSub(this.value,'combo_4', 'sales-by-setting')"/>
                        <x-form.select name="combo_4" :datas="$sales" option="- Semua Sales -"
                                       class="w-200px w-md-200px"/>
                        <x-form.select name="combo_5" :datas="$products" option="- Semua Produk -"
                                       class="w-200px w-md-200px"/>
                    </div>
                </form>
            </div>
            <div class="card-toolbar mt-3 mt-md-0">
                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <x-views.filter-month-year nameMonth="combo_1" :valueMonth="date('m')" nameYear="combo_2"
                                               :valueYear="date('Y')" :all="true" class="w-100 w-md-auto"/>
                    @can('add '.$menu_path)
                        <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}"
                                            text="Tambah Data"/>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="min-w-50px">No</th>
                        <th class="min-w-100px">Tanggal</th>
                        <th class="min-w-150px">Nomor</th>
                        @if(Auth::user()->role_id != 9)
                            <th class="min-w-150px">Sales</th>
                            <th class="min-w-150px">Cabang</th>
                        @endif
                        <th class="min-w-150px">Produk</th>
                        <th class="min-w-150px w-100">Customer</th>
                        <th class="min-w-150px">Aktifitas</th>
                        <th class="min-w-200px">Update</th>
                        <th class="min-w-100px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    if(Auth::user()->role_id != 9){
                        $datas = array("date","number", "agent_id", "unit_id", "product_id", "customer_id", "recent_activity", "updated_at", "action\ttrue\tfalse");
                    }else{
                        $datas = array("date","number", "product_id", "customer_id", "recent_activity", "updated_at", "action\ttrue\tfalse");
                    }
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1" def-order-status="desc"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <style>
    </style>
    <x-modal-form/>
    <script>
        function resetFilter() {
            $('#combo_4').val('').trigger('change'); // Optional, to trigger change event for other dependent UI elements
            $('#combo_5').val('').trigger('change'); // Optional, to trigger change event for other dependent UI elements

            console.log('test')
        }
    </script>
@endsection