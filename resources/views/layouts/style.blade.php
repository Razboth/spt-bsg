<style>
    /*button primary */
    .btn-primary {
        background-color: {{ $primary }} !important;
    }

    .btn.btn-primary:hover:not(.btn-active) {
        background-color: {{ $lightPrimary  }} !important;
    }

    .btn.btn-primary:focus:not(.btn-active) {
        background-color: {{ $primary }} !important;
    }

    .btn.btn-primary.btn-modal:hover {
        background-color: {{ $lightPrimary  }} !important;
    }

    .btn-check:active+.btn.btn-light-primary, .btn-check:checked+.btn.btn-light-primary, .btn.btn-light-primary.active, .btn.btn-light-primary.show, .btn.btn-light-primary:active:not(.btn-active), .btn.btn-light-primary:focus:not(.btn-active), .btn.btn-light-primary:hover:not(.btn-active), .show>.btn.btn-light-primary {
        background-color: {{ $backgroundLightPrimary  }} !important;
    }

    /*button light primary */
    .btn-light-primary {
        background-color: {{ $backgroundLightPrimary }} !important;
        color: {{ $lightPrimary  }} !important;
    }

    .btn-light-primary i {
        color: {{ $lightPrimary  }} !important;
    }

    .btn-light-primary:hover i {
        color: white !important;
    }

    /*TABS AND ACCORDION*/
    .nav-line-tabs .nav-item .nav-link.active {
        color: {{ $lightPrimary  }} !important;
        border-bottom-color: {{ $lightPrimary  }} !important;
    }

    .nav-line-tabs .nav-item .nav-link:hover {
        color: {{ $lightPrimary  }} !important;
        border-bottom-color: {{ $lightPrimary  }} !important;
    }

    .btn-light-primary .svg-icon {
        color: {{ $lightPrimary  }} !important;
    }

    .btn-light-primary:hover .svg-icon {
        color: white !important;
    }

    .btn.btn-light-primary:hover:not(.btn-active) {
        background-color: {{ $lightPrimary  }} !important;
        color: white !important;
    }

    .btn.btn-light-primary.btn-modal:hover {
        background-color: {{ $lightPrimary  }} !important;
        color: white !important;
    }

    /*pagination */
    .paginate_button.page-item.active a {
        background-color: {{ $primary }} !important;
    }

    .paginate_button.page-item a:hover {
        color: {{ $primary }} !important;
    }

    .paginate_button:hover .page-link i {
        background-color: {{ $primary }} !important;
    }

    .paginate_button.page-item.active a:hover {
        color: white !important;
    }

    /* menu and modul */
    .menu-icon i {
        color: {{ $primary }} !important;
    }

    .menu-title.sidebar {
        color: {{ $primary }} !important;
    }

    .menu-item.show .menu-title {
        color: {{ $primary }} !important;
    }

    .menu-item:hover .menu-title {
        color: {{ $primary }} !important;
    }

    text-hover-primary {
        color: {{ $primary }} !important;
    }

    #datatables td, th {
        vertical-align: middle;
    }

    .table.dataTable>thead>tr>td:not(.sorting_disabled), table.dataTable>thead>tr>th:not(.sorting_disabled){
        padding-left: 5px;
        padding-right: 5px;
    }

    .table>:last-child>* {
        padding-left: 5px;
        padding-right: 5px;
    }

</style>
