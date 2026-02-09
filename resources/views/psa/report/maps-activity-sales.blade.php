@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex flex-wrap">
                        <x-form.datepicker label="Filter Tanggal" name="filterDate" event="document.getElementById('form-filter').submit();" class="w-200px me-5" value="{{ $filterDate }}" />
                        <x-form.select label="Filter Cabang" name="filterBranch" option="- Semua Cabang -" :datas="$units" :value="$filterBranch" event="document.getElementById('form-filter').submit()" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body py-4">
            <div id="mapid"></div>
        </div>
    </div>
    <x-modal-form/>
    <style>
        #mapid {
            height: 700px
        }
    </style>
@endsection

@section('scripts')
    <!-- Link ke CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <!-- Script Leaflet -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Membuat peta dengan Leaflet
            var mymap = L.map('mapid').setView([1.4908055, 124.7998966], 5); // Pusat Indonesia dengan zoom level 4

            // Menambahkan tile layer dari OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(mymap);

            // Data lokasi marker (contoh lokasi di Indonesia)
            var locations = [
                    @foreach($logs as $log)
                {
                    lon: {{ $log['longitude'] }},
                    lat: {{ $log['latitude'] }},
                    title: '{{ $log['name'] }}',
                    address: '{{ $log['address'] }}'
                },
                @endforeach
            ];

            // Menambahkan marker di peta
            locations.forEach(function (location) {
                L.marker([location.lat, location.lon])
                    .addTo(mymap)
                    .bindPopup('<h3>' + location.title + '</h3><p>' + location.address + '</p>'); // Popup dengan nama dan alamat
            });
        });
    </script>
@endsection
