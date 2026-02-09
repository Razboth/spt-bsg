@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" enctype="multipart/form-data">
            @csrf
            @if(!empty($activity))
                @method('PATCH')
            @endif
            <x-form.header title="Sales Mapping" :is-can-save="false" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-center mb-5">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        <img src="{{ asset('storage/'.$agent->photo) }}" alt="image" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor Pegawai" name="number" :value="$agent->employee_number ?? ''" readonly class="bg-gray-100"/>
                        <x-form.input label="Atasan" name="number" :value="$agent->leader->name ?? ''" readonly class="bg-gray-100"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Nama" name="number" :value="$agent->name ?? ''" readonly class="bg-gray-100"/>
                        <x-form.input label="Unit Kerja" name="number" :value="$agent->unit->name ?? ''" readonly class="bg-gray-100"/>
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="card-toolbar mb-5">
                    <div class="d-flex justify-content-end">
                        <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.add-customer', [$agent->id]) }}" text="Tambah Customer" size="mw-950px" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                        <thead>
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="min-w-10px">No</th>
                            <th class="min-w-100px">Kode</th>
                            <th class="min-w-150px w-100">Nama Customer</th>
                            <th class="min-w-200px">Kategori</th>
                            <th class="min-w-200px">Nomor Telp</th>
                            <th class="min-w-100px text-center">Kontrol</th>
                        </tr>
                        </thead>
                        <tbody class="text-black fs-7">
                        @php
                            $no = 0;
                        @endphp
                        @foreach($agent->customers as $key => $customer)
                            @php
                                $no++;
                            @endphp
                            <tr>
                                <td><span class="ms-5"><?= $no ?>.</span></td>
                                <td>{{ $customer->code }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->category->name }}</td>
                                <td>{{ $customer->phone_number }}</td>
                                <td>
                                    <x-views.action
                                            menu_path="{{ $menu_path }}"
                                            url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.destroy-customer', $customer->id) }}"
                                    />
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <x-views.delete-form/>
                </div>

            </div>
        </form>
    </div>
    <x-views.delete-form/>
    @php
        $route = route(str_replace('/', '.', $menu_path).'.data-customer', $agent->id);
        $datas = array("code", "name","category_id", "phone_number", "check\ttext-center\tfalse");
        $defOrder = 0;
        $defOrderStatus = 'asc';
        $method = 'GET';
        $classDefault = '';
        $classFirstColumn = '';
    @endphp
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#modal-form').on('shown.bs.modal', function () {
                let datas = @json($datas);
                let route = '{{ $route }}';
                let defOrder = '{{ $defOrder }}';
                let defOrderStatus = '{{ $defOrderStatus }}';
                let classDefault = '{{ $classDefault }}';
                let classFirstColumn = '{{ $classFirstColumn }}';

                createDatatable(datas, route, defOrder, defOrderStatus, classDefault, classFirstColumn);
            });

            $('#modal-form').on('hidden.bs.modal', function () {
                $('#datatables').dataTable().fnDestroy();
            })
        });
    </script>
@endsection
