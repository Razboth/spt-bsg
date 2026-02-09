<div class="card">
    <form method="POST" id="form-edit"
          @if($type == 1)
            action="{{ route(Str::replace('/', '.', $menu_path).'.update-detail-approve', [$detail]) }}">
          @elseif($type == 2)
            action="{{ route(Str::replace('/', '.', $menu_path).'.update-detail-approve-2', [$detail]) }}">
          @else
              action="{{ route(Str::replace('/', '.', $menu_path).'.update-detail-approve-3', [$detail]) }}">
            @endif
        @csrf
        @if(!empty($detail))
            @method('PATCH')
        @endif
        @php
            $approvedDate = $detail->approved_date && $detail->approved_date != '0000-00-00' ? $detail->approved_date : date('Y-m-d');
        @endphp
        <x-form.modal-header title="Approve"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Tahapan Aktifitas" name="type_id" :value="$detail->typeActivity->name ?? ''" readonly
                          class="bg-gray-100"/>
            <h5 class="text-gray-900 fw-semibold">Data Agenda</h5>
            <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Tanggal" name="plan_date" :value="setDate($detail->plan_date, 'd F Y') ?? ''"
                                  readonly class="bg-gray-100"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Jam" name="plan_time" :value="$detail->plan_time ?? ''" readonly
                                  class="bg-gray-100"/>
                </div>
            </div>
            <x-form.textarea label="Catatan" name="plan_note" :value="$detail->plan_note ?? ''" readonly
                             class="bg-gray-100"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.file label="Foto" name="plan_filename" value="{{ $detail->plan_filename ?? '' }}" :is-delete="false"/>
                </div>
                <div class="col-md-6">
                    <x-form.file label="Dokumen Pendukung" name="plan_document" value="{{ $detail->plan_document ?? '' }}" :is-delete="false"/>
                </div>
            </div>
            <h5 class="text-gray-900 fw-semibold">Data Realisasi</h5>
            <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Tanggal" name="plan_date"
                                  :value="setDate($detail->realization_date, 'd F Y') ?? ''" readonly
                                  class="bg-gray-100"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Jam" name="plan_time" :value="$detail->realization_time ?? ''" readonly
                                  class="bg-gray-100"/>
                </div>
            </div>
            @if($detail->typeActivity->name == 'Closing')
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Closing Amount" :value="setCurrency($detail->closing_amount) ?? 0" readonly
                                      class="bg-gray-100 text-end"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Nomor Account" :value="$detail->account_number ?? ''" readonly
                                      class="bg-gray-100"/>
                    </div>
                </div>
            @endif
            <x-form.textarea label="Catatan" name="realization_note" :value="$detail->realization_note ?? ''" readonly
                             class="bg-gray-100"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.file label="Foto" name="realization_filename" value="{{ $detail->realization_filename ?? '' }}" :is-delete="false" />
                </div>
                <div class="col-md-6">
                    <x-form.file label="Dokumen Pendukung" name="realization_filename" value="{{ $detail->realization_filename ?? '' }}" :is-delete="false" />
                </div>
            </div>
            <br/>
            <div id="mapid"
                 style="width: 100%; height: 200px; border-radius: 10px; overflow: hidden; position: relative;">
            </div>
            <br/>

            <h5 class="text-gray-900 fw-semibold">Data Approval</h5>
            <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
            <x-form.datepicker name="approved_date" label="Tanggal Approve" value="{{ $approvedDate }}"
                               required="true"/>
            <x-form.textarea name="approved_note" label="Catatan" value="{{ $detail->approved_note ?? '' }}"/>
            <x-form.radio label="Approved Status" name="approved_status" :datas="$arrApprove"
                          value="{{ $detail->approved_status ?? '' }}"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>
<script>
    //document ready
    $(document).ready(function () {
        var mymap;
        var marker;

        function initMap(latitude = 51.505, longitude = -0.09) {
            // Inisialisasi peta dengan koordinat yang diberikan
            mymap = L.map('mapid').setView([latitude, longitude], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mymap);

            // Menambahkan marker ke peta
            marker = L.marker([latitude, longitude]).addTo(mymap);

            // Membuat peta tidak dapat diinteraksi
            makeMapReadOnly();
        }

        // Fungsi untuk membuat peta read-only
        function makeMapReadOnly() {
            mymap.dragging.disable();
            mymap.touchZoom.disable();
            mymap.doubleClickZoom.disable();
            mymap.scrollWheelZoom.disable();
            mymap.boxZoom.disable();
            mymap.keyboard.disable();
            if (mymap.tap) mymap.tap.disable();
        }

        // Fungsi untuk meminta lokasi pengguna
        function requestLocation(latitude, longitude) {
            initMap(latitude, longitude);
        }

        $('#modal-form').off('shown.bs.modal').on('shown.bs.modal', function () {
            if (mymap) {
                mymap.remove();
            }

            requestLocation({{ $detail->latitude }}, {{ $detail->longitude }});

            $(".input-date").flatpickr({
                dateFormat: "d/m/Y",
            });

            $(".input-date-time").flatpickr({
                dateFormat: "d/m/Y H:i",
                enableTime: true,
                time_24hr: true,
            });

            $(".input-time").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                static: true,
            });
            $(".form-select").select2({
                dropdownParent: $("#modal-form"),
            });
        });

    });
</script>