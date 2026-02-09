@extends('layouts.app')
@section('content')
    <div class="card mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-center justify-content-between">
                <div>
                    &nbsp;
                </div>
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('storage/'.$product->icon) }}" width="150" height="150"/>
                    <span class="fw-bold">{{ $product->name }}</span>
                </div>
                <div>
                    <a href="{{ route('psa.products.index') }}" class="btn btn-light-primary me-3">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
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
                        <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.create-child', $product->id) }}" text="Tambah Data" />
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
                        <th class="min-w-100px">Icon</th>
                        <th class="min-w-250px w-100">Nama Produk</th>
                        <th class="min-w-150px">Keterangan</th>
                        <th class="min-w-75px">Urutan</th>
                        <th class="min-w-50px">Status</th>
                        <th class="min-w-125px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data-child', $product->id);
                    $data = array("icon", "name", "description", "order\ttext-end", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$data" :route="$route" def-order="4"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form />
@endsection