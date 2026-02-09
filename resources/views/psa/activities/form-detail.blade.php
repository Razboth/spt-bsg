<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($detail) ? route(Str::replace('/', '.', $menu_path).'.store-detail', $activity->id) : route(Str::replace('/', '.', $menu_path).'.update-detail', $detail->id) }}">
        @csrf
        @php
            $divClosingClass = 'd-none';
            if(!empty($detail)){
                if($detail->type_id == '40b85dfb-a08d-43e5-941a-7ba4dddbd9f2'){
                    $divClosingClass = 'd-block';
                }
            }
        @endphp
        @if(!empty($detail))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($detail) ? __('Tambah Tahapan') : __('Edit Tahapan') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="activity_id" value="{{ $activity->id }}">
            <input type="hidden" id="latitude" name="latitude" value="{{ $detail->latitude ?? '' }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ $detail->longitude ?? '' }}">
            <input type="hidden" id="address" name="address" value="{{ $detail->address ?? '' }}">
            <input type="hidden" name="type_id" value="{{ $detail->type_id ?? '' }}"/>
            <x-form.input label="Tahapan Aktifitas" :value="$detail->typeActivity->name ?? ''" readonly class="bg-gray-100"/>
            <!--begin::Accordion-->
            <div class="accordion" id="kt_accordion_1">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="kt_accordion_1_header_1">
                        <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#kt_accordion_1_body_1" aria-expanded="true"
                                aria-controls="kt_accordion_1_body_1">
                            Data Rencana
                        </button>
                    </h2>
                    <div id="kt_accordion_1_body_1" class="accordion-collapse collapse show"
                         aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#kt_accordion_1">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.datepicker label="Tanggal" name="plan_date"
                                                       :value="$detail->plan_date ?? ''"
                                                       class="w-100"/>
                                </div>
                                <div class="col-md-6">
                                    <x-form.timepicker label="Jam" name="plan_time" :value="$detail->plan_time ?? ''"/>
                                </div>
                            </div>
                            <x-form.textarea label="Catatan" name="plan_note" :value="$detail->plan_note ?? ''"/>
                            <x-form.image-input label="Foto" name="plan_filename"
                                                max="2" value="{{ $detail->plan_filename ?? '' }}"/>
                            <x-form.file label="Dokumen Pendukung" name="plan_document" accept=".pdf,.xls,.xlsx,.jpg,.png,.jpeg"
                                         max="2" value="{{ $detail->plan_document ?? '' }}"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="kt_accordion_1_header_2">
                        <button class="accordion-button fs-4 fw-semibold collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_2" aria-expanded="false"
                                aria-controls="kt_accordion_1_body_2">
                            Data Realisasi
                        </button>
                    </h2>
                    <div id="kt_accordion_1_body_2" class="accordion-collapse collapse"
                         aria-labelledby="kt_accordion_1_header_2" data-bs-parent="#kt_accordion_1">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.datepicker label="Tanggal" name="realization_date"
                                                       :value="$detail->realization_date ?? ''"
                                                       class="w-100"/>
                                </div>
                                <div class="col-md-6">
                                    <x-form.timepicker label="Jam" name="realization_time"
                                                       :value="$detail->realization_time ?? ''"/>
                                </div>
                            </div>
                            <x-form.textarea label="Catatan" name="realization_note"
                                             :value="$detail->realization_note ?? ''"/>
                            <x-form.image-input label="Foto" name="realization_filename"
                                                max="2" value="{{ $detail->realization_filename ?? '' }}"/>
                            <x-form.file label="Dokumen Pendukung" name="realization_document"
                                         accept=".pdf,.xls,.xlsx,.jpg,.png,.jpeg"
                                         max="2"
                                         value="{{ $detail->realization_document ?? '' }}"/>
                            <div class="invalid-feedback" id="realization_document-error"></div>
                            <div id="divClosing" class="{{ $divClosingClass }}">
                                <h5 class="text-gray-900 fw-semibold">Data Closing</h5>
                                <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input label="CIF" name="cif" numeric class="text-end"
                                                      :value="$detail->cif ?? ''"/>
                                        <x-form.input label="Closing Amount" name="closing_amount"
                                                      :value="$detail->closing_amount ?? 0" currency/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input label="Nomor Account" name="account_number" numeric
                                                      class="text-end" :value="$detail->account_number ?? ''"/>

                                    </div>
                                </div>
                            </div>
                            <div id="divBtnLocation" class="mb-5">
                                <button type="button" class="btn btn-primary w-100" id="btnLocation">
                                    <i class="fa-solid fa-map-location-dot"></i> Tampilkan Lokasi
                                </button>
                            </div>
                            <div id="mapid"
                                 style="width: 100%; height: 200px; border-radius: 10px; overflow: hidden; position: relative;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Accordion-->


        </div>
        <x-form.modal-footer/>
    </form>
</div>

<script>
    //document ready
    $(document).ready(function () {
        var mymap;
        var marker;
        var mapInitialized = false;

        // Sembunyikan peta saat halaman dimuat
        $('#mapid').hide();

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
            mapInitialized = true;
        }

        $('#btnLocation').click(function () {
            // Tampilkan peta saat tombol diklik
            $('#mapid').show();

            // Inisialisasi peta jika belum diinisialisasi
            if (!mapInitialized) {
                @if(!empty($detail) && $detail->latitude && $detail->longitude)
                requestLocation({{ $detail->latitude }}, {{ $detail->longitude }});
                @else
                requestLocation();
                @endif
            } else {
                // Jika peta sudah diinisialisasi, perbaharui ukuran peta
                if (mymap) {
                    mymap.invalidateSize();
                }
            }
        });

        function setLocation(latitude, longitude) {
            if (mymap) {
                mymap.setView(new L.LatLng(latitude, longitude), 13);
                marker.setLatLng(new L.LatLng(latitude, longitude)); // Memperbarui posisi marker
            }
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;

            reverseGeocode(latitude, longitude);
        }

        function reverseGeocode(latitude, longitude) {
            var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Untuk debugging
                    if (data.display_name) {
                        document.getElementById('address').value = data.display_name; // Simpan alamat ke dalam form
                    }
                })
                .catch(error => console.log(error));
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
            if (latitude && longitude) {
                initMap(latitude, longitude);
                setLocation(latitude, longitude);
            } else {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        initMap(position.coords.latitude, position.coords.longitude);
                        setLocation(position.coords.latitude, position.coords.longitude);
                    }, function (error) {
                        console.error("Error Code = " + error.code + " - " + error.message);
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }
        }

        $('#type_id').on('change', function () {
            var value = $(this).val();
            if (value === '40b85dfb-a08d-43e5-941a-7ba4dddbd9f2') {
                $('#divClosing').removeClass('d-none').addClass('d-block');
            } else {
                $('#divClosing').removeClass('d-block').addClass('d-none');
            }
        });

        $('#modal-form').off('shown.bs.modal').on('shown.bs.modal', function () {
            if (mymap) {
                mymap.remove();
                mapInitialized = false;
            }

            // Sembunyikan peta saat modal dibuka
            $('#mapid').hide();

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