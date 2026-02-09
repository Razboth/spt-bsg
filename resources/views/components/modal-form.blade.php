@props(['isDismissable' => true])
<div class="modal fade" id="modal-form" aria-hidden="true">
    <div id="modalDialogDefault" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

@if(!$isDismissable)
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#modal-form').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    </script>
@endif
