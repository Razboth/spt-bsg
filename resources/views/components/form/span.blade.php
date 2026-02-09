<div class="d-flex flex-wrap">
    <div class="col-md-4">
        <p class="card-text user-info-title font-weight-bold mb-50 mr-50">{{ $label }}</p>
    </div>
    <div class="col-md-8">
        <p class="c ard-text mb-50" id="{{ $name }}">
            @if($file)
                <a href="{{ asset('storage'.$value) }}" download>{!! $value ? getIcon($value) : '' !!}</a>
            @else
                {!! $value !!}
            @endif
        </p>
    </div>
</div>
