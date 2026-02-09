<div class="card">
    <form method="POST" id="form-edit" action="{{ route('settings.users.update-password', $id) }}">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="Reset Password" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row d-flex align-items-middle justify-content-center">
                <div class="col-md-8">
                    <x-form.input label="Password" placeholder="Masukkan Password" name="password" required />
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-light-info mt-8" id="generate-password">Generate</button>
                </div>
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#generate-password').click(function() {
            var password = Math.random().toString(36).slice(-8);
            $('input[name="password"]').val(password);
        });
    });
</script>
