@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter" class="d-flex">
                    <div class="d-flex flex-wrap gap-2">
                        <x-views.search/>
                        <x-form.select name="combo_3" :datas="$settingProducts" option="- Semua Kelompok -"
                                           class="w-200px"
                                           event="resetFilter();getSub(this.value,'combo_5', 'products-by-setting');getSub(this.value,'combo_4', 'sales-by-setting')"/>
                        <x-form.select name="combo_4" :datas="$sales" option="- Semua Sales -" class="w-200px"/>
                    </div>
                </form>
            </div>
            <div class="card-toolbar mt-3 mt-md-0">
                <x-views.filter-month-year nameMonth="combo_1" :valueMonth="date('m')" nameYear="combo_2" :valueYear="date('Y')" :all="true" class="me-3" />
                <a class="btnApprove btn btn-primary d-none me-2" data-type="1">
                    <i class="fa-solid fa-pencil"></i> Approve 1 All
                </a>
                <a class="btnApprove btn btn-primary d-none" data-type="2">
                    <i class="fa-solid fa-pencil"></i> Approve 2 All
                </a>
            </div>
        </div>

        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="min-w-50px">
                            <div class="form-check form-check-custom form-check-solid d-flex justify-content-center align-items-center">
                                <input class="form-check-input" id="checkAll" type="checkbox">
                            </div>
                        </th>
                        <th class="min-w-100px">Tanggal</th>
                        <th class="min-w-150px w-100">Sales</th>
                        <th class="min-w-200px">Customer</th>
                        <th class="min-w-150px">Produk</th>
                        <th class="min-w-100px">Appr 1</th>
                        <th class="min-w-100px">Appr 2</th>
                        <th class="min-w-100px">Detail</th>
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7"></tbody>
                </table>

                @php
                    $config = [
                        'method' => 'POST',
                        'route' => route(str_replace('/', '.', $menu_path).'.data'),
                        'columns' => [
                            "check\ttext-center\tfalse",
                            "date\ttext-center\tfalse",
                            "agent_id",
                            "customer_id",
                            "product_id",
                            "approved_status",
                            "approved_2_status",
                            "detail\ttext-center\tfalse",
                        ],
                        'defaultOrderDir' => 'asc',
                        'paginate' => true,
                        'classDefault' => '',
                        'classFirstColumn' => ''
                    ];
                @endphp
                <x-views.delete-form/>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
         function resetFilter() {
                $('#combo_4').val('').trigger('change'); // Optional, to trigger change event for other dependent UI elements
                $('#combo_5').val('').trigger('change'); // Optional, to trigger change event for other dependent UI elements

                console.log('test')
            }
        $(document).ready(function () {
            const KTDatatablesServerSide = (() => {
                const datatables = $('#datatables');
                let dt, searchTimeout;

                // CSRF setup for POST requests
                @if($config['method'] === 'POST')
                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });
                @endif

                // Initialize DataTable
                const initDatatable = () => {
                    dt = datatables.DataTable({
                        processing: true,
                        serverSide: true,
                        paginate: {{ $config['paginate'] ? 'true' : 'false' }},
                        ajax: {
                            url: '{{ $config['route'] }}',
                            method: '{{ $config['method'] }}',
                            // pangkas metadata kolom DataTables (searchable/orderable/search) agar payload kecil
                            data: function (d) {
                                d.columns = d.columns.map(function (c) {
                                    return (c.name && c.name !== c.data) ? {data: c.data, name: c.name} : {data: c.data};
                                });
                                return d;
                            }
                        },
                        order: [[5, 'asc'], [6, 'asc'], [1, 'asc']],
                        columnDefs: [{targets: "_all", className: '{{ $config['classDefault'] }}'}],
                        columns: [
                                @foreach($config['columns'] as $column)
                                    @php
                                        $parts = explode("\t", $column);
                                        $class = $parts[1] ?? '';
                                        $orderable = $parts[2] ?? 'true';
                                    @endphp
                            {
                                data: '{{ $parts[0] }}',
                                name: '{{ $parts[0] }}',
                                className: '{{ $class }}',
                                orderable: {{ $orderable }}
                            },
                            @endforeach
                        ]
                    });

                    datatables.on('draw.dt', refreshFsLightbox);
                };

                // Handle search with debounce
                const handleSearch = () => {
                    $('#filter').on('keyup', function (e) {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => dt.search(e.target.value).draw(), 100);
                    });
                };

                // Filter handler factory
                const createFilterHandler = (id) => {
                    $(`#${id}`).on('change', function () {
                        datatables.on('preXhr.dt', (e, settings, data) => {
                            data[id] = this.value;
                        }).DataTable().ajax.reload();
                    });
                };

                // Initialize filters and combos
                const initFilters = () => {
                    for (let i = 1; i <= 10; i++) {
                        createFilterHandler(`filter_${i}`);
                        createFilterHandler(`combo_${i}`);
                    }
                };

                return {
                    init: () => {
                        initDatatable();
                        handleSearch();
                        initFilters();
                    }
                };
            })();

            // Initialize on DOM content loaded
            KTUtil.onDOMContentLoaded(KTDatatablesServerSide.init);

            $('#checkAll').on('click', function () {
                $('.check-item').prop('checked', this.checked);

                if (this.checked) {
                    $('.btnApprove').removeClass('d-none');
                } else {
                    $('.btnApprove').addClass('d-none');
                }
            });

            $(document).on('change', '.check-item', function () {
                //count check item checked
                let countChecked = $('.check-item:checked').length;
                if (countChecked > 0) {
                    $('.btnApprove').removeClass('d-none');
                } else {
                    $('.btnApprove').addClass('d-none');
                }
            });

            $('.btnApprove').on('click', function () {
                let type = $(this).data('type');
                // Ambil semua checkbox yang dicentang dan gabungkan nilainya dengan koma
                let ids = $('.check-item:checked').map(function () {
                    return this.value;
                }).get().join(',');

                // SweetAlert2 untuk konfirmasi sebelum mengirim AJAX
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dipilih akan disetujui!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika dikonfirmasi, jalankan AJAX request
                        $.ajax({
                            url: '{{ route(str_replace("/", ".", $menu_path).".approve-batch") }}',
                            type: 'POST',
                            data: {
                                ids: ids,
                                type: type,
                            },
                            success: function (data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: 'Data berhasil di-approve'
                                });

                                // Reload DataTables setelah sukses
                                $('#datatables').DataTable().ajax.reload();

                                // Reset check all
                                $('#checkAll').prop('checked', false);
                                //hide button approve
                                $('.btnApprove').addClass('d-none');
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan, coba lagi nanti!'
                                });
                            }
                        });
                    }
                })
            });

        })
    </script>
@endsection