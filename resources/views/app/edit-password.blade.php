<div class="card">
    <form method="POST" id="form-edit" action="{{ route('app.update-password', $route) }}">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="Ubah Password" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input password label="Password" placeholder="Masukkan Password" name="password" required />
            <x-form.input password label="Confirm Password" name="password_confirmation" required />
        </div>
        <x-form.modal-footer />
    </form>
</div>
