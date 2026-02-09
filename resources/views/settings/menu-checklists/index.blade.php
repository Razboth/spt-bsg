@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                &nbsp;<form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search :value="$filterSearch" />
                        <input type="submit" class="btn btn-primary ms-2" value="GO" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="*">Nama</th>
                            <th width="200" class="text-center">Export</th>
                        </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                        @php
                            $no = 0;
                        @endphp
                        @foreach($modules as $key => $module)
                            @php
                                $no++;
                            @endphp
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $module->name ?></td>
                                <td class="text-center">
                                    <a href="{{ route(str_replace('/', '.', $menu_path).'.export', $module->id) }}" class="btn btn-icon btn-success w-30px h-30px me-1" title="List Data">
                                        <i class="fa-solid fa-file-excel"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
