@props(['datas', 'route', 'defOrder' => '', 'defOrderStatus' => 'asc', 'classDefault' => '', 'classFirstColumn' => '', 'method' => 'GET', 'paginate' => true])
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            let input_filter_timeout=null;
            let KTDatatablesServerSide = function () {
                let table;
                let dt;
                let datatables = $('#datatables');

                $('#datatables').on('draw.dt', function() {
                    refreshFsLightbox();
                });

                let initDatatable = function () {
                    @if($method == 'POST')
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    @endif
                    dt = $("#datatables").DataTable({
                        processing: true,
                        serverSide: true,
                        paginate: {{ $paginate ? 'true' : 'false' }},
                        ajax: {
                            url: '{{ $route }}',
                            method: '{{ $method }}',
                            // pangkas metadata kolom DataTables (searchable/orderable/search) agar payload kecil
                            data: function (d) {
                                d.columns = d.columns.map(function (c) {
                                    return (c.name && c.name !== c.data) ? {data: c.data, name: c.name} : {data: c.data};
                                });
                                return d;
                            }
                        },
                        order: [[ <?= $defOrder ?>, '{!! $defOrderStatus !!}' ]],
                        columnDefs: [
                            { targets: "_all", className: '{{ $classDefault }}'},
                        ],
                        columns: [
                            { data: 'DT_RowIndex', name: 'id', searchable: false, orderable:true, className: 'text-center {{ $classFirstColumn }}' },
                            @foreach($datas as $key => $value)
                                @php
                                    $data = explode("\t", $value);
                                    $class = $data[1] ?? '';

                                    if(str_contains($classDefault, 'border-end')){
                                        // Cek apakah $key adalah kunci terakhir
                                        $isLast = $key === key(array_slice($datas, -1, 1, true));
                                        $class = $isLast ? $class.' pe-3' : $class;
                                    }
                                @endphp
                                { data: '{{ $data[0] }}', name: '{{ $data[0] }}', className: '{!! $class !!}', orderable: {!! $data[2] ?? 'true' !!},  },
                            @endforeach
                        ],
                    });
                    table = dt.$;
                }

                let handleSearchDatatable = function () {
                    const filterSearch = document.getElementById('filter');
                    filterSearch.addEventListener('keyup', function (e) {
                        clearTimeout();
                        input_filter_timeout=setTimeout(function() {
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
