@extends('layouts.app')
@section('content')
    <div class="card mb-5">
        <div class="card-body">
            <form id="form-filter" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <x-form.select label="Periode" name="filterYear" :datas="$arrPeriods" :value="$filterYear"
                                       event="document.getElementById('form-filter').submit()"/>
                    </div>
                    @if(Auth::user()->role_id != 9)
                        @if(Auth::user()->role->name != 'Manager' && Auth::user()->role->name != 'Pemimpin Cabang')
                            <div class="col-md-3">
                                <x-form.select label="Cabang" name="filterBranch" option="- Semua Cabang -"
                                               :datas="$units"
                                               :value="$filterBranch"
                                               event="document.getElementById('form-filter').submit()"/>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <x-form.select label="Sales" name="filterAgent" option="- Semua Sales -" :datas="$agents"
                                           :value="$filterAgent"
                                           event="document.getElementById('form-filter').submit()"/>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="row g-5 g-xl-5 mb-5 mb-xl-10">
       
        <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Performa Sales</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-6">
                            <div id="chartProgressSales" style="height: 130px;"></div>
                        </div>
                        <div class="col-md-6 justify-content-center align-items-center">
                            <p class="fs-3 fw-bold">Rp. {{ setCurrency($data['totalClosingAmount']) }}</p>
                            <p class="fs-7 fw-semibold">Total Penjualan Anda sampai saat ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Realisasi per Produk </span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartProduct" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Realisasi per Booster </span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartBooster" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Report Realisasi semua Produk </span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartReportProduct" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Report Aktivitas Closing</span>
                    </h3>
                </div>
                <div class="card-body mt-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                            <thead>
                            <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                                <th class="text-center min-w-175px">Tanggal</th>
                                <th class="min-w-200px w-100">Sales</th>
                                <th class="text-center min-w-250px">Produk</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($closingActivities as $activity)
                                <tr>
                                    <td>{{ setDate($activity->realization_date, 'd F Y') }}</td>
                                    <td>{{ $activity->activity->agent->name }}</td>
                                    <td>{{ $activity->activity->product->name }}</td>
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
                        <span class="card-label fw-bold text-dark">Aktivitas Terbaru</span>
                    </h3>
                </div>
                <div class="card-body mt-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                            <thead>
                            <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                                <th class="text-center min-w-175px">Tanggal</th>
                                <th class="min-w-200px w-100">Sales</th>
                                <th class="text-center min-w-250px">Produk</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($latestActivities as $activity)
                                <tr>
                                    <td>{{ setDate($activity->realization_date, 'd F Y') }}</td>
                                    <td>{{ $activity->activity->agent->name }}</td>
                                    <td>{{ $activity->activity->product->name }}</td>
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
                series: [{{ $data['percentageAccount'] }}],
                chart: {
                    height: 280,
                    type: 'radialBar',
                },
                grid: {
                    padding: {
                        top: -20,
                    }
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '70%',
                        }
                    },
                },
                labels: ['Penjualan'],
            };

            var chart = new ApexCharts(document.querySelector("#chartProgressSales"), options);
            chart.render();

            var options = {
                series: [{
                    data: [
                        @foreach($products as $k => $value)
                                {{ isset($data['arrProductPerformance'][$k]) && $data['arrProductPerformance'][$k] > 0 ? decimalFormat($data['arrProductPerformance'][$k] / 100000000) : 0 }},
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                grid: {
                    padding: {
                        top: -20,
                    }
                },
                plotOptions: {
                    bar: {
                        barHeight: '80%',
                        distributed: true,
                        horizontal: true,
                        dataLabels: {
                            position: 'bottom'
                        },
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        colors: ['#fff']
                    },
                    formatter: function (val, opt) {
                        return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
                    },
                    offsetX: 0,
                    dropShadow: {
                        enabled: true
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: [
                        @foreach($products as $value)
                            '{{ $value }}',
                        @endforeach
                    ],
                },
                yaxis: {
                    title: {
                        text: 'Rp (Juta)'
                    },
                    labels: {
                        show: false
                    }
                },
                tooltip: {
                    theme: 'dark',
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function () {
                                return ''
                            }
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chartProduct"), options);
            chart.render();

            var options = {
                series: [{
                    data: [
                        @foreach($boosters as $value)
                                {{ isset($data['arrBoosterPerformance'][$value->id]) && $data['arrBoosterPerformance'][$value->id] > 0 ? $data['arrBoosterPerformance'][$value->id] : 0 }},
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                grid: {
                    padding: {
                        top: -20,
                    }
                },
                plotOptions: {
                    bar: {
                        barHeight: '80%',
                        distributed: true,
                        horizontal: false,
                        dataLabels: {
                            position: 'top'
                        },
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        colors: ['#fff'],
                        fontSize: '16px' // Change this value as needed

                    },
                    offsetY: -30,
                    formatter: function (val, opt) {
                        return val;
                    },
                    dropShadow: {
                        enabled: true
                    },
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: [
                        @foreach($boosters as $value)
                            '{{ $value->name }}',
                        @endforeach
                    ],
                    labels: {
                        show: false
                    },
                },
                yaxis: {
                    title: {
                        text: 'Jumlah'
                    },
                    labels: {
                        show: false
                    }
                },
                tooltip: {
                    theme: 'dark',
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function () {
                                return ''
                            }
                        }
                    }
                },
                maximum: 100,
            };

            var chart = new ApexCharts(document.querySelector("#chartBooster"), options);
            chart.render();

            var options = {
                series: [{
                    name: "Total Penjualan",
                    data: [
                        @for($i = 1; $i <= 12; $i++)
                                {{ $data['arrMonthlySales'][$i] ?? 0 }},
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
                        formatter: function (value) {
                            return 'Rp. ' + value.toLocaleString(); // Format y-axis labels as currency with "Rp."
                        }
                    }
                },
                legend: {
                    horizontalAlign: 'left'
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return 'Rp. ' + value.toLocaleString(); // Format value as currency
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chartReportProduct"), options);
            chart.render();

        }

        window.addEventListener('load', function () {
            initChart();
        });
    </script>
@endsection
