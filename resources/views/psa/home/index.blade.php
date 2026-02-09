@extends('layouts.app')
@section('content')
    @if ($isAlert)
        <div class="alert alert-danger d-flex align-items-center p-5">
            <!--begin::Icon-->
            <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span
                    class="path2"></span></i>
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column">
                <!--begin::Title-->
                <h4 class="mb-1 text-danger">Informasi</h4>
                <!--end::Title-->

                <!--begin::Content-->
                <span>Penuhi penjualan minimal 10% untuk semua produk untuk mendapatkan insentif.</span>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->
        </div>
    @endif
    <div class="card mb-5">
        <div class="card-body">
            <form id="form-filter" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <x-form.select label="Periode" name="filterYear" :datas="$arrPeriods" :value="$filterYear"
                            event="document.getElementById('form-filter').submit()" />
                    </div>
                    <div class="col-md-2">
                        <x-form.select label="Bulan" name="filterMonth" :datas="$arrMonth" :value="$filterMonth"
                            event="document.getElementById('form-filter').submit()" />
                    </div>
                    @if (Auth::user()->role_id != 9)
                        @if (Auth::user()->role->name != 'Manager' && Auth::user()->role->name != 'Pemimpin Cabang')
                            <div class="col-md-2">
                                <x-form.select label="Cabang" name="filterBranch" option="- Semua Cabang -"
                                    :datas="$units" :value="$filterBranch"
                                    event="document.getElementById('form-filter').submit()" />
                            </div>
                        @endif
                        <div class="col-md-3">
                            <x-form.select label="Sales" name="filterAgent" option="- Semua Sales -" :datas="$agents"
                                :value="$filterAgent" event="document.getElementById('form-filter').submit()" />
                        </div>
                    @endif
                    @if (Auth::user()->role_id != 9 && Auth::user()->role->name != 'Manager')
                        <div class="col-md-3">
                            <x-form.select label="Kategori Posisi" name="filterSetting" option="- Semua Kategori Posisi -"
                                :datas="$settingPositions" :value="$filterSetting"
                                event="document.getElementById('form-filter').submit()" />
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="row g-5 g-xl-5 mb-5 mb-xl-10">
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body">
                    <div id="" class="rounded bg-gray-100">
                        <div class="d-flex flex-column p-5">
                            <div class="d-flex align-content-center align-items-center mb-2">
                                <img src="{{ asset('assets/media/icons/wallet-icon.png') }}" class="w-50px h-50px me-3" />
                                <span class="fs-6 text-black fw-bold">
                                    PERFORMANCE SALES
                                </span>
                            </div>
                            <span class="fs-8 text-black fw-bold mb-1">
                                Target
                            </span>
                            <span class="fs-4 text-black fw-semibold mb-3">
                                Rp. {{ setCurrency($data['totalTargetAccount']) }}
                            </span>
                            <span class="fs-8 text-black fw-bold mb-1">
                                Realisasi
                            </span>
                            <span class="fs-4 text-black fw-semibold mb-3">
                                Rp. {{ setCurrency($data['totalClosingAmount']) }}
                            </span>
                            <span class="fs-8 text-black fw-bold mb-1">
                                Delta Realisasi
                            </span>
                            <span class="fs-4 text-black fw-semibold">
                                Rp. {{ setCurrency($data['deltaAccount']) }}
                            </span>
                            <div class="d-flex align-items-center flex-column w-100">
                                <div
                                    class="d-flex justify-content-end fw-bold fs-6 text-black opacity-75 w-100 mt-auto mb-2">
                                    <span>{{ $data['percentageAccount'] }}%</span>
                                </div>

                                <div class="h-8px mx-3 w-100 rounded" style="background-color: #b3b3b4">
                                    <div class="rounded h-8px" role="progressbar"
                                        style="width: {{ $data['percentageAccount'] > 100 ? 100 : $data['percentageAccount'] }}%; background-color: #ff7900"
                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body">
                    <div id="" class="rounded bg-gray-100">
                        <div class="d-flex flex-column p-5">
                            <div class="d-flex align-content-center align-items-center mb-2">
                                <img src="{{ asset('assets/media/icons/todo-icons.png') }}" class="w-50px h-50px me-3" />
                                <span class="fs-6 text-black fw-bold">
                                    PERFORMANCE ACTIVITY SALES
                                </span>
                            </div>
                            <span class="fs-8 text-black fw-bold mb-1">
                                Target
                            </span>
                            <span class="fs-4 text-black fw-semibold mb-3">
                                {{ $data['totalTargetPoint'] }}
                            </span>
                            <span class="fs-8 text-black fw-bold mb-1">
                                Realisasi
                            </span>
                            <span class="fs-4 text-black fw-semibold mb-3">
                                {{ $data['totalPoint'] }}
                            </span>
                            <span class="fs-8 text-black fw-bold mb-1">
                                Delta Realisasi
                            </span>
                            <span class="fs-4 text-black fw-semibold">
                                {{ $data['deltaPoint'] }}
                            </span>
                            <div class="d-flex align-items-center flex-column w-100">
                                <div
                                    class="d-flex justify-content-end fw-bold fs-6 text-black opacity-75 w-100 mt-auto mb-2">
                                    <span>{{ $data['percentagePoint'] }}%</span>
                                </div>

                                <div class="h-8px mx-3 w-100 rounded" style="background-color: #b3b3b4">
                                    <div class="rounded h-8px" role="progressbar"
                                        style="width: {{ $data['percentagePoint'] > 100 ? 100 : $data['percentagePoint'] }}%; background-color: #ff7900"
                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12 col-xl-4 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-body">
                    <div id="spark2" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        @if ($data['score'] > 0)
            <div class="col-lg-2 col-xl-2 mb-5 mb-xl-0">
                <div class="card">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Score SPT Bulan Ini</span>
                            <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center" style="height: 415px">
                            <span class="fw-bold" style="font-size: 80px; color: red">{{ $data['score'] ?? 0 }}</span>
                            <span class="fw-bold"
                                style="font-size: 20px; font-style: italic; color: black">({{ $data['percentageAccount'] }}%)</span>
                            <div class="mb-20"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-lg-12 col-xl-{{ $data['score'] > 0 ? '5' : '6'}} mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Performa Sales</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="chartSalesPerformance" style="height: 130px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-{{ $data['score'] > 0 ? '5' : '6'}} mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Performa per Produk </span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartProductPerformance" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Top 5 Sales</span>
                </h3>
            </div>
            <div class="card-body mt-0 pt-0">
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                        <thead>
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="text-center min-w-25px">No</th>
                            <th class="min-w-200px text-center w-100">Nama</th>
                            <th class="text-center min-w-100px text-center">Penjualan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($topAgents as $k => $value)
                            @php
                                $agent = $agentData->where('id', $k)->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $agent->name }}</td>
                                <td>{{ $value }}%</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Top 5 Unit Kerja</span>
                </h3>
            </div>
            <div class="card-body mt-0 pt-0">
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                        <thead>
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="text-center min-w-25px">No</th>
                            <th class="min-w-200px text-center w-100">Nama</th>
                            <th class="text-center min-w-100px text-center">Penjualan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($topUnits as $k => $value)
                            @php
                                $unit = $unitData->where('id', $k)->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $unit->name }}</td>
                                <td>{{ $value }}%</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Leadeboard Keseluruhan</span>
                </h3>
            </div>
            <div class="card-body mt-0 pt-0">
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                        <thead>
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="text-center min-w-25px">No</th>
                            <th class="min-w-200px text-center w-100">Nama</th>
                            <th class="text-center min-w-100px text-center">Penjualan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($topCategories as $k => $value)
                            @php
                                $setting = $settingProducts->where('id', $k)->first();
                                if($value == 0){
                                    continue;
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $setting->name }}</td>
                                <td>{{ $value }}%</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    <script>
        @php
            $colors = ['#011E44', '#FDB414', '#7db9f4', '#316da8', '#9b384d', '#9dd9b0', '#e78499', '#518d64', '#9776ea', '#4a2a9d', '#d3d3d3', '#f5f5dc'];
        @endphp

        let colors = @json($colors);

        function initChart() {
            var options = {
                chart: {
                    id: 'sparkline1',
                    type: 'area',
                    height: 160,
                    sparkline: {
                        enabled: true
                    },
                },
                stroke: {
                    curve: 'straight'
                },
                fill: {
                    opacity: 1,
                },
                series: [{
                    name: 'Penjualan',
                    data: [20, 50, 60, 70, 12, 50]
                }],
                labels: [...Array(5).keys()].map(n => `2018-09-0${n + 1}`),
                yaxis: {
                    min: 0
                },
                xaxis: {
                    type: 'datetime',
                },
                colors: ['#008FFB'],
                title: {
                    text: 'Rp. {{ setCurrency($data['totalClosingAmount']) }}',
                    offsetX: 30,
                    style: {
                        fontSize: '24px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                },
                subtitle: {
                    text: 'Performance Sales',
                    offsetX: 30,
                    style: {
                        fontSize: '14px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                }
            }

            new ApexCharts(document.querySelector("#spark1"), options).render();

            var options = {
                chart: {
                    id: 'sparkline2',
                    type: 'area',
                    height: 280,
                    sparkline: {
                        enabled: true
                    },
                },
                stroke: {
                    curve: 'straight'
                },
                fill: {
                    opacity: 1,
                },
                series: [{
                    name: 'Nasabah',
                    data: [10, 3, 5, 2, 5, 6]
                }],
                labels: [...Array(5).keys()].map(n => `2018-09-0${n + 1}`),
                yaxis: {
                    min: 0
                },
                xaxis: {
                    type: 'datetime',
                },
                colors: ['#FE7900'],
                title: {
                    text: '{{ $data['totalCustomer'] }}',
                    offsetX: 30,
                    style: {
                        fontSize: '24px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                },
                subtitle: {
                    text: 'Total Nasabah',
                    offsetX: 30,
                    style: {
                        fontSize: '14px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                }
            }

            new ApexCharts(document.querySelector("#spark2"), options).render();

            var options = {
                chart: {
                    id: 'sparkline3',
                    type: 'area',
                    height: 160,
                    sparkline: {
                        enabled: true
                    },
                },
                stroke: {
                    curve: 'straight'
                },
                fill: {
                    opacity: 1,
                },
                series: [{
                    name: 'KPI',
                    data: [10, 3, 5, 2, 5, 6]
                }],
                labels: [...Array(5).keys()].map(n => `2018-09-0${n + 1}`),
                yaxis: {
                    min: 0
                },
                xaxis: {
                    type: 'datetime',
                },
                colors: ['#49cfe4'],
                title: {
                    text: '{{ $data['totalPoint'] }}',
                    offsetX: 30,
                    style: {
                        fontSize: '24px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                },
                subtitle: {
                    text: 'Activity Sales',
                    offsetX: 30,
                    style: {
                        fontSize: '14px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                }
            }

            new ApexCharts(document.querySelector("#spark3"), options).render();

            var options = {
                series: [{{ $data['percentageAccount'] ?? 1 }}, {{ $data['percentagePoint'] ?? 1 }}],
                chart: {
                    height: 450,
                    type: 'radialBar',
                },
                grid: {
                    padding: {
                        top: -30,
                    }
                },
                legend: {
                    position: 'bottom',
                    fontWeight: 600,
                    show: true,
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: true,
                                label: 'Sales',
                                formatter: function(w) {}
                            }
                        }
                    }
                },
                labels: ['Penjualan', 'Aktifitas'],
            };

            var chart = new ApexCharts(document.querySelector("#chartSalesPerformance"), options);
            chart.render();

            var options = {
                series: [{
                    data: [
                        @foreach ($products as $k => $value)
                            {{ decimalFormat($data['arrProductPercentage'][$k]) }},
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 440,
                    toolbar: {
                        show: false
                    },
                    parentHeightOffset: 0,
                },
                plotOptions: {
                    bar: {
                        columnWidth: '80%',
                        distributed: true,
                        dataLabels: {
                            position: 'top'
                        },
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: true,
                    textAnchor: 'middle',
                    style: {
                        colors: ['#000']
                    },
                    offsetY: -20,
                    formatter: function(val, opt) {
                        return val + ' %'
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: [
                        @foreach ($products as $k => $value)
                            '{{ $value }}',
                        @endforeach
                    ],
                    labels: {
                        show: false
                    },
                },
                yaxis: {
                    labels: {
                        show: true
                    }
                },
            };

            var chart = new ApexCharts(document.querySelector("#chartProductPerformance"), options);
            chart.render();

        }

        window.addEventListener('load', function() {
            initChart();
        });
    </script>
@endsection
