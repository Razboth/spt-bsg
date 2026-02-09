<div class="card">
    <x-form.modal-header title="Detail Log"/>
    <div class="separator mt-2 mb-5 d-flex"></div>
    <div class="card-body pt-0">
        <div class="row">
            <div class="col-md-6">
                <x-form.input label="Waktu" placeholder="Masukkan Judul" name="title" :value="$activity->created_at ?? ''" readonly class="bg-gray-100"/>
                <x-form.input label="IP" placeholder="Masukkan Judul" name="title" :value="$activity->ip ?? ''" readonly class="bg-gray-100"/>
                <x-form.input label="Entity" placeholder="Masukkan Judul" name="title" :value="$activity->subject_type ?? ''" readonly class="bg-gray-100"/>
            </div>
            <div class="col-md-6">
                <x-form.input label="User" placeholder="Masukkan Judul" name="title" :value="$user->name ?? ''" readonly class="bg-gray-100"/>
                <x-form.input label="Akses Info" placeholder="Masukkan Judul" name="title" :value="$activity->access_info ?? ''" readonly class="bg-gray-100"/>
                <x-form.input label="Action" placeholder="Masukkan Judul" name="title" :value="$activity->event ?? ''" readonly class="bg-gray-100"/>
            </div>
        </div>
        <x-form.textarea label="Before" placeholder="Masukkan Keterangan" name="before" :value="$activity->before ?? ''" class="bg-gray-100 h-150px" readonly/>
        <x-form.textarea label="After" placeholder="Masukkan Keterangan" name="after" :value="$activity->after ?? ''" class="bg-gray-100 h-150px" readonly/>
    </div>
</div>
