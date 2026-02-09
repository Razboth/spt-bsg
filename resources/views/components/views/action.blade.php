@props(['url_edit' => '', 'url_destroy' => '', 'url_show' => '', 'icon_show' => 'fa-solid fa-list', 'isModal' => true, 'size' => '', 'url_slot' => '', 'icon_slot' => 'fa-solid fa-list', 'isModalSlot' => true, 'isTargetBlankSlot' => false, 'menu_path' => '', 'customAction' => ''])
<div class="text-center justify-content-between">
    @if (is_array($customAction))
        @foreach ($customAction as $key => $action)
            @if (str_contains($action['url'], 'edit'))
                @can('edit ' . $menu_path)
                    <a @if ($action['isModal']) data-url="{{ $action['url'] }}"
                            @else
                                href="{{ $action['url'] }}" @endif
                        class="btn btn-icon btn-light-{{ $action['class-icon'] ?? 'info' }} w-25px h-25px me-1 {{ $action['isModal'] ? 'btn-modal' : '' }}"
                        title="Edit Data" @if ($action['isModal']) data-bs-toggle="modal" @endif>
                        <i class="{{ $action['icon'] }} fs-7"></i>
                    </a>
                @endcan
            @elseif(str_contains($action['url'], 'destroy'))
                @can('delete ' . $menu_path)
                    <button href="{{ $action['url'] }}" id="delete" class="btn btn-icon btn-light-danger w-25px h-25px"
                        title="Delete Data">
                        <i class="{{ $action['icon'] }} fs-7"></i>
                    </button>
                @endcan
            @else
                <a @if ($action['isModal']) data-url="{{ $action['url'] }}"
                        @else
                            href="{{ $action['url'] }}" @endif
                    class="btn btn-icon btn-light-{{ $action['class-icon'] ?? 'info' }} w-25px h-25px me-1 {{ $action['isModal'] ? 'btn-modal' : '' }}"
                    @if ($action['isModal']) data-bs-toggle="modal" @endif
                    @if ($action['size']) data-size="{{ $action['size'] }}" @endif
                    title="{{ $action['title'] }}">
                    <i class="{{ $action['icon'] }} fs-7"></i>
                </a>
            @endif
        @endforeach
    @endif
    @if ($url_slot)
        <a @if ($isModalSlot) data-url="{{ $url_slot }}"
                @else
                    href="{{ $url_slot }}" @endif
            @if ($isTargetBlankSlot) target="_blank" @endif
           @if ($size) data-size="{{ $size }}" @endif
           class="btn btn-icon btn-light-info w-25px h-25px me-1 {{ $isModalSlot ? 'btn-modal' : '' }}"
            @if ($isModalSlot) data-bs-toggle="modal" @endif>
            <i class="{{ $icon_slot }} fs-7"></i>
        </a>
    @endif
    @if ($url_show)
        <a href="{{ $url_show }}" class="btn btn-icon btn-light-info w-25px h-25px me-1" title="List Data">
            <i class="{{ $icon_show }} fs-7"></i>
        </a>
    @endif
    @if ($url_edit)
        @can('edit ' . $menu_path)
            @if ($isModal)
                <a data-bs-toggle="modal" title="Edit Data"
                    class="btn btn-icon btn-light-primary w-25px h-25px me-1 btn-modal" data-url="{{ $url_edit }}">
                    <i class="fa-solid fa-pen fs-7"></i>
                </a>
            @else
                <a href="{{ $url_edit }}" title="Edit Data" class="btn btn-icon btn-light-primary w-25px h-25px me-1">
                    <i class="fa-solid fa-pen fs-7"></i>
                </a>
            @endif
        @endcan
    @endif
    @if ($url_destroy)
        @can('delete ' . $menu_path)
            <button href="{{ $url_destroy }}" id="delete" class="btn btn-icon btn-light-danger w-25px h-25px"
                title="Delete Data">
                <i class="fa-solid fa-trash fs-7"></i>
            </button>
        @endcan
    @endif
</div>
