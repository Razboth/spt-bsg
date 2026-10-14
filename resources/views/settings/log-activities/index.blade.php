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
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="min-w-50px">No</th>
                        <th class="min-w-150px">Waktu</th>
                        <th class="min-w-200px w-100">User</th>
                        <th class="min-w-150px">IP</th>
                        <th class="min-w-150px">Device</th>
                        <th class="min-w-150px">Browser</th>
                        <th class="min-w-200px">Entity</th>
                        <th class="min-w-100px">Action</th>
                        <th class="min-w-50px">Detail</th>
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $datas = array("created_at", "name", "ip", "device", "browser", "subject_type", "event", "detail\ttext-center");
                @endphp
                <x-views.datatables method="POST" :datas="$datas" :route="$route" def-order="0" def-order-status="desc"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <style>
    </style>
    <x-modal-form/>
@endsection
