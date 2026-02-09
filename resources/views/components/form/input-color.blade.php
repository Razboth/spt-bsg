@props(["label" => "", "name" => "", "value" => "", "class" => ""])
@if($label)
    <div class="form-group mb-5">
        <label class="form-label">{{ $label }}</label>
        <input type="color"
               id="{{ $name }}"
               name="{{ $name }}"
               class="form-control form-control-color w-100px"
               value="{{ old($name, $value) }}"
               {{ $attributes->merge(['class' => 'form-control '.$class]) }}
               placeholder=""/>
    </div>
@else
    <input type="text"
           id="{{ $name }}"
           {{ $attributes->merge(['class' => 'form-control '.$class]) }}
           name="{{ $name }}"
           value="{{ empty($value) ? '' : $value }}"
           placeholder="{{ $placeholder }}"
    />
@endif
