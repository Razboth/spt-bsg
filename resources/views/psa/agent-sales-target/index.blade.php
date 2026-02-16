@extends('layouts.app')
@section('content')
    <div class="card mb-5">
        <div class="card-body">
            <div id="chart" style="height: 300px;"></div>
        </div>
    </div>
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-toolbar">
                    <div class="d-flex flex-wrap gap-2">
                        <x-form.select name="filterAgent" :datas="$agents" class="w-250px w-md-200px" :value="$filterAgent"
                                       event="document.getElementById('form-filter').submit();"/>
                        <x-form.select name="filterPeriod" :datas="$periods" class="w-250px w-md-200px" :value="$filterPeriod"
                                       event="document.getElementById('form-filter').submit();"/>
                        <x-form.select name="filterProduct" :datas="$products" class="w-250px w-md-200px" :value="$filterProduct"
                                       event="document.getElementById('form-filter').submit();"/>
                    </div>
                </div>
                <div class="card-toolbar">
                    <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.import') }}" class="me-3 bg-info text-white" icon="fa-solid fa-upload" text="Import Data" />
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th rowspan="2" class="min-w-200px w-100">Bulan</th>
                        <th colspan="2" class="text-center min-w-150px">Target</th>
                        <th colspan="2" class="text-center min-w-150px">Realisasi</th>
                        <th colspan="2" class="text-center min-w-150px">Delta Target</th>
                        <th rowspan="2" class="text-center min-w-50px">Kontrol</th>
                    </tr>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="text-center min-w-150px">Nilai</th>
                        <th class="text-center min-w-150px">Akumulasi</th>
                        <th class="text-center min-w-150px">Nilai</th>
                        <th class="text-center min-w-150px">Akumulasi</th>
                        <th class="text-center min-w-150px">Nilai</th>
                        <th class="text-center min-w-150px">Akumulasi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $accumulatedTarget = 0;
                        $accumulatedRealization = 0;
                        $accumulatedDelta = 0;
                        $totalTarget = $targetAgent?->total;
                        $monthFulfillment = 0;
                        $targetFulfillment = 0;
                        $realizationRemaining = 0;
                        $tempTotalRealization = $totalRealization;
                    @endphp
                    @for($i = 1; $i <= 12; $i++)
                        @php
                            $month = Str::padLeft($i, 2, 0);
                            $target = $targetAgent?->details->where('month', $month)->first()?->amount ?? 0;
                            $accumulatedTarget += $target;

                            $realization = $arrTotalRealization[$month] ?? 0;
                            $accumulatedRealization += $realization;

                            if($type == '1'){
                                if($totalRealization > 0){
                                    if($totalRealization > $target){
                                        $delta = 0;
                                        $totalRealization -= $target;
                                        $accumulatedDelta += $delta;
                                        $monthFulfillment ++;
                                    }else{
                                        $delta = (($totalTarget - $tempTotalRealization) / (12 - $monthFulfillment));
                                        $totalRealization = 0;
                                        $accumulatedDelta += $delta;
                                        $realizationRemaining = $target - $realization;
                                    }
                                }else{
                                    $delta = ($totalTarget - $tempTotalRealization) / (12 - $monthFulfillment);
                                    $accumulatedDelta += $delta;
                                }
                            } else {
                                $delta = $target - $realization;
                                $accumulatedDelta += $delta;
                            }
                        @endphp
                        <tr>
                            <td>{{ numToMonth($i) }}</td>
                            <td class="text-end">{{ setCurrency($target) }}</td>
                            <td class="text-end">{{ setCurrency($accumulatedTarget) }}</td>
                            <td class="text-end">{{ setCurrency($realization) }}</td>
                            <td class="text-end">{{ setCurrency($accumulatedRealization) }}</td>
                            <td class="text-end">{{ setCurrency($delta) }}</td>
                            <td class="text-end">{{ setCurrency($accumulatedDelta) }}</td>
                            <td>
                                <x-views.action
                                        menu_path="{{ $menu_path }}"
                                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.edit-batch', [$filterAgent, $filterPeriod, $filterProduct]) }}"
                                        is-modal="true"
                                />
                            </td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
@section('scripts')
    <script>
        var options = {
            series: [
                {
                    name: "Target",
                    data: [
                        @for($i= 1; $i<= 12; $i++)
                            @php
                                $month = Str::padLeft($i, 2, 0);
                                $target = $targetAgent?->details->where('month', $month)->first()?->amount ?? 0;
                            @endphp
                            '{{ $target }}',
                        @endfor
                    ]
                },
                {
                    name: "Realisasi",
                    data: [
                        @for($i= 1; $i<= 12; $i++)
                            @php
                                $month = Str::padLeft($i, 2, 0);
                                $realization = $arrTotalRealization[$month] ?? 0;
                            @endphp
                            '{{ $realization }}',
                        @endfor
                    ]
                },
            ],
            chart: {
                height: 350,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            title: {
                text: 'Grafik Target Penjualan Sales Account',
                align: 'left'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    @if($type == '1')
                    if (val >= 1000000000) {
                        return (val / 1000000000).toFixed(1) + ' miliar';
                    } else if (val >= 1000000){
                        // Jika tidak, bagi dengan 1 juta dan tambahkan 'juta'
                        return (val / 1000000) + ' juta';
                    } else if(val > 0){
                        return val + ' ribu';
                    }else{
                        return '';
                    }
                    @else
                    if (val > 0) {
                        return val
                    } else {
                        return '';
                    }
                    @endif
                }
            },
            stroke: {
                curve: 'straight'
            },
            grid: {
                padding: {
                    top: 0,
                },
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            yaxis: {
                title: {
                    text: 'Nilai'
                },
                min: 0,
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        window.onLoad = function () {
            chart.render();
        }
    </script>
@endsection