<div class="card">
    <form method="POST" id="form-edit" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="{{ $title }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-views.span-inline text="Last Sync" value="{{ $lastSyncTime }}"/>
                </div>
                <div class="col-md-6">
                    <x-views.span-inline text="Last Data" value="{{ $lastData }}"/>
                </div>
            </div>
            <x-form.process-button
                route-start="{{ route(Str::replace('/', '.', $menu_path).'.process-start') }}"
                route-progress="{{ route(Str::replace('/', '.', $menu_path).'.process-progress') }}"
                route-end="{{ route(Str::replace('/', '.', $menu_path).'.process-end') }}"
            />
        </div>
    </form>
</div>
