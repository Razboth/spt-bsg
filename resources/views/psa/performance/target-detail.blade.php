<div class="card">
    <x-form.modal-header title="Detail Target"/>
    <div class="separator mt-2 mb-5 d-flex"></div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                <thead>
                <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                    <th class="min-w-50px text-center ">No</th>
                    <th class="min-w-150px">Tanggal</th>
                    <th class="min-w-175px ">Produk</th>
                    <th class="min-w-200px w-100">Customer</th>
                    <th class="min-w-150px">Nominal</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ setDate($activity->realization_date, 'd M Y') }}</td>
                            <td>{{ $activity->activity->product->name }}</td>
                            <td>{{ $activity->activity->customer->name }}</td>
                            <td class="text-end">{{ setCurrency($activity->closing_amount ?? 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
