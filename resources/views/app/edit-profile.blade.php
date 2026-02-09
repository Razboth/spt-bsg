<div class="card">
    <form method="POST" id="form-edit" action="{{ route('app.update-profile', $route) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="Edit Profile" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="d-flex justify-content-center">
                <x-form.image-input name="photo" value="{{ $user->photo ?? '' }}" />
            </div>
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $user->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Email" placeholder="Masukkan Email" name="email" value="{{ $user->email ?? '' }}" required />
                </div>
                <div class="col-md-6">
                    <x-form.input label="Username" placeholder="Masukkan Username" name="username" value="{{ $user->username ?? '' }}" required />
                </div>
            </div>
            <x-form.input label="Role" placeholder="Masukkan Username" value="{!! $role->name !!}" readonly />
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" value="{{ $user->description ?? '' }}" name="description" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
