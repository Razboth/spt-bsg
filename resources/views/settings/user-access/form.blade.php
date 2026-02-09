@extends('layouts.app')
@section('content')
<div class="card">
    <form method="POST" id="form-edit" action="{{ route('settings.user-access.update', $role->id) }}">
        @csrf
        @method('PATCH')
        <x-form.header title="List Akses {{ $role->name }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        @php
            $no = 0;
        @endphp
        <div class="card-body pt-0">
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                @foreach($moduls as $key => $modul)
                    @if(in_array($modul->target, $rolePermissions))
                        @php $no++; @endphp
                        <li class="nav-item">
                            <a class="nav-link text-dark {{ $no == 1 ? 'active' : '' }}" data-bs-toggle="tab" href="#{{ $modul->target }}">{{ $modul->name }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>

            @php
                $no = 0;
            @endphp
            <div class="tab-content" id="myTabContent">
                @foreach($moduls as $key => $modul)
                    @if(in_array($modul->target, $rolePermissions))
                        @php $no++; @endphp
                        <div class="tab-pane fade {{ $no == 1 ? 'show active' : '' }}" id="{{ $modul->target }}" role="tabpanel">
                            @foreach($modul->subModul as $subModul)
                                <div class="card-title mb-5">
                                    <h4 class="card-label">
                                        {{ $subModul->name }}
                                    </h4>
                                </div>
                                <table class="table table-rounded table-row-bordered border gy-5 gs-7 mb-5">
                                    <thead>
                                        <tr class="text-start text-gray-800 bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                                            <th width="10">No</th>
                                            <th width="*">Nama</th>
                                            @foreach($arrPermission as $key => $permission)
                                                <th width="120" class="text-center">{{ $permission }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="text-black fs-7">
                                    @foreach($subModul->appMenu as $k => $menu)
                                        <tr>
                                            <td>{{ $k + 1 }}</td>
                                            <td>{{ $menu->name }}</td>
                                            @foreach($arrPermission as $key => $permission)
                                                <td align="center">
                                                    @php
                                                        $permissionMenu = $key." ".$modul->target."/".$menu->target;
                                                    @endphp
                                                    @if(in_array($permissionMenu, $permissions))
                                                        <div class="form-check form-check-custom form-check-solid col-md-1">
                                                            <input class="form-check-input text-center align-middle" type="checkbox"
                                                                   {{ in_array($permissionMenu, $rolePermissions) ? 'checked' : '' }}
                                                                   value="{{ $key }}"
                                                                   name="access[{{ $modul->target }}][{{ $menu->target }}][{{ $key }}]"
                                                                   id="access[{{ $key }}]" />
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                        @endif
                @endforeach

            </div>
        </div>
    </form>
</div>
@endsection
