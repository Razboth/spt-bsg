@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.import') }}" class="me-3 bg-info text-white" icon="fa-solid fa-upload" text="Import Data" />
                        <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.create') }}" text="Tambah Data" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="min-w-25px">No</th>
                        <th class="min-w-250px w-100">Sales</th>
                        <th class="min-w-100px">Periode</th>
                        <th class="min-w-200px">Customer</th>
                        <th class="min-w-200px">Produk</th>
                        <th class="min-w-100px">Nilai</th>
                        <th class="min-w-100px">Status</th>
                        <th class="min-w-125px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $data = array("agent_id", "month", "customer_id", "product_id", "value", "approved_status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables method="POST" :datas="$data" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form />
@endsection