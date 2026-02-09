@props(['route' => '', 'text' => '', 'event' => '', 'icon' => 'fa-plus'])
<a id="btnAddView" onclick="{{ $event }}" class="btn btn-primary"
   @if($route)
   href="{{ $route }}"
    @endif>
    <i class="fa-solid {{ $icon }}"></i> {{ $text }}
</a>
