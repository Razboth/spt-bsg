@props(['employee'])
<div class="card-body pt-9 pb-0">
    <div class="d-flex flex-wrap flex-sm-nowrap">
        <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                <img id="detail-photo" src="{{ $employee->photo ? asset('storage/'.$employee->photo) : asset('assets/media/blank-image.svg') }}" alt="image" />
            </div>
        </div>
        <div class="d-flex align-items-start flex-wrap mb-2">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-2">
                    <a href="#" class="text-gray-900 text-light-primary fs-2 fw-bold me-5">{{ $employee->name }}</a>
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
                        {{ $employee->position->positionName ?? '' }}
                    </div>
                    <div class="d-flex align-items-center fw-bold me-5">
                        <span class="svg-icon svg-icon-4 me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="currentColor" />
                                <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="currentColor" />
                            </svg>
                        </span>
                        {{ $employee->position->locationName ?? '' }}
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex flex-column me-10">
                        <div class="text-gray-600 mt-5">Nomor Induk Karyawan</div>
                        <div class="fw-bold">{{ $employee->employee_number }}</div>
                        <div class="text-gray-600 mt-2">Tanggal Masuk</div>
                        <div class="fw-bold">{{ setDate($employee->join_date, 't') }}</div>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="text-gray-600 mt-5">Unit Kerja</div>
                        <div class="fw-bold">{{ $employee->position->unitName ?? '' }}</div>
                        <div class="text-gray-600 mt-2">Golongan</div>
                        <div class="fw-bold">{{ $employee->position->gradeName ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #detail-photo {
        object-fit: cover;
    }
</style>
