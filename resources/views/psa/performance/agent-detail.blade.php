@extends('layouts.app')
@section('content')
    <div class="card mb-5">
        <div class="card-body">
            <form id="form-filter" method="GET">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                        <x-form.datepicker label="Tanggal Awal" name="filterStartDate" :value="$filterStartDate" class="w-100 mx-2" event="document.getElementById('form-filter').submit()" />
                        <x-form.datepicker label="Tanggal Akhir" name="filterEndDate" :value="$filterEndDate" class="w-100 mx-2" event="document.getElementById('form-filter').submit()" />
                    </div>
                    <div>
                        <a href="{{ route('psa.performance.index') }}" class="btn btn-light-primary me-3">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-5">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex justify-content-between">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        <img id="detail-photo" src="{{ $agent->photo ? asset('storage/'.$agent->photo) : asset('assets/media/blank-image.svg') }}" alt="image" />
                    </div>
                </div>
                <div class="d-flex align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" class="text-gray-900 text-light-primary fs-2 fw-bold me-5">{{ $agent->name }}</a>
                            <span class="badge badge-light-success">Aktif</span>
                        </div>
                        <div class="d-flex flex-wrap fw-semibold fs-6 pe-2">
                            <div class="d-flex align-items-center fw-bold me-5">
                                <span class="svg-icon svg-icon-4 me-1">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M16.5 9C16.5 13.125 13.125 16.5 9 16.5C4.875 16.5 1.5 13.125 1.5 9C1.5 4.875 4.875 1.5 9 1.5C13.125 1.5 16.5 4.875 16.5 9Z" fill="currentColor" />
                                        <path d="M9 16.5C10.95 16.5 12.75 15.75 14.025 14.55C13.425 12.675 11.4 11.25 9 11.25C6.6 11.25 4.57499 12.675 3.97499 14.55C5.24999 15.75 7.05 16.5 9 16.5Z" fill="currentColor" />
                                        <rect x="7" y="6" width="4" height="4" rx="2" fill="currentColor" />
                                    </svg>
                                </span>
                                {{ $agent->position->name ?? '' }}
                            </div>
                            <div class="d-flex align-items-center fw-bold me-5">
                                <span class="svg-icon svg-icon-4 me-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="currentColor" />
                                        <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="currentColor" />
                                    </svg>
                                </span>
                                {{ $agent->unit->name ?? '' }}
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="d-flex flex-column me-10">
                                <div class="text-gray-600 mt-5">Nomor Induk Karyawan</div>
                                <div class="fw-bold">{{ $agent->employee_number }}</div>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="text-gray-600 mt-5">Tanggal Masuk</div>
                                <div class="fw-bold">{{ setDate($agent->join_date, 'd F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="d-flex justify-content-end align-items-center fw-bold" style="font-size: 40px">
                    {{ decimalFormat($successRate) }}% Success Rate
                </div>
        </div>
    </div>
    <style>
        #detail-photo {
            object-fit: cover;
        }
    </style>
    </div>

    <div class="row g-5 g-xl-5 mb-5 mb-xl-10">
        <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Grafik Tahapan Aktifitas</span>
                        <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                    </h3>
                </div>
                <div class="card-body" style="padding-top: 0 !important;">
                    <div id="chartTypeActivity" style="height: 150px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
            <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Summary Point Activity</span>
                    <span class="text-gray-600 mt-1 fw-semibold fs-6">&nbsp;</span>
                </h3>
            </div>
            <div class="card-body" style="padding-top: 0 !important;">
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                        <thead>
                        <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="min-w-50px text-center">No</th>
                            <th class="min-w-125px text-center w-50">Produk</th>
                            <th class="min-w-125px text-center w-50">Customer</th>
                            <th class="min-w-200px text-center">Poin</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach($arrCustomerData as $customerId => $productArray)
                                @foreach($productArray as $productId => $value)
                                    @php
                                        $customer = $customers->where('id', $customerId)->first();
                                        $product = $products->where('id', $productId)->first();
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td class="text-center">{{ $value }}</td>
                                    </tr>
                                @endforeach
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

        function initChart(){
            var options = {
                series: [{
                    data: [
                        @foreach($types as $k => $value)
                            '{{ $arrActivity[$k] ?? 0 }}',
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
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    min: 0,
                    categories: [
                        @foreach($types as $k => $value)
                            '{{ $value }}',
                        @endforeach
                    ],
                },
                yaxis: {
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

            var chart = new ApexCharts(document.querySelector("#chartTypeActivity"), options);
            chart.render();
        }

        window.addEventListener('load', function(){
            initChart();
        });
    </script>
@endsection
