@extends('layouts.app')
@section('content')
    <div class="card">
        <x-form.header :is-can-save="false" title="Detail Aktifitas" :back-url="route('psa.approvals.index')"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Nomor" :value="$activity->number ?? ''" readonly class="bg-gray-100"/>
                    <x-form.input label="Pegawai" :value="$activity->agent->name ?? ''" readonly class="bg-gray-100"/>
                    <x-form.input label="Produk" :value="$activity->product->name ?? ''" readonly class="bg-gray-100"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Tanggal" :value="setDate($activity->date) ?? ''" readonly class="bg-gray-100"/>
                    <x-form.input label="Tipe Aktifitas" :value="$typeActivities[$activity->activity_type] ?? ''"
                                  readonly class="bg-gray-100"/>
                    <x-form.input label="Customer" :value="$activity->customer->name ?? ''" readonly
                                  class="bg-gray-100"/>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-5">
                <div class="mb-5">
                    <h5 class="text-gray-900 fw-semibold">List Aktifitas</h5>
                    <div style="width: 100px; height: 2px;" class="bg-primary"></div>
                </div>
                <div>
                    <a class="btnApprove btn btn-primary d-none me-2" data-type="1">
                        <i class="fa-solid fa-pencil"></i> Approve 1 All
                    </a>
                    <a class="btnApprove btn btn-primary d-none" data-type="2">
                        <i class="fa-solid fa-pencil"></i> Approve 2 All
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-black bg-gray-300 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th class="min-w-50px">
                            <div class="form-check form-check-custom form-check-solid d-flex justify-content-center align-items-center">
                                <input class="form-check-input" id="checkAll" type="checkbox">
                            </div>
                        </th>
                        <th class="min-w-150px">Rencana</th>
                        <th class="min-w-150px">Realisasi</th>
                        <th class="min-w-100px w-100">Aktifitias</th>
                        <th class="min-w-150px">Appr 1</th>
                        <th class="min-w-150px">Appr 2</th>
                        <th class="min-w-150px">Appr 3</th>
                    </tr>
                    </thead>
                    <tbody class="text-black fs-7">
                    @foreach($activity->details->where('realization_date', '!=', '0000-00-00')->where('realization_date', '!=', null) as $detail)
                        @php
                            $planTime = setDate($detail->plan_date)." ".$detail->plan_time;
                            $realizationTime = setDate($detail->realization_date)." ".$detail->realization_time;
                        @endphp
                        <tr>
                            <td>
                                @if($detail->approved_status != 't' || $detail->approved_2_status != 't')
                                    <div class="form-check form-check-custom form-check-solid d-flex justify-content-center align-items-center">
                                        <input class="form-check-input detail-check" id="check_[{{ $detail->id }}]"
                                               type="checkbox"
                                               value="{{ $detail->id }}">
                                    </div>
                                @endif
                            </td>
                            <td>{{ $planTime }}</td>
                            <td>{{ $realizationTime }}</td>
                            <td>{{ $detail->typeActivity->name }}</td>
                            <td>
                                <x-views.approve-status :status="$detail->approved_status"
                                                        :url="route('psa.approvals.approve', [$detail->id])"/>
                            </td>
                            <td>
                                <x-views.approve-status :status="$detail->approved_2_status"
                                                        :url="route('psa.approvals.approve', [$detail->id, 'type' => '2'])"/>
                            </td>
                            <td>
                                @if($detail->typeActivity->name == 'Closing')
                                    <x-views.approve-status :status="$detail->approved_3_status"
                                                        :url="route('psa.approvals.approve', [$detail->id, 'type' => '3'])"/>
                                @else
                                        -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('checkAll').addEventListener('click', function () {
            const checkboxes = document.querySelectorAll('table input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.id !== 'checkAll') {
                    checkbox.checked = this.checked;
                }
            });
            toggleBtnApprove();
        });

        document.querySelectorAll('table input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBtnApprove);
        });

        function toggleBtnApprove() {
            const btnApproves = document.querySelectorAll('.btnApprove');
            const checkedBoxes = document.querySelectorAll('table input[type="checkbox"]:checked:not(#checkAll)');

            btnApproves.forEach(btn => {
                btn.classList.toggle('d-none', checkedBoxes.length === 0);
            });
        }

        document.querySelectorAll('.btnApprove').forEach(button => {
            button.addEventListener('click', function () {
                // Get approval type from data attribute
                const approvalType = this.getAttribute('data-type');

                // Collect IDs from checked checkboxes (excluding the checkAll checkbox)
                const checkedBoxes = document.querySelectorAll('.detail-check:checked');
                const ids = Array.from(checkedBoxes).map(checkbox => checkbox.value).join(',');

                // Ensure we have selected items
                if (!ids) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih setidaknya satu item'
                    });
                    return;
                }

                // Show SweetAlert confirmation
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dipilih akan disetujui!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send POST request if confirmed
                        $.ajax({
                            url: '{{ route(str_replace("/", ".", $menu_path).".approve-detail-batch") }}',
                            type: 'POST',
                            data: {
                                ids: ids,
                                type: approvalType
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: 'Data berhasil di-approve'
                                }).then(() => {
                                    // Reload the page to show updated statuses
                                    location.reload();
                                });
                            },
                            error: function (xhr) {
                                console.error(xhr);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan, coba lagi nanti!'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
