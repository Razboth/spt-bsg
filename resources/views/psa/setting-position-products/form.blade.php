<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($setting) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $setting->id) }}">
        @csrf
        @if(!empty($setting))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($setting) ? __('Tambah Mapping Posisi') : __('Edit Mapping Posisi') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="type" value="{{ getParameterMenu($menu_path) }}" />
            <x-form.input label="Nama Mapping Posisi" placeholder="Masukkan Nama Mapping Posisi" name="name" :value="$setting->name ?? ''" required/>
            <div class="form-group mb-5">
                <label class="form-label required">Posisi</label>
                <table style="width: 100%;">
                    <tr>
                        @foreach($positions as $key => $value)
                            @if ($loop->index % 3 == 0 && !$loop->first)
                    </tr><tr>
                        @endif
                        <td>
                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox"
                                       @if(in_array($key, $settingPositions ?? [])) checked @endif
                                       value="{{ $key }}"
                                       name="positions[{{ $key }}]"
                                       id="positions[{{ $key }}]" />
                                <label class="form-check-label text-dark" for="positions[{{ $key }}]">{{ $value }}</label>
                            </div>
                        </td>
                        @endforeach
                    </tr>
                </table>
                @error('positions')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="positions-error"></div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" :value="$setting->description ?? ''"/>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $setting->status ?? '' }}" />
        </div>
        <x-form.modal-footer/>
    </form>
</div>