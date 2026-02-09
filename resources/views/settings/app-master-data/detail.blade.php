@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        @if($category->parent_id != 0)
                            <x-form.select id="combo_1" :datas="$parents" option="- Pilih Induk -" class="w-200px" />
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(str_replace('/', '.', $menu_path).'.create-master', $category->id) }}" text="Tambah {{ $selected_menu->name }}" />
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
                            <th width="100">Kode</th>
                            <th width="200">Nama</th>
                            <th width="*">Keterangan</th>
                            <th width="100">Urutan</th>
                            <th width="150" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.show', $category->id);
                    $datas = array("code", "name", "description", "order", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="4"/>
                <x-views.delete-form/>
                <br>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
