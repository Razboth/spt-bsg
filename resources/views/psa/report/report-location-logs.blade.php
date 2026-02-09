@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-toolbar">
                    <x-views.search class="mb-2 mb-md-0 w-100 w-md-250px"/>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        <x-views.export-button id="btnExport" text="Export Data" class="me-3" url="{{ $menu_path }}"/>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables"
                       class="table table-rounded table-row-bordered custom-border-bottom border border-white gy-5 gs-7">
                    <thead class="border border-white">
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        <th rowspan="2" class="min-w-50px text-center ps-2">No</th>
                        <th rowspan="2" class="min-w-200px text-center">Nama</th>
                        <th rowspan="2" class="min-w-200px text-center">Cabang</th>
                        <th rowspan="2" class="min-w-200px text-center">Jabatan</th>
                        <th rowspan="2" class="min-w-200px text-center">Waktu</th>
                        <th colspan="3" class="min-w-300px w-50 text-center">Lokasi</th>
                    </tr>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        <th class="min-w-100px text-center">Longitude</th>
                        <th class="min-w-100px text-center">Latitude</th>
                        <th class="min-w-250px text-center">Alamat</th>
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
                "unit_id",
                "position_id",
                "created_at",
                "longitude",
                "latitude",
                "address",
                );
            @endphp
            <x-views.datatables :datas="$datas" :route="$route" def-order="1" class-default="border-end"
                                class-first-column="ps-2"/>
        </div>
    </div>
    <x-modal-form/>
@endsection