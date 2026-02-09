@extends('layouts.app')
@section('content')
    <div class="card mb-5">
        <x-form.header title="Data Produk" :is-can-save="false"
                       :back-url="route(str_replace('/', '.', $menu_path).'.index')"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Nama" name="number" :value="$setting->name ?? ''" readonly
                                  class="bg-gray-100"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Posisi" name="number" :value="$position ?? ''" readonly class="bg-gray-100"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">

            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal
                                route="{{ route(Str::replace('/', '.', $menu_path).'.create-product', $setting->id) }}"
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
                        <th class="min-w-25px">No</th>
                        <th class="min-w-250px w-100">Nama Produk</th>
                        <th class="min-w-50px">Bobot</th>
                        <th class="min-w-125px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach($setting->details as $detail)
                        @php
                            $no++;
                        @endphp
                        <tr>
                            <td>{{ $no }}.</td>
                            <td>{{ $detail->product->name }}</td>
                            <td>{{ $detail->weight }}</td>
                            <td>
                                <x-views.action
                                        :menu_path="$menu_path"
                                        :url_edit="route(Str::replace('/', '.', $menu_path).'.edit-product', $detail->id)"
                                        :url_destroy="route(Str::replace('/', '.', $menu_path).'.destroy-product', $detail->id)"
                                        :is-modal="true"
                                />
                            </td>
                        </tr>
                    @endforeach
                    <x-views.delete-form/>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection