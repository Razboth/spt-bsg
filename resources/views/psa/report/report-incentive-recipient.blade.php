@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-toolbar">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <x-views.search class="mb-2 mb-md-0 w-100 w-md-250px" />
                        <x-form.select name="combo_1" :datas="$arrPeriods" :value="array_key_last($arrPeriods)" class="w-250px w-md-150px" />
                        @if(Auth::user()->role->name !='Manager')
                            <x-form.select name="combo_2" :datas="$settingPositions" :value="array_key_first($settingPositions)" class="w-250px" />
                         @endif
                        @if(Auth::user()->role->name !='Manager' && Auth::user()->role->name != 'Pemimpin Cabang')
                            <x-form.select name="combo_3" :datas="$units" option="- Semua Cabang -" class="w-250px w-md-200px" />
                        @endif
                        <x-views.filter-datepicker class="w-md-150px w-200px" name="filter_1" :value="date('Y-01-01')" />
                        &nbsp;s/d&nbsp;
                        <x-views.filter-datepicker class="w-md-150px w-200px" name="filter_2" :value="date('Y-12-31')" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        <x-views.export-button id="btnExport" text="Export Data" class="me-3" url="{{ $menu_path }}" />
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered custom-border-bottom border border-white gy-5 gs-7">
                    <thead class="border border-white">
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        <th class="min-w-50px text-center ps-2">No</th>
                        <th class="min-w-200px w-100 text-center">Nama</th>
                        <th class="min-w-150px text-center">Cabang</th>
                        <th class="min-w-150px text-center">Jabatan</th>
                        <th class="min-w-100px text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7 border border-gray-300">
                    </tbody>
                </table>
            </div>
            @php
                $route = route(Str::replace('/', '.', $menu_path).'.data');
                $datas = array(
                "name",
                "unit_id\ttext-start\tfalse",
                "position_id\ttext-start\tfalse",
                "status_incentive\ttext-start\tfalse",
                );
            @endphp
            <x-views.datatables :datas="$datas" :route="$route" def-order="1" class-default="border-end" class-first-column="ps-2" method="POST" />
        </div>
    </div>
    <x-modal-form/>
@endsection