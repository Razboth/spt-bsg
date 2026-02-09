<div class="card">
    <form method="POST" id="form-edit"
          action="{{ route(Str::replace('/', '.', $menu_path).'.update', [$agent->id, $year]) }}">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="Setting Boom Cabang"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="agent_id" value="{{ $agent->id }}">
            <x-form.input label="Sales" :value="$agent->name ?? ''" readonly class="bg-gray-100"/>
            <x-form.input label="Periode" :value="$year ?? ''" readonly class="bg-gray-100"/>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="row">
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $monthName = numToMonth($monthKey);
                        $field = 'total_' . $monthKey;
                        $setting = $settings?->where('month', $monthKey)->first();
                    @endphp

                    @if ($i % 2 == 1)
                        <div class="row">
                            @endif

                            <div class="col-md-6">
                                <div class="mt-5 mb-2">
                                    <h5 class="text-gray-900 fw-semibold">{{ $monthName }}</h5>
                                    <div style="width: 100px; height: 2px;" class="bg-primary"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input label="Target" name="amount[{{ $monthKey }}][target]"
                                                      :value="$setting?->target ?? 0" currency/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input label="Aktual" name="amount[{{ $monthKey }}][realization]"
                                                      :value="$setting?->actual ?? 0" currency/>
                                    </div>
                                </div>
                            </div>

                            @if ($i % 2 == 0 || $i == 12)
                        </div>
                    @endif
                @endfor
            </div>
        </div>
        <x-form.modal-footer/>
    </form>
</div>