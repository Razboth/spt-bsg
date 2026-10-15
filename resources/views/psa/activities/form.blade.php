@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit"
              action="{{ empty($activity) ? route(Str::replace('/', '.', $menu_path) . '.store') : route(Str::replace('/', '.', $menu_path) . '.update', $activity->id) }}"
             >
            @csrf
            @if(!empty($activity))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($activity) ? 'Tambah Aktifitas' : 'Edit Aktifitas' }}"
                           :back-url="route('psa.activities.index')"/>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor" name="number" :value="$activity->number ?? $lastNumber" required
                                      readonly class="bg-gray-100"/>
                        @if(Auth::user()->role_id == 9)
                            <x-form.input label="Pegawai" :value="Auth::user()->name ?? ''" readonly
                                          class="bg-gray-100"/>
                            <input type="hidden" name="agent_id" value="{{ Auth::user()->model_id }}">
                        @else
                            <x-form.select label="Pegawai" name="agent_id" option="- Pilih Pegawai -" :datas="$agents"
                                           :value="$activity->agent_id ?? ''" required
                                           event="getSub(this.value, 'product_id', 'products');"/>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker label="Tanggal" name="date" :value="$activity->date ?? date('Y-m-d')"
                                           required class="w-100"/>
                        @if(empty($activity))
                            <x-form.radio label="Tipe Aktifitas" name="activity_type"
                                          :value="$activity->activity_type ?? ''" required :datas="$typeActivities"/>
                        @else
                            <x-form.input label="Tipe Aktifitas"
                                          :value="$typeActivities[$activity->activity_type] ?? ''" readonly
                                          class="bg-gray-100"/>
                            <input type="hidden" name="activity_type" value="{{ $activity->activity_type }}">
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @if(!empty($activity))
                            <x-form.input label="Produk" :value="$activity->product->name ?? ''" readonly
                                          class="bg-gray-100"/>
                            <input type="hidden" name="product_id" value="{{ $activity->product_id ?? '' }}">
                        @else
                            <x-form.select label="Produk" name="product_id" option="- Pilih Produk -" :datas="$products"
                                           :value="$activity->product_id ?? ''" required/>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Customer" name="customer_id" option="- Pilih Customer -"
                                       :datas="$customers" :value="$activity->customer_id ?? ''" required/>
                    </div>
                </div>
                @if(!empty($activity))
                    <div class="d-flex justify-content-between mb-5">
                        <div>
                            <h5 class="text-gray-900 fw-semibold">Timeline Aktifitas</h5>
                            <div style="width: 100px; height: 2px;" class="bg-primary"></div>
                        </div>
                        <x-views.add-button-modal
                                route="{{ route(str_replace('/', '.', $menu_path).'.edit-batch', $activity->id) }}"
                                text="Bulk Edit" icon="fa-solid fa-pen-to-square"/>
                    </div>
                    <div class="timeline">
                        @foreach($details as $detail)
                            @php
                                $detail->load('typeActivity');
                                if($detail->approved_status == 't'){
                                    $class = 'success';
                                    $text = 'Disetujui';
                                } else if($detail->approved_status == 'f'){
                                    $class = 'info';
                                    $text = 'Ditolak';
                                } else {
                                    if(empty($detail->realization_date) || $detail->realization_date == '0000-00-00'){
                                        if(empty($detail->plan_date) || $detail->plan_date == '0000-00-00'){
                                            $class = 'danger';
                                            $text = 'Pending';
                                        } else {
                                            $class = 'warning';
                                            $text = 'Terjadwal';
                                        }
                                    } else {
                                        $class = 'primary';
                                        $text = 'Terealisasi';
                                    }
                                }
                            @endphp
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-{{ $class }}">
                                        <span class="svg-icon svg-icon-2 svg-icon-white">
                                            {!! $detail->typeActivity->icon !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n2">
                                    <div class="overflow-auto pe-3">
                                        <div class="fs-5 fw-semibold mb-2">{{ $detail->typeActivity->name }}</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            @if(empty($detail->realization_date) || $detail->realization_date == '0000-00-00')
                                                <div class="text-muted me-2 fs-7">{{ !empty($detail->plan_date) && $detail->plan_date != '0000-00-00' ? $detail->plan_time != null ? setDateTime($detail->plan_date." ".$detail->plan_time, 'd F Y H:i') : setDate($detail->plan_date) : '' }}</div>
                                            @else
                                                <div class="text-muted me-2 fs-7">{{ $detail->realization_time != null ? setDateTime($detail->realization_date." ".$detail->realization_time, 'd F Y H:i') : setDate($detail->realization_date) }}</div>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center mt-3 fs-6">
                                            <a data-bs-toggle="modal"
                                               class="btn btn-icon btn-light-info w-25px h-25px me-2 btn-modal"
                                               title="Detail Data"
                                               data-url="{{ route(str_replace('/', '.', $menu_path).'.show-detail', $detail->id) }}">
                                                <i class="fa fa-search fs-7"></i>
                                            </a>
                                            <a data-bs-toggle="modal"
                                               class="btn btn-icon btn-light-success w-25px h-25px me-2 btn-modal"
                                               title="Edit Data"
                                               data-url="{{ route(str_replace('/', '.', $menu_path).'.edit-detail', $detail->id) }}">
                                                <i class="fa fa-pencil fs-7"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="approved-text"
                                     style="position: absolute; right: 0; top: 20%; transform: translateY(-50%); margin-right: 20px;">
                                    <span class="badge badge-{{ $class }}">
                                        {{ $text }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </form>
    </div>
    <x-views.delete-form/>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('input[name="type"]').on('change', function() {
                if($(this).val() === '2') {
                    $('#divPlan').removeClass('d-block').addClass('d-none');
                    $('#divActivity').removeClass('d-none').addClass('d-block');
                } else {
                    $('#divPlan').removeClass('d-none').addClass('d-block');
                    $('#divActivity').removeClass('d-block').addClass('d-none');
                }
            });

            $('#product_id').on('change', function() {
                var child = 'sub_product_id';
                let product_id = $(this).val();
                if (product_id) {
                    $.ajax({
                        url: '/psa/activities/sub-products/' + product_id,
                        type: "GET",
                        success: function (data) {
                            let childSelect = $('select[name=' + child + ']');
                            let firstOption = childSelect.find("option:first-child").text();
                            childSelect.empty().append('<option value=""> ' + firstOption + '</option>');

                            $.each(data, function (key, datas) {
                                $('select[name=' + child + ']').append('<option value="' + datas.id + '">' + datas.name + '</option>');
                            });

                            if(data.length > 0){
                                $('#subProduct').removeClass('d-none');
                            }else{
                                $('#subProduct').addClass('d-none');
                            }
                        }
                    });
                } else {
                    var firstOption = $('select[name=' + child + ']').find("option:first-child").text();
                    $('select[name=' + child + ']').empty();
                    $('select[name=' + child + ']').append('<option value=""> ' + firstOption + '</option>');
                }
            });
        });
    </script>
@endsection
