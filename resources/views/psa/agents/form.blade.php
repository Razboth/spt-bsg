@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($agent) ? route(Str::replace('/', '.', $menu_path) . '.store') : route(Str::replace('/', '.', $menu_path) . '.update', $agent->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($agent))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($agent) ? 'Tambah Sales Agent' : 'Edit Sales Agent' }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-center mb-2">
                    <x-form.image-input name="photo" value="{{ $agent->photo ?? '' }}" max="2" />
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" :value="$agent->name ?? ''" required/>
                        <x-form.input label="Tempat Lahir" placeholder="Masukkan Tempat Lahir" name="birth_place" :value="$agent->birth_place ?? ''"/>
                        <x-form.input label="Nomor HP (WA)" placeholder="Masukkan Nomor HP" name="phone_number" :value="$agent->phone_number ?? ''" maxlength="15"/>
                        <x-form.radio label="Jenis Kelamin" name="gender" :datas="$genderOption" :value="$agent->gender ?? ''" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Nomor Pegawai" placeholder="Masukkan Nomor Pegawai"  name="employee_number" :value="$agent->employee_number ?? ''"/>
                        <x-form.datepicker label="Tanggal Lahir" name="birth_date" :value="$agent->birth_date ?? ''" />
                        <x-form.input label="Email" placeholder="Masukkan Email" name="email" :value="$agent->email ?? ''" />
                        <x-form.input label="Nomor KTP" placeholder="Masukkan Nomor KTP"  name="identity_number" :value="$agent->identity_number ?? ''"/>
                    </div>
                </div>
                <x-form.textarea label="Alamat" name="address" :value="$agent->address ?? ''" />

                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select label="Pangkat" name="rank_id" option="- Pilih Pangkat -" :datas="$ranks" :value="$agent->rank_id ?? ''" required />
                        <x-form.select label="Atasan" name="leader_id" option="- Pilih Atasan -" :datas="$leaders" :value="$agent->leader_id ?? ''" />
                        <x-form.datepicker label="Tanggal Masuk" name="join_date" :value="$agent->join_date ?? ''" required />
                        <x-form.select label="Status Pegawai" name="status_id" option="- Pilih Status Pegawai -" :datas="$statuses" :value="$agent->status_id ?? ''" required />
                        <x-form.radio label="Jenis Aktifitas" name="activity_type" :datas="$activityTypeOptions" :value="$agent->activity_type ?? ''" />
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Posisi" name="position_id" option="- Pilih Posisi -" :datas="$positions" :value="$agent->position_id ?? ''" required />
                        <x-form.select label="Tipe Pegawai" name="type_id" option="- Pilih Tipe Pegawai -" :datas="$types" :value="$agent->type_id ?? ''" required />
                        <x-form.select label="Unit Kerja" name="unit_id" option="- Pilih Unit Kerja -" :datas="$units" :value="$agent->unit_id ?? ''" />
                        <x-form.datepicker label="Tanggal Keluar" name="leave_date" :value="$agent->leave_date ?? ''" />
                        <x-form.image-input name="identity_file" label="File KTP" :value="$agent->identity_file ?? ''" />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
