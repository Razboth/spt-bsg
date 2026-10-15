@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path) . '.update', [$agent->id, $customer->id, $date]) }}">
            @csrf
            @method('PATCH')
            <x-form.header title="Form Sales Ratio" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Tanggal" :value="setDate($date)" class="w-100 bg-gray-100" readonly/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Customer" :value="$customer->name" class="bg-gray-100" readonly/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Cabang" :value="$agent->unit->name" class="bg-gray-100" readonly/>
                        <input type="hidden" name="unit_id" value="{{ $agent->unit_id }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Sales" :value="$agent->name" class="bg-gray-100" readonly/>
                    </div>
                </div>
                <div class="row">
                    @foreach($products as $key => $product)
                        @php
                            $balance = $salesRatios->where('product_id', $key)->first()?->nominal ?? 0;
                        @endphp
                        <div class="col-md-6">
                            <div class="mb-5">
                                <h5 class="text-gray-900 fw-semibold">{{ $product }}</h5>
                                <div style="width: 100px; height: 2px;" class="bg-primary"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.input label="Saldo" placeholder="Masukkan Core" :value="$balance ?? 0" currency class="bg-gray-100" readonly/>
                                </div>
                                <div class="col-md-6">
                                    <x-form.input label="Core" placeholder="Masukkan Core" name="nominal[{{ $key }}]" :value="$salesRatio->nominal ?? 0" currency/>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
    <x-views.delete-form/>
@endsection
