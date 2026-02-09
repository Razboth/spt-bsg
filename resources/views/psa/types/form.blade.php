<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($type) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $type->id) }}">
        @csrf
        @if(!empty($type))
            @method('PATCH')
        @endif
        @php
            $defaultSVG = "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">
                            <path opacity=\"0.3\" d=\"M5.78001 21.115L3.28001 21.949C3.10897 22.0059 2.92548 22.0141 2.75004 21.9727C2.57461 21.9312 2.41416 21.8418 2.28669 21.7144C2.15923 21.5869 2.06975 21.4264 2.0283 21.251C1.98685 21.0755 1.99507 20.892 2.05201 20.7209L2.886 18.2209L7.22801 13.879L10.128 16.774L5.78001 21.115Z\" fill=\"currentColor\" />
                            <path d=\"M21.7 8.08899L15.911 2.30005C15.8161 2.2049 15.7033 2.12939 15.5792 2.07788C15.455 2.02637 15.3219 1.99988 15.1875 1.99988C15.0531 1.99988 14.92 2.02637 14.7958 2.07788C14.6717 2.12939 14.5589 2.2049 14.464 2.30005L13.74 3.02295C13.548 3.21498 13.4402 3.4754 13.4402 3.74695C13.4402 4.01849 13.548 4.27892 13.74 4.47095L14.464 5.19397L11.303 8.35498C10.1615 7.80702 8.87825 7.62639 7.62985 7.83789C6.38145 8.04939 5.2293 8.64265 4.332 9.53601C4.14026 9.72817 4.03256 9.98855 4.03256 10.26C4.03256 10.5315 4.14026 10.7918 4.332 10.984L13.016 19.667C13.208 19.859 13.4684 19.9668 13.74 19.9668C14.0115 19.9668 14.272 19.859 14.464 19.667C15.3575 18.77 15.9509 17.618 16.1624 16.3698C16.374 15.1215 16.1932 13.8383 15.645 12.697L18.806 9.53601L19.529 10.26C19.721 10.452 19.9814 10.5598 20.253 10.5598C20.5245 10.5598 20.785 10.452 20.977 10.26L21.7 9.53601C21.7952 9.44108 21.8706 9.32825 21.9221 9.2041C21.9737 9.07995 22.0002 8.94691 22.0002 8.8125C22.0002 8.67809 21.9737 8.54505 21.9221 8.4209C21.8706 8.29675 21.7952 8.18392 21.7 8.08899Z\" fill=\"currentColor\" />
                        </svg>";
        @endphp
        <x-form.modal-header title="{{ empty($type) ? __('Tambah Tahapan Aktifitas') : __('Edit Tahapan Aktifitas') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama Aktifitas" placeholder="Masukkan Nama Aktifitas" name="name" :value="$type->name ?? ''" required/>
            <x-form.input label="Poin Aktifitas" placeholder="Masukkan Poin Aktifitas" name="point" :value="$type->point ?? ''" required numeric class="w-50"/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" :value="$type->description ?? ''"/>
            <x-form.checkbox label="Jenis Aktifitas" name="activity_type" :arr="$activityTypeOptions" :datas="$activityTypes ?? []" />
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Order" placeholder="Masukkan Order" name="order" value="{{ $type->order ?? $lastOrder }}" numeric class="text-end" />
                </div>
                <div class="col-md-6">
                    <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $type->status ?? '' }}" />
                </div>
            </div>
            <h5 class="text-gray-900 fw-semibold">Developer Section</h5>
            <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
            <x-form.textarea label="Icon SVG" placeholder="Masukkan Icon SVG" name="icon" :value="$type->icon ?? $defaultSVG"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>