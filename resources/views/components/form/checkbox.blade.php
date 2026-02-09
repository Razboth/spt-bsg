@props(["label" => "", "required" => "", "name" => "", "class" => "", "arr" => [], "datas" => [], "event" => ""])
<div class="form-group mb-5">
    <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
    <div class="d-flex flex-wrap">
        @foreach($arr as $key => $value)
            <div class="form-check form-check-custom form-check-solid mb-2 me-5">
                <input class="form-check-input" type="checkbox"
               @if($event)
                   onclick="{{ $event }}"
               @endif

               @if(in_array($key, $datas)) checked @endif
               value="{{ $key }}"
               name="{{ $name."[$key]" }}"
               id="{{ $name."[$key]" }}" />
                <label class="form-check-label text-dark" for="{{ $name."[$key]" }}">{{ $value }}</label>
            </div>
        @endforeach
    </div>
</div>
