<div class="card">
    <form method="POST" id="form-edit"
          action="{{ route(Str::replace('/', '.', $menu_path).'.update', [$product->id, $year]) }}">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="Setting Produk"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="unit_id" value="{{ $product->id }}">
            <x-form.input label="Produk" :value="$product->name ?? ''" readonly class="bg-gray-100"/>
            <x-form.input label="Periode" :value="$year ?? ''" readonly class="bg-gray-100"/>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="row">
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $monthName = numToMonth($monthKey);
                        $setting = $settings?->where('month', $monthKey)->first();
                    @endphp

                    @if ($i % 3 == 1)
                        <div class="row">
                            @endif

                            <div class="col-md-4">
                                <x-form.input label="{{ $monthName }}" name="amount[{{ $monthKey }}]" :value="$setting?->value ?? 0" maxlength="6"/>
                            </div>

                            @if ($i % 3 == 0 || $i == 12)
                        </div>
                    @endif
                @endfor
            </div>
        </div>
        <x-form.modal-footer/>
    </form>
</div>