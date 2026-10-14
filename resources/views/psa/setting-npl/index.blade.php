@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-views.filter-year name="filterYear" event="document.getElementById('form-filter').submit();" class="w-150px" :value="$filterYear" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables"
                       class="table table-rounded table-row-bordered custom-border-bottom border border-white gy-5 gs-7">
                    <thead class="border border-white">
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        <th rowspan="3" class="min-w-50px text-center ps-2">No</th>
                        <th rowspan="3" class="min-w-200px text-center">Cabang</th>
                        <th colspan="24" class="min-w-300px w-50 text-center">Setting NPL {{ $filterYear }}</th>
                        <th rowspan="3" class="min-w-100px text-center">Kontrol</th>
                    </tr>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        @for($i = 1; $i <= 12; $i++)
                            <th class="min-w-50px text-center" colspan="2">{{ numToMonth($i, true) }}</th>
                        @endfor
                    </tr>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border border-white">
                        @for($i = 1; $i <= 12; $i++)
                            <th class="min-w-50px text-center">T</th>
                            <th class="min-w-50px text-center">A</th>
                        @endfor
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7 border border-gray-300">
                    </tbody>
                </table>
            </div>
            @php
                $route = route(Str::replace('/', '.', $menu_path).'.data');
                $datas = array(
                "name",
                );
                for ($i = 1; $i <= 12; $i++) {
                    $datas[] = "target_".$i."\ttext-end";
                    $datas[] = "realization_".$i."\ttext-end";
                }
                $datas[] = "control";
            @endphp
            <x-views.datatables method="POST" :datas="$datas" :route="$route" def-order="1" class-default="border-end"
                                class-first-column="ps-2"/>
        </div>
    </div>
    <x-modal-form size="mw-950px" />
@endsection