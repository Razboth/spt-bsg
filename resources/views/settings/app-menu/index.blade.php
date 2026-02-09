@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-form.select name="filterModule" :datas="$moduls" value="{{ $filterModule }}" event="getSub(this.value, 'filterSubModule', 'sub-moduls', false)" class="w-200px" />
                        <x-form.select name="filterSubModule" :datas="$submoduls" value="{{ $filterSubModule }}" class="w-200px" />
                        <input type="submit" class="btn btn-primary ms-2" value="GO" />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah {{ $selected_menu->name }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="*">Nama</th>
                            <th width="200">Target</th>
                            <th width="50">Urutan</th>
                            <th width="100">Status</th>
                            <th width="150" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                        @php
                            $no = 0;
                        @endphp
                        @foreach($menus as $key => $menu)
                            @php
                                $no++;
                            @endphp
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $menu->name ?></td>
                                <td><?= $menu->target ?></td>
                                <td align="right"><?= $menu->order ?></td>
                                <td align="center"><x-views.status :status="$menu->status" /></td>
                                <td>
                                    <x-views.action
                                        menu_path="{{ $menu_path }}"
                                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.edit', $menu->id) }}"
                                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.destroy', $menu->id) }}"
                                        url_slot="{{ route(Str::replace('/', '.', $menu_path).'.create-child', [$menu->id]) }}"
                                        icon_slot="bi bi-plus-circle"
                                        is-modal="true"
                                    />
                                </td>
                            </tr>
                            @if(isset($menu->children))
                                @foreach($menu->children as $children)
                                    @php
                                        $no++;
                                    @endphp
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><span class="ms-15"><?= $children->name ?></span></td>
                                        <td><?= $children->target ?></td>
                                        <td align="right"><?= $children->order ?></td>
                                        <td align="center"><x-views.status :status="$children->status" /></td>
                                        <td>
                                            <x-views.action
                                                menu_path="{{ $menu_path }}"
                                                url_edit="{{ route(Str::replace('/', '.', $menu_path).'.edit', $children->id) }}"
                                                url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.destroy', $children->id) }}"
                                                is-modal="true"
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
