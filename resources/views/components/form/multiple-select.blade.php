<div class="form-group">
    <label>{{ $label }}</label>
    <select name="{{ $name }}[]"
            class="select2 form-control form-control-lg"
            id="{{ $name }}"
            @if($event)
                onchange="{{ $event }}"
            @endif
            multiple>
        @if($options)
            <option value="">{{ $options }}</option>
        @endif
        @if($all)
            <option value="All">All</option>
        @endif
        @foreach($datas as $key => $values)
            @if(is_array($values))
                <optgroup label="{{ $key }}">
                    @php
                    $arrValue = explode(',', $value);
                    @endphp
                    @foreach($values as $k => $v)
                        <option value="{{ $k }}" {{ in_array($k, $arrValue) || collect(old($name))->contains($k)  ? 'selected' : '' }} >{{ $v }}</option>
                    @endforeach
                </optgroup>
            @else
                @php
                    $arrValue = explode(',', $value);
                @endphp
                @foreach($values as $k => $v)
                    <option value="{{ $k }}" {{ in_array($k, $arrValue) || collect(old($name))->contains($k)  ? 'selected' : '' }} >{{ $v }}</option>
                @endforeach
            @endif
        @endforeach
    </select>
    @if($required)
        <div class="invalid-feedback" id="{{ $name }}-error"></div>
    @endif
</div>

