<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($score) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $score->id) }}">
        @csrf
        @if(!empty($score))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($score) ? __('Tambah Setting Score') : __('Edit Setting Score') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="setting_id" value="{{ $filterPosition ?? $score->setting_id }}" >
            <x-form.input label="Score" placeholder="Masukkan Score" name="score" :value="$score->score ?? ''" required numeric class="text-end" maxlength="1"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Minimum" name="minimum_value" :value="$score->minimum_value ?? ''" class="text-end"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Maximum" name="maximum_value" :value="$score->maximum_value ?? ''" class="text-end"/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="note" :value="$score->note ?? ''"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>