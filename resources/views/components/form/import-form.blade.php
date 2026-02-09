<div class="card">
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path) . '.process-import') }}"
        enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @isset($params)
            @foreach ($params as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        @endisset

        <x-form.modal-header title="{{ $title }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.file label="File" name="filename" accept=".xlsx, .xls" />
            <div class="text-end"><a href="{{ $url }}"><span class="text-active-primary">download template disini (.xlsx)</span></a></div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
