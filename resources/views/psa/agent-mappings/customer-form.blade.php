<div class="card">
    <form method="POST" id="form-edit-continue" action="{{ route(Str::replace('/', '.', $menu_path).'.store-customer', $agent->id) }}">
        @csrf
        <x-form.modal-header title="Data Customer"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
    </form>

    <div class="card-header border-0">
        <div class="card-toolbar">
            <form method="GET" id="form-filter">
                <div class="d-flex">
                    <x-form.select name="combo_1" :datas="$types" class=" select-modal " option="- Semua Tipe Customer -" class="w-250px" />
                </div>
            </form>
        </div>
    </div>
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.store-customer', $agent->id) }}">
        @csrf
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="w-50px">No</th>
                        <th class="w-100px">Kode</th>
                        <th>Nama Cutomer</th>
                        <th class="w-200px">Kategori</th>
                        <th class="w-100px">No Telp</th>
                        <th class="w-50px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <x-form.modal-footer/>
    </form>
</div>