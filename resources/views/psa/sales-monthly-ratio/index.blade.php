@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        @if(Auth::user()->role->name != 'Sales' && Auth::user()->role->name != 'Manager')
                            <x-form.select name="setting_id" :value="$settingId" :datas="$settingProducts"
                                       class="w-250px w-md-250px" event="document.getElementById('form-filter').submit()"/>
                         @endif
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <x-views.filter-month-year name-month="filter_1" value-month="{{ date('m') }}" name-year="filter_2" value-year="{{ date('Y') }}" />
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered custom-border-bottom border border-white gy-5 gs-7">
                    <thead class="border border-white">
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        <th rowspan="3" class="min-w-50px text-center border-end ps-2">No</th>
                        @if(Auth::user()->role->name != 'Sales')
                            <th rowspan="3" class="min-w-250px text-center border-end">Sales</th>
                        @endif
                        <th rowspan="3" class="min-w-200px text-center border-end">Customer</th>
                        <th colspan="{{ $setting->details->count() * 3 }}" class="min-w-250px text-center border-end">Produk</th>
                        <th colspan="4" rowspan="2" class="min-w-250px text-center border-end">Total</th>
                    </tr>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        @foreach($setting->details as $detail)
                            <th class="min-w-225x text-center border-end" colspan="3">{{ $detail->product->name }}</th>
                        @endforeach
                    </tr>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        @foreach($setting->details as $detail)
                            <th class="min-w-75px text-center border-end">Awal</th>
                            <th class="min-w-75px text-center border-end">Akhir</th>
                            <th class="min-w-75px text-center border-end">Delta</th>
                        @endforeach
                        <th class="min-w-75px text-center border-end">Awal</th>
                        <th class="min-w-75px text-center border-end">Akhir</th>
                        <th class="min-w-75px text-center border-end">Delta</th>
                        <th class="min-w-75px text-center border-end">Average</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr class="text-start text-black fw-bold fs-7 border-top text-uppercase gs-0 align-middle">
                            @if(Auth::user()->role->name != 'Sales')
                                <td colspan="3">Total</td>
                            @else
                                <td colspan="2">Total</td>
                            @endif
                            @foreach ($setting->details as $detail)
                                <td id="total_balance_{{ $detail->product_id }}" class="text-end">0</td>
                                <td id="total_core_{{ $detail->product_id }}" class="text-end">0</td>
                                <td id="total_delta_{{ $detail->product_id }}" class="text-end">0</td>
                            @endforeach
                            <td id="total_balance_all" class="text-end">0</td>
                            <td id="total_core_all" class="text-end">0</td>
                            <td id="total_delta_all" class="text-end">0</td>
                            <td id="total_average_all" class="text-end">0</td>
                        </tr>

                    </tfoot>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data', $settingId);
                    if(Auth::user()->role->name != 'Sales'){
                        $datas = array("name", "customer_name");
                    }else{
                        $datas = array("customer_name");
                    }

                    foreach ($setting->details as $detail){
                        //create new column to array
                        $datas[] = "balance_".$detail->product_id."\ttext-end";
                        $datas[] = "core_".$detail->product_id."\ttext-end";
                        $datas[] = "delta_".$detail->product_id."\ttext-end";
                    }
//
                    $datas[] = "balance_total\ttext-end";
                    $datas[] = "core_total\ttext-end";
                    $datas[] = "delta_total\ttext-end";
                    $datas[] = "average_total\ttext-end";
                @endphp
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form />
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {

            let input_filter_timeout = null;
            let KTDatatablesServerSide = function () {
                let table;
                let dt;
                let datatables = $('#datatables');

                $('#datatables').on('draw.dt', function () {
                    refreshFsLightbox();
                });

                let initDatatable = function () {
                    $.ajaxSetup({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                    });

                    dt = $("#datatables").DataTable({
                        processing: true,
                        serverSide: true,
                        paginate: 'true',
                        ajax: {
                            url: '{{ $route }}',
                            method: 'POST'
                        },
                        order: [[1, 'asc']],
                        columnDefs: [
                            {targets: "_all", className: ''},
                        ],
                        columns: [
                            {
                                data: 'DT_RowIndex',
                                name: 'id',
                                searchable: false,
                                orderable: true,
                                className: 'text-center'
                            },
                                @foreach($datas as $key => $value)
                                @php
                                    $data = explode("\t", $value);
                                    $class = $data[1] ?? '';
                                @endphp
                            {
                                data: '{{ $data[0] }}',
                                name: '{{ $data[0] }}',
                                className: '{!! $class !!}',
                                orderable: {!! $data[2] ?? 'true' !!},
                            },
                            @endforeach
                        ],
                        footerCallback: function (row, data, start, end, display) {
                            // Reset all totals
                            @foreach($setting->details as $detail)
                            $('#total_balance_{{$detail->product_id}}').html('0');
                            $('#total_core_{{$detail->product_id}}').html('0');
                            $('#total_delta_{{$detail->product_id}}').html('0');
                            @endforeach
                            $('#total_balance_all').html('0');
                            $('#total_core_all').html('0');
                            $('#total_delta_all').html('0');
                            $('#total_average_all').html('0');

                            if (data.length === 0) return;

                            let totalBalanceAll = 0;
                            let totalCoreAll = 0;
                            let totalDeltaAll = 0;
                            let totalAverageAll = 0;

                            let productTotals = {};
                            @foreach($setting->details as $detail)
                                productTotals['{{$detail->product_id}}'] = {
                                balance: 0,
                                core: 0,
                                delta: 0
                            };
                                    @endforeach

                            for (let i = 0; i < data.length; i++) {
                                @foreach($setting->details as $detail)
                                    productTotals['{{$detail->product_id}}'].balance += parseFloat((data[i]['balance_{{$detail->product_id}}'] || '0').toString().replace(/,/g, ''));
                                productTotals['{{$detail->product_id}}'].core += parseFloat((data[i]['core_{{$detail->product_id}}'] || '0').toString().replace(/,/g, ''));
                                productTotals['{{$detail->product_id}}'].delta += parseFloat((data[i]['delta_{{$detail->product_id}}'] || '0').toString().replace(/,/g, ''));
                                @endforeach

                                totalBalanceAll += parseFloat((data[i]['balance_total'] || '0').toString().replace(/,/g, ''));
                                totalCoreAll += parseFloat((data[i]['core_total'] || '0').toString().replace(/,/g, ''));
                                totalDeltaAll += parseFloat((data[i]['delta_total'] || '0').toString().replace(/,/g, ''));
                                totalAverageAll += parseFloat((data[i]['average_total'] || '0').toString().replace(/,/g, ''));
                            }

                            @foreach($setting->details as $detail)
                            $('#total_balance_{{$detail->product_id}}').html(productTotals['{{$detail->product_id}}'].balance.toLocaleString('id-ID'));
                            $('#total_core_{{$detail->product_id}}').html(productTotals['{{$detail->product_id}}'].core.toLocaleString('id-ID'));
                            $('#total_delta_{{$detail->product_id}}').html(productTotals['{{$detail->product_id}}'].delta.toLocaleString('id-ID'));
                            @endforeach

                            $('#total_balance_all').html(totalBalanceAll.toLocaleString('id-ID'));
                            $('#total_core_all').html(totalCoreAll.toLocaleString('id-ID'));
                            $('#total_delta_all').html(totalDeltaAll.toLocaleString('id-ID'));
                            $('#total_average_all').html((totalAverageAll / data.length).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                    });
                    table = dt.$;
                }

                let handleSearchDatatable = function () {
                    const filterSearch = document.getElementById('filter');
                    filterSearch.addEventListener('keyup', function (e) {
                        clearTimeout();
                        input_filter_timeout = setTimeout(function () {
                            dt.search(e.target.value).draw();
                        }, 100);
                    });
                }

                $("#filter_1").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_1 = $("#filter_1").val();
                    }).DataTable().ajax.reload();
                });
                $("#filter_2").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_2 = $("#filter_2").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_3").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_3 = $("#filter_3").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_4").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_4 = $("#filter_4").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_5").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_5 = $("#filter_5").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_6").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_6 = $("#filter_6").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_7").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_7 = $("#filter_7").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_8").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_8 = $("#filter_8").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_9").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_9 = $("#filter_9").val();
                    }).DataTable().ajax.reload();
                });

                $("#filter_10").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.filter_10 = $("#filter_10").val();
                    }).DataTable().ajax.reload();
                });


                $("#combo_1").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_1 = $("#combo_1").val();
                    }).DataTable().ajax.reload();
                });
                $("#combo_2").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_2 = $("#combo_2").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_3").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_3 = $("#combo_3").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_4").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_4 = $("#combo_4").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_5").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_5 = $("#combo_5").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_6").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_6 = $("#combo_6").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_7").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_7 = $("#combo_7").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_8").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_8 = $("#combo_8").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_9").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_9 = $("#combo_9").val();
                    }).DataTable().ajax.reload();
                });

                $("#combo_10").on('change', function () {
                    datatables.on('preXhr.dt', function (e, settings, data) {
                        data.combo_10 = $("#combo_10").val();
                    }).DataTable().ajax.reload();
                });

                return {
                    init: function () {
                        initDatatable();
                        handleSearchDatatable();
                    }
                }
            }();

            // On document ready
            KTUtil.onDOMContentLoaded(function () {
                KTDatatablesServerSide.init();
            });
        });
    </script>
@endsection