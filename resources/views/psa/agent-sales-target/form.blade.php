<div class="card">
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.store') }}">
        @csrf
        <x-form.modal-header title="Edit Data Target"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="type" value="{{ $menu_path == 'psa/agent-sales-target-account' ? '1' : '2' }}"/>
            <input type="hidden" name="agent_id" value="{{ $agent->id ?? '' }}"/>
            <input type="hidden" name="target_period_id" value="{{ $period->id ?? '' }}"/>
            <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}"/>
            <x-form.input label="Periode" :value="$period->name ?? ''" readonly class="bg-gray-100"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Sales" :value="$agent->name ?? ''" readonly class="bg-gray-100"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Produk" :value="$product->name ?? ''" readonly class="bg-gray-100"/>
                </div>
            </div>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Target Per Tahun" name="total" :value="$target->total ?? 0" currency/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Target Per Bulan" name="total_per_month" :value="!empty($target->total) ? $target->total / 12 : 0" currency readonly class="bg-gray-100"/>
                </div>
            </div>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="row">
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $monthName = numToMonth($monthKey);
                        $field = 'total_' . $monthKey;
                        $detail = $target?->details->where('month', $monthKey)->first();
                    @endphp

                    @if ($i % 3 == 1)
                        <div class="row">
                            @endif

                            <div class="col-md-4">
                                <x-form.input label="{{ $monthName }}" name="amount[{{ $monthKey }}]" :value="$detail->amount ?? 0" currency/>
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
<script>
    $(document).ready(function () {
        $('#total').on('input', function () {
            let total = $(this).val();
            //remove comma
            total = total.replace(/,/g, '');
            let total_per_month = (total / 12).toFixed(0);

            //add comma
            total_per_month = total_per_month.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            //loop in every input field
            $('input[name^="amount"]').each(function () {
                $(this).val(total_per_month);
            });

            $('#total_per_month').val(total_per_month);
        });

        $('input[name^="amount"]').on('input', function() {
            //loop amount and sum it to total
            let total = 0;
            $('input[name^="amount"]').each(function () {
                let amount = $(this).val();
                //remove comma
                amount = amount.replace(/,/g, '');
                total += parseInt(amount);
            });


            let total_per_month = (total / 12).toFixed(0);

            //add comma
            total_per_month = total_per_month.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('#total_per_month').val(total_per_month);

            //add comma
            total = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('#total').val(total);
        });
    });
</script>