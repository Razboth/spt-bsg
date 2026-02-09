<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($user) ? route('settings.users.store') : route('settings.users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($user))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($user) ? __('Tambah User') : __('Edit User') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="d-flex justify-content-center">
                <x-form.image-input name="photo" value="{{ $user->photo ?? '' }}" max="2" />
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $user->name ?? '' }}" required/>
                    <x-form.input label="Email" placeholder="Masukkan Email" name="email" value="{{ $user->email ?? '' }}" required />
                </div>
                <div class="col-md-6">
                    <x-form.input label="Username" placeholder="Masukkan Username" name="username" value="{{ $user->username ?? '' }}" required />
                    <x-form.select label="Role" name="role_id" required :datas="$roles" option="- Pilih Role -" value="{{ $user->role_id ?? '' }}" />
                </div>
            </div>
            <x-form.select label="Pegawai" name="model_id" :datas="$models" option="- Pilih Pegawai -" value="{{ $user->model_id ?? '' }}" />
            @if(empty($user))
            <x-form.input password label="Password" placeholder="Masukkan Password" name="password" required />
            <x-form.input password label="Confirm Password" name="password_confirmation" required />
            @endif
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" value="{{ $user->description ?? '' }}" name="description" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
