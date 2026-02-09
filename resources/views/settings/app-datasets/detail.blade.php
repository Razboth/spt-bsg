@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex align-items-center">
                        <x-views.search />
                        <h3>{{ $dataset->title }}</h3>
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(str_replace('/', '.', $menu_path).'.create-detail', $dataset->id) }}" text="Tambah Dataset" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-gray-800 bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th width="10">No</th>
                        <th width="200">Judul</th>
                        <th width="150">Penggunaan</th>
                        <th width="200">Contoh</th>
                        <th width="*">Keterangan</th>
                        <th width="150" class="text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data-detail', $dataset->id);
                    $datas = array("title", "code", "example", "description", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
                <br>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
