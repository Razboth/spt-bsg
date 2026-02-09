@props(['isReset' => true])
<button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
    <span class="svg-icon svg-icon-2">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
        </svg>
    </span>Filter
</button>
<div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
    <div class="px-7 py-5">
        <div class="fs-5 text-dark fw-bold">Filter Options</div>
    </div>
    <div class="separator border-gray-200"></div>
    <div class="px-7 py-5" data-kt-user-table-filter="form">
        {{ $slot }}
        @if($isReset)
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6 w-100" onclick="resetFilter();" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
            </div>
        @endif
    </div>
</div>
<script>
    function resetFilter(){
        $('#combo_1').val('').trigger("change")
        $('#combo_2').val('').trigger("change")
        $('#combo_3').val('').trigger("change")
        $('#combo_4').val('').trigger("change")
        $('#combo_5').val('').trigger("change")
        $('#combo_6').val('').trigger("change")
        $('#combo_7').val('').trigger("change")
        $('#combo_8').val('').trigger("change")
        $('#combo_9').val('').trigger("change")
        $('#combo_10').val('').trigger("change")

        $('#filter_1').val('').trigger("change")
        $('#filter_2').val('').trigger("change")
        $('#filter_3').val('').trigger("change")
        $('#filter_4').val('').trigger("change")
        $('#filter_5').val('').trigger("change")
        $('#filter_6').val('').trigger("change")
        $('#filter_7').val('').trigger("change")
        $('#filter_8').val('').trigger("change")
        $('#filter_9').val('').trigger("change")
        $('#filter_10').val('').trigger("change")
    }
</script>
