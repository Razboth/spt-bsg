@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($template) ? route(Str::replace('/', '.', $menu_path) . '.store') :
            route(Str::replace('/', '.', $menu_path) . '.update', $template->id) }}">
            @csrf
            @if(!empty($template))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($template) ? 'Tambah Template' : 'Edit Template' }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <x-form.radio label="Tipe" name="type" :datas="$types" value="{{ $template->type ?? '' }}" />
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $template->name ?? '' }}" required/>
                        <x-form.select label="Dataset" name="dataset_id" option="- Pilih Dataset -" :datas="$datasets" value="{{ $template->dataset_id ?? '' }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Kode" placeholder="Masukkan Kode" name="code" value="{{ $template->code ?? '' }}" required/>
                        <x-form.select label="Ukuran Kertas (Kosongkan jika Email)" name="page_id" option="- Pilih Ukuran Kertas -" :datas="$pages" value="{{ $template->page_id ?? '' }}"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <x-form.input label="Margin Bottom (Kosongkan jika Email)" maxlength="2" name="margin_bottom" value="{{ $template->margin_bottom ?? '' }}"/>
                    </div>
                    <div class="col-md-3">
                        <x-form.input label="Margin Top (Kosongkan jika Email)" maxlength="2" name="margin_top" value="{{ $template->margin_top ?? '' }}"/>
                    </div>
                    <div class="col-md-3">
                        <x-form.input label="Margin Right (Kosongkan jika Email)" maxlength="2" name="margin_right" value="{{ $template->margin_right ?? '' }}"/>
                    </div>
                    <div class="col-md-3">
                        <x-form.input label="Margin Left (Kosongkan jika Email)" maxlength="2" name="margin_left" value="{{ $template->margin_left ?? '' }}"/>
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <table class="table table-rounded table-row-bordered border gy-5 gs-7" id="table-dataset">
                    <thead>
                    <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th width="50">No</th>
                        <th width="200">Judul</th>
                        <th width="200">Penggunaan</th>
                        <th width="200">Contoh</th>
                        <th width="*">Keterangan</th>
                    </tr>
                    </thead>
                    <tbody id="bodyDataset">
                    @isset($template->dataset->details)
                        @foreach($template->dataset->details as $k => $dataset)
                            <tr>
                                <td>{{ $k + 1 }}</td>
                                <td>{{ $dataset->title }}</td>
                                <td onclick="clipThis('{{'{' . $dataset->code . '}'}}')" data-clipboard-text="{{'{' . $dataset->code . '}'}}">{{'{' . $dataset->code . '}'}}</td>
                                <td>{{ $dataset->example }}</td>
                                <td>{{ $dataset->description }}</td>
                            </tr>
                        @endforeach
                    @endisset
                    </tbody>
                </table>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div id="kt_docs_ckeditor_document_toolbar"></div>
                <textarea name="content" id="content" hidden></textarea>
                <div id="kt_docs_ckeditor_document">
                    {!! $template->content ?? '' !!}
                </div>
            </div>
        </form>
    </div>
    <style>
        td[data-clipboard-text] {
            color: #007bff; /* Bootstrap primary color */
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
@endsection
@section('scripts')
    <script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-document.bundle.js')}}"></script>
    <script>
        $('#dataset_id').change(function () {
            if ($(this).val() !== '') {
                $.ajax({
                    url: "/settings/app-master-templates/get-dataset/" + $(this).val(),
                    type: "GET",
                    success: function (response) {
                        // Clear existing rows
                        $('#bodyDataset').empty();

                        // Each response then create table row
                        $.each(response, function (key, value) {
                            var tr = '<tr>' +
                                '<td>' + value.id + '</td>' +
                                '<td>' + value.title + '</td>' +
                                '<td onclick="clipThis(\'{' + value.code + '}\')" data-clipboard-text="{' + value.code + '}">{' + value.code + '}</td>' +
                                '<td>' + value.example + '</td>' +
                                '<td>' + value.description + '</td>' +
                                '</tr>';
                            $('#bodyDataset').append(tr);
                        });
                    }
                });
            }
        });

        function clipThis(value) {
            // Create a temporary button
            var btn = $('<button class="btn-clipboard" data-clipboard-text="' + value + '">Copy</button>').appendTo('body');

            var clipboard = new ClipboardJS('.btn-clipboard', {
                text: function(trigger) {
                    return value;
                }
            });

            clipboard.on('success', function(e) {
                alert('Copied!');
                e.clearSelection();
                clipboard.destroy(); // Destroy the clipboard instance after copying
                btn.remove(); // Remove the temporary button
            });

            clipboard.on('error', function(e) {
                alert('Error copying!');
                clipboard.destroy(); // Destroy the clipboard instance after error
                btn.remove(); // Remove the temporary button
            });

            // Trigger click on the temporary button
            btn.click();
        }

        DecoupledEditor
            .create(document.querySelector('#kt_docs_ckeditor_document'))
            .then(editor => {
                const toolbarContainer = document.querySelector( '#kt_docs_ckeditor_document_toolbar' );

                toolbarContainer.appendChild( editor.ui.view.toolbar.element );
            })
            .catch(error => {
                console.error(error);
            });

        $('#form-edit').submit(function (e) {
            e.preventDefault();
            $('#content').val($('#kt_docs_ckeditor_document').html());
            $(this).unbind('submit').submit();
        });
    </script>
@endsection
