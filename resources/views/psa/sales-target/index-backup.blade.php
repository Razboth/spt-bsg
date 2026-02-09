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
                    <div class="d-flex">
                        <x-form.select name="filterUnit" :datas="$units" class="w-200px" :value="$filterUnit"
                                       event="document.getElementById('form-filter').submit();" />
                        <x-form.select name="filterPeriod" :datas="$periods" class="w-200px" :value="$filterPeriod"
                                       event="document.getElementById('form-filter').submit();" />
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="min-w-200px w-100">Produk</th>
                        <th class="text-center min-w-150px">Target Project</th>
                        @for($i = 1; $i <= 12; $i++)
                            <th class="text-center min-w-75px">{{ Str::substr(numToMonth($i), 0, 3) }}</th>
                        @endfor
                        <th class="text-center min-w-75px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $arrAmount = [];
                    @endphp
                    @foreach($products as $product)
                        @php
                            $targetUnit = $targetUnits->where('product_id', $product->id)->first();
                            $total = $targetUnit?->total ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-end">{{ setCurrency($total) }}</td>
                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $detail = $targetUnit?->details->where('month', Str::padLeft($i, 0, 2))->first();
                                    $amount = $detail?->amount ?? 0;
                                    $arrAmount[] = $detail?->amount ?? 0;
                                @endphp
                                <td class="text-end">{{ setCurrency($amount) }}</td>
                            @endfor
                            <td>
                                <x-views.action
                                        menu_path="{{ $menu_path }}"
                                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.edit-batch', [$filterUnit, $filterPeriod, $product->id]) }}"
                                        is-modal="true"
                                />
                            </td>
                        </tr>
                    @endforeach
                    @php
                        if(isset($arrAmount) && count($arrAmount) > 0){
                            $maxAmount = max($arrAmount) * 1.2;
                        }else{
                            $maxAmount = 0;
                        }
                    @endphp
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-modal-form />
@endsection
@section('scripts')
    <script>
        var options = {
            series: [
                @foreach($products as $key => $product)
                    @php
                        $targetUnit = $targetUnits->where('product_id', $product->id)->first();
                    @endphp
                    {
                        name: "{{ $product->name }}",
                        data: [
                            @for($i= 1; $i<= 12; $i++)
                                '{{ $targetUnit?->details->where('month', Str::padLeft($i, 0, 2))->first()?->amount ?? 0 }}',
                            @endfor
                        ]
                    },
                @endforeach
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
                    console.log(val);
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
                max: {{ $maxAmount }},
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

{{--        --}}{{--var options = {--}}
{{--        --}}{{--    series: [--}}
{{--        --}}{{--        @foreach($products as $key => $product)--}}
{{--        --}}{{--        @php--}}
{{--        --}}{{--            $targetUnit = $targetUnits->where('product_id', $product->id)->first();--}}
{{--        --}}{{--        @endphp--}}
{{--        --}}{{--        {--}}
{{--        --}}{{--            name: "{{ $product->name }}",--}}
{{--        --}}{{--            data: [--}}
{{--        --}}{{--                @for($i= 1; $i<= 12; $i++)--}}
{{--        --}}{{--                    '{{ $targetUnit?->details->where('month', Str::padLeft($i, 0, 2))->first()?->amount }}',--}}
{{--        --}}{{--                @endfor--}}
{{--        --}}{{--            ]--}}
{{--        --}}{{--        },--}}
{{--        --}}{{--        @endforeach--}}
{{--        --}}{{--    ],--}}
{{--        --}}{{--    chart: {--}}
{{--        --}}{{--        height: 350,--}}
{{--        --}}{{--        type: 'line',--}}
{{--        --}}{{--        dropShadow: {--}}
{{--        --}}{{--            enabled: true,--}}
{{--        --}}{{--            color: '#000',--}}
{{--        --}}{{--            top: 18,--}}
{{--        --}}{{--            left: 7,--}}
{{--        --}}{{--            blur: 10,--}}
{{--        --}}{{--            opacity: 0.2--}}
{{--        --}}{{--        },--}}
{{--        --}}{{--        toolbar: {--}}
{{--        --}}{{--            show: false--}}
{{--        --}}{{--        }--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    dataLabels: {--}}
{{--        --}}{{--        enabled: true,--}}
{{--        --}}{{--        formatter: function(val) {--}}
{{--        --}}{{--            if (val >= 1000000000) {--}}
{{--        --}}{{--                return (val / 1000000000).toFixed(1) + ' miliar';--}}
{{--        --}}{{--            } else if (val >= 1000000){--}}
{{--        --}}{{--                // Jika tidak, bagi dengan 1 juta dan tambahkan 'juta'--}}
{{--        --}}{{--                return (val / 1000000) + ' juta';--}}
{{--        --}}{{--            } else if(val > 0){--}}
{{--        --}}{{--                return val + ' ribu';--}}
{{--        --}}{{--            }else{--}}
{{--        --}}{{--                return '';--}}
{{--        --}}{{--            }--}}
{{--        --}}{{--        }--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    stroke: {--}}
{{--        --}}{{--        curve: 'smooth'--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    title: {--}}
{{--        --}}{{--        text: 'Grafik Target Penjualan',--}}
{{--        --}}{{--        align: 'left'--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    grid: {--}}
{{--        --}}{{--        borderColor: '#e7e7e7',--}}
{{--        --}}{{--        row: {--}}
{{--        --}}{{--            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns--}}
{{--        --}}{{--            opacity: 0.5--}}
{{--        --}}{{--        },--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    markers: {--}}
{{--        --}}{{--        size: 1--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    yaxis: {--}}
{{--        --}}{{--        title: {--}}
{{--        --}}{{--            text: 'Nilai'--}}
{{--        --}}{{--        },--}}
{{--        --}}{{--        min: 0,--}}
{{--        --}}{{--        max: {{ $maxAmount }},--}}
{{--        --}}{{--    },--}}
{{--        --}}{{--    xaxis: {--}}
{{--        --}}{{--        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],--}}
{{--        --}}{{--    }--}}
{{--        --}}{{--};--}}
{{--        --}}
{{--        --}}{{--var chart = new ApexCharts(document.querySelector("#chart"), options);--}}
{{--        --}}{{--chart.render();--}}

{{--        window.onLoad = function () {--}}
{{--            chart.render();--}}
{{--        }--}}
    </script>
@endsection