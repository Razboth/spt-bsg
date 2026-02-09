@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex flex-wrap gap-2">
                        <x-views.search />
                        <x-form.select name="combo_1" :datas="$categories" option="- Semua Kategori -" class="w-200px" />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.import') }}" class="me-3 bg-info text-white" icon="fa-solid fa-upload" text="Import Data" />
                        <x-views.add-button route="{{ route(Str::replace('/', '.', $menu_path).'.create') }}" text="Tambah Data" />
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
                        <th class="min-w-100px">Kode</th>
                        <th class="min-w-250px w-100">Nama Customer</th>
                        <th class="min-w-150px">Nomor HP</th>
                        <th class="min-w-150px">Email</th>
                        <th class="min-w-50px">Status</th>
                        <th class="min-w-125px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $data = array("code", "name", "phone_number", "email", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$data" :route="$route" def-order="2"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form />
@endsection