@extends('layouts.app')
@section('content')
    <div class="card mb-5">
        <div class="card-body">
            <form id="form-filter" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <x-form.select label="Periode" name="filterYear" :datas="$arrPeriods" :value="$filterYear"
                            event="document.getElementById('form-filter').submit()" />
                    </div>
                    @if (Auth::user()->role_id != 9)
                        @if (Auth::user()->role->name != 'Manager' && Auth::user()->role->name != 'Pemimpin Cabang')
                            <div class="col-md-4">
                                <x-form.select label="Cabang" name="filterBranch" option="- Semua Cabang -"
                                    :datas="$units" :value="$filterBranch"
                                    event="document.getElementById('form-filter').submit()" />
                            </div>
                        @endif
                        <div class="col-md-4">
                            <x-form.select label="Sales" name="filterAgent" option="- Semua Sales -" :datas="$agents"
                                :value="$filterAgent" event="document.getElementById('form-filter').submit()" />
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="row g-5 g-xl-5 mb-5 mb-xl-10">
        <div class="col-lg-6 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Grafik Sales Performance</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartSalesPerformance" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Grafik Sales Activity</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartSalesActivity" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Summary Target</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartSummaryTarget" style="height: 150px;" class="mb-5"></div>
                    <div class="table-responsive">
                        <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                            <thead>
                                <tr
                                    class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                                    <th class="text-center min-w-125px w-100">Target</th>
                                    <th class="min-w-200px text-center">Nilai Target</th>
                                    <th class="min-w-200px text-center">Realisasi</th>
                                    <th class="min-w-200px text-center">Progress</th>
                                    <th class="min-w-200px text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-end">{{ setCurrency($product->target ?? 0) }}</td>
                                        <td class="text-end">{{ setCurrency($product->realization ?? 0) }}</td>
                                        <td class="text-end">{{ decimalFormat($product->percentage ?? 0) }} %</td>
                                        <td class="text-center">
                                            <x-datatables.on-click-modal :route="route('psa.performance.target-detail', [
                                                'periodId' => $filterYear,
                                                'productId' => $product->id,
                                            ])" text="View" size="mw-950px" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Performa Sales</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div class="table-responsive">
                        <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                            <thead>
                                <tr
                                    class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                                    <th class="text-center min-w-50px">No</th>
                                    <th class="min-w-125px w-100">Name</th>
                                    <th class="min-w-200px">Kantor</th>
                                    <th class="min-w-125px">Success Rate</th>
                                    <th class="min-w-50px text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($agentData as $agent)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $agent->name }}</td>
                                        <td>{{ $agent->unit?->name }}</td>
                                        <td>{{ decimalFormat($agent->success_rate ?? 0) }} %</td>
                                        <td class="text-center">
                                            <x-datatables.on-click-redirect :route="route('psa.performance.agent-detail', [
                                                'periodId' => $filterYear,
                                                'agentId' => $agent->id,
                                            ])" text="View"
                                                size="mw-950px" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Performa Cabang</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div class="table-responsive">
                        <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                            <thead>
                                <tr
                                    class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                                    <th class="text-center min-w-50px">No</th>
                                    <th class="min-w-125px w-100">Name</th>
                                    <th class="min-w-125px">Success Rate</th>
                                    <th class="min-w-50px text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($unitData as $unit)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $unit->name }}</td>
                                        <td>{{ decimalFormat($unit->success_rate ?? 0) }} %</td>
                                        <td class="text-center">
                                            <x-datatables.on-click-redirect :route="route('psa.performance.unit-detail', [
                                                'periodId' => $filterYear,
                                                'unitId' => $unit->id,
                                            ])" text="View"
                                                size="mw-950px" />
                                        </td>
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
                series: [{
                    name: "Total Penjualan",
                    data: [
                        @for ($i = 1; $i <= 12; $i++)
                            {{ $arrMonthlySales[$i] ?? 0 }},
                        @endfor
                    ]
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                grid: {
                    padding: {
                        top: -20,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                yaxis: {
                    opposite: true,
                    labels: {
                        formatter: function(value) {
                            return 'Rp. ' + value.toLocaleString(); // Format y-axis labels as currency with "Rp."
                        }
                    }
                },
                legend: {
                    horizontalAlign: 'left'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return 'Rp. ' + value.toLocaleString(); // Format value as currency
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chartSalesPerformance"), options);
            chart.render();

            var options = {
                series: [{
                    name: "Total Penjualan",
                    data: [
                        @for ($i = 1; $i <= 12; $i++)
                            {{ $arrMonthlyPoint[$i] ?? 0 }},
                        @endfor
                    ]
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                grid: {
                    padding: {
                        top: -20,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                yaxis: {
                    opposite: true,
                    labels: {
                        formatter: function(value) {
                            return value.toLocaleString() + ' Point'; // Format y-axis labels as currency with "Rp."
                        }
                    }
                },
                colors: ['#ff1653'],
                legend: {
                    horizontalAlign: 'left'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return 'Rp. ' + value.toLocaleString(); // Format value as currency
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chartSalesActivity"), options);
            chart.render();

            var options = {
                series: [{
                    name: 'Target',
                    data: [100, 100],
                    originalData: [
                        {{ $targetUnitAccount }},
                        {{ $targetUnitPoint }}
                    ]
                }, {
                    name: 'Realisasi',
                    data: [
                        {{ $totalClosingAmount > 0 && $targetUnitAccount > 0 ? ($totalClosingAmount / $targetUnitAccount) * 100 : 0 }},
                        {{ $totalPoint > 0 && $targetUnitPoint > 0 ? ($totalPoint / $targetUnitPoint) * 100 : 0 }}
                    ],
                    originalData: [
                        {{ $totalClosingAmount }},
                        {{ $totalPoint }}
                    ]
                }, {
                    name: 'Delta Realisasi',
                    data: [
                        {{ abs($deltaAmount) > 0 && $targetUnitAccount > 0 ? ($deltaAmount / $targetUnitAccount) * 100 : 0 }},
                        {{ abs($deltaPoint) > 0 && $targetUnitPoint ? ($deltaPoint / $targetUnitPoint) * 100 : 0 }}
                    ],
                    originalData: [
                        {{ $deltaAmount }},
                        {{ $deltaPoint }}
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 430
                },
                colors: ['#3d9efa', '#3d5cfa', '#011E44'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetX: -50,
                    formatter: function(val, opts) {
                        var originalValue = opts.w.config.series[opts.seriesIndex].originalData[opts
                        .dataPointIndex];
                        return val.toFixed(1) + '% (' + originalValue.toLocaleString() + ')';
                    },
                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(value, opts) {
                            var originalValue = opts.w.config.series[opts.seriesIndex].originalData[opts
                                .dataPointIndex];
                            return originalValue.toLocaleString();
                        }
                    }
                },
                xaxis: {
                    categories: ['Sales Account', 'Sales Activity'],
                },
            };


            var chart = new ApexCharts(document.querySelector("#chartSummaryTarget"), options);
            chart.render();
        }

        window.addEventListener('load', function() {
            initChart();
        });
    </script>
@endsection
