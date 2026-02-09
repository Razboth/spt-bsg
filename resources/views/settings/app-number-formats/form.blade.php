<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($format) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $format->id) }}">
        @csrf
        @if(!empty($format))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($format) ? __('Tambah Penomoran') : __('Edit Penomoran') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Judul" placeholder="Masukkan Judul" name="title" value="{{ $format->title ?? '' }}"
                          required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Kode" placeholder="Masukkan Kode" name="code"
                                  value="{{ $format->code ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.select label="Pemisah" :datas="$delimiterOptions" option="- Pilih Pemisah -"
                                   name="delimiter" value="{{ $format->delimiter ?? '' }}" required/>
                </div>
            </div>
            <!--begin::Alert-->
            <div class="alert alert-danger d-flex align-items-center p-5">
                <!--begin::Icon-->
                <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                <!--end::Icon-->

                <!--begin::Wrapper-->
                <div class="d-flex flex-column">
                    <!--begin::Content-->
                    <span>Tipe Variabel 1 harus dalam bentuk Sequence, mengubah Format di tengah live akan mereset sequence dari awal kembali (0).</span>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Alert-->
            @for($i = 1; $i<= 5; $i++)
                @php
                    $type = !empty($format->{"var{$i}_type"}) ? $format->{"var{$i}_type"} : '';
                    $value = !empty($format->{"var{$i}_value"}) ? $format->{"var{$i}_value"} : '';

                    $month = !empty($format->{"var{$i}_type"}) && $format->{"var{$i}_type"} === 'month' ? "d-block" : 'd-none';
                    $year = !empty($format->{"var{$i}_type"}) && $format->{"var{$i}_type"} === 'year' ? "d-block" : 'd-none';
                    $text = !empty($format->{"var{$i}_type"}) && ($format->{"var{$i}_type"} === 'sequence' || $format->{"var{$i}_type"} === 'code') ? "d-block" : 'd-none';
                    $text = empty($format->{"var{$i}_type"}) ? "d-block" : $text;
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select label="Tipe Variabel {{ $i }}" :datas="$variableOptions" select2="0" option="- Pilih Tipe -"
                                       name="var{{ $i }}_type" value="{{ $type }}" />
                    </div>
                    <div class="col-md-6">
                        <div id="month_{{ $i }}" class="{{ $month }}">
                            <x-form.select label="Value {{ $i }}" :datas="$monthOptions" option="- Pilih Tipe Bulan -" select2="0"
                                           name="var{{ $i }}_value_month" id="var{{ $i }}_value_month" value="{{ $value }}"/>
                        </div>
                        <div id="year_{{ $i }}" class="{{ $year }}">
                            <x-form.select label="Value {{ $i }}" :datas="$yearOptions" option="- Pilih Tipe Tahun -" select2="0"
                                           name="var{{ $i }}_value_year" id="var{{ $i }}_value_year" value="{{ $value }}"/>
                        </div>
                        <div id="text_{{ $i }}" class="{{ $text }}">
                            <x-form.input label="Value {{ $i }}" placeholder="Masukkan Value" name="var{{ $i }}_value_text"
                                          id="var{{ $i }}_value_text" value="{{ $value }}"/>
                        </div>
                    </div>
                </div>
            @endfor
            <x-form.input label="Contoh" readonly name="example" value="{{ $format->example ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description"
                             value="{{ $format->description ?? '' }}"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>
<script>
    // Add event listeners to each type select option
    for (let i = 1; i <= 5; i++) {
        $(`#var${i}_type`).on('change', function () {
            updateValueInput(i);
            updateExample();
        });

        // Add event listeners to each value input/select option
        $(`#var${i}_value_text, #var${i}_value_month, #var${i}_value_year`).on('change keyup', function () {
            updateExample();
        });
    }

    function updateValueInput(i) {
        let type = $(`#var${i}_type`).val();
        // Sembunyikan semua input value terlebih dahulu
        $(`#text_${i}, #month_${i}, #year_${i}`).addClass('d-none');

        if (type === 'sequence' || type === 'code') {
            $(`#text_${i}`).removeClass('d-none').addClass('d-block');
        } else if (type === 'month') {
            $(`#month_${i}`).removeClass('d-none').addClass('d-block');
        } else if (type === 'year') {
            $(`#year_${i}`).removeClass('d-none').addClass('d-block');
        }
    }

    function updateExample() {
        let delimiter = $('#delimiter').val();
        let example = '';

        for (let i = 1; i <= 5; i++) {
            let type = $(`#var${i}_type`);
            let value;

            if (type.val() === 'sequence' || type.val() === 'code') {
                value = $(`#var${i}_value_text`);
            } else if (type.val() === 'month') {
                value = $(`#var${i}_value_month`);
            } else if (type.val() === 'year') {
                value = $(`#var${i}_value_year`);
            }

            if (type.val() && value) {
                let segment = '';

                if (type.val() === 'sequence') {
                    segment = "0".padStart(value.val(), '0');
                } else if (type.val() === 'month') {
                    if (value.val() === 'roman') {
                        segment = "XI";
                    } else if(value.val() === 'number') {
                        segment = "11";
                    } else{
                        segment = "";
                    }
                } else if (type.val() === 'year') {
                    if (value.val() === '4digit') {
                        segment = "2023";
                    } else if(value.val() === '2digit') {
                        segment = "23";
                    } else{
                        segment = "";
                    }
                } else {
                    segment = value.val();
                }

                example += segment;

                if (i < 5) {
                    example += delimiter;
                }
            }
        }

        $('#example').val(example);

        //DELETE LAST DIGIT
        let lastDigit = example.slice(-1);
        if (lastDigit === delimiter) {
            $('#example').val(example.slice(0, -1));
        }
    }
</script>
