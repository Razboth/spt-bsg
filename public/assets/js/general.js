$(function () {
    $(document).on("click", "#delete", function (e) {
        e.preventDefault();
        var link = $(this).attr("href");

        Swal.fire({
            title: "Are you sure?",
            text: "Delete this data?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("formDelete").action = link;
                document.getElementById("formDelete").submit();
            }
        });
    });
});

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on("click", "#btnConfirm", function (e) {
        e.preventDefault();
        var link = $(this).attr("href");
        var text = $(this).data("text");

        Swal.fire({
            title: "Are you sure?",
            text: text,
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request POST menggunakan jQuery AJAX
                $.ajax({
                    type: "POST",
                    url: link,
                    data: {
                        // Tambahkan data yang ingin Anda kirim ke server di sini
                        // contoh: name: "John", age: 30
                    },
                    success: function(response) {
                        // Handle ketika response sukses dari server
                        Swal.fire("Success", "Email telah dikirim!", "success");
                    },
                    error: function(error) {
                        // Handle ketika ada error dari server
                        Swal.fire("Error", "Ada kesalahan saat mengirim email.", "error");
                    }
                });
            }
        });
    });
});

function showGlobalModal(uri, width, filter) {
    let currentURL = window.location.href;
    let splitURL = currentURL.split("?");
    let url = splitURL[1] === undefined ? uri : uri + "?" + splitURL[1];
    if (filter !== undefined && filter !== "") {
        var filters = filter.split(',');

        filters.forEach(function(item, index) {
            if (url.includes("?")) {
                url = url + "&" + item + "=" + $("#" + item).val();
            } else {
                url = url + "?" + item + "=" + $("#" + item).val();
            }
        });
    }

    console.log(url);
    $.ajax({
        url: url,
        method: "GET",
        success: function (data) {
            $("#modal-form").find(".modal-body").html(data);
            $("#modal-form").modal("show");
            $("#modalDialogDefault").addClass(width);
        },
        error: function (error) {
            console.log(error);
        },
    });
}

$(document).on("click", ".btn-modal", function (e) {
    e.preventDefault();
    var url = $(this).data("url");
    var filter = $(this).data("filter");
    var width =
        $(this).data("size") === undefined ? "mw-650px" : $(this).data("size");
    showGlobalModal(url, width, filter);
});

$(document).on("click", "#btnSubmitForm", function (e) {
    e.preventDefault();
    let btnSubmit = this;
    let form = $(this).closest("form");
    if (!form.length) return;
    btnSubmit.setAttribute("data-kt-indicator", "on");
    btnSubmit.disabled = true;
    form.submit();
});

$("#modal-form").on("click", "#btnSubmit", function () {
    let btnSubmit = this;
    btnSubmit.setAttribute("data-kt-indicator", "on");
    btnSubmit.disabled = true;
    var form = $(this).closest("form");
    var action = form.attr("action");

    // Kirim multipart hanya bila benar-benar ada file yang dipilih.
    // Tanpa file, multipart cuma menambah overhead boundary + header per field.
    var hasFile = form
        .find('input[type="file"]')
        .toArray()
        .some(function (input) {
            return input.files && input.files.length > 0;
        });

    var payload = hasFile
        ? {
              data: new FormData(form[0]),
              processData: false,
              contentType: false,
          }
        : {
              data: form.serialize(),
              processData: true,
              contentType: "application/x-www-form-urlencoded; charset=UTF-8",
          };

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
        },
    });
    $.ajax({
        url: action,
        method: "post",
        processData: payload.processData,
        contentType: payload.contentType,
        cache: false,
        data: payload.data,
        success: function (result) {
            if (result.errors) {
                $.each(result.errors, function (key, value) {
                    $("<p>" + value + "</p>").appendTo("#" + key + "-error");
                    $("#" + key + "-error").css("display", "block");
                    $("#" + key).addClass("is-invalid");
                });
                btnSubmit.removeAttribute("data-kt-indicator");
                btnSubmit.disabled = false;
            } else {
                $("#modal-edit").modal("hide");
                let icon = "success";
                if (result.success.indexOf("Gagal") >= 0) icon = "error";
                if(result.type === 'import'){
                    Swal.fire({
                        text: result.success,
                        icon: icon,
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        footer: '<a href="' + result.url_log + '" class="btn btn-secondary" download>Download Log</a>'
                    }).then(function () {
                        window.location = result.url;
                    });
                }else{
                    Swal.fire({
                        text: result.success,
                        icon: icon,
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(function () {
                        window.location = result.url;
                    });
                }
            }
        },
    });
});

$("#modal-form").on("hidden.bs.modal", function () {
    $(this).find(".modal-body").empty();
    $("select").select2({
        dropdownParent: $("#form-filter"),
    });
});

$("#modal-form").on("shown.bs.modal", function (e) {
    $(".input-date").flatpickr({
        dateFormat: "d/m/Y",
    });

    $(".input-date-time").flatpickr({
        dateFormat: "d/m/Y H:i",
        enableTime: true,
        time_24hr: true,
    });

    $(".input-time").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        static: true,
    });
    $(".form-select").select2({
        dropdownParent: $("#modal-form"),
    });
});

$(".input-date").flatpickr({
    dateFormat: "d/m/Y",
});

$(".input-daterange").flatpickr({
    dateFormat: "d/m/Y",
    altInput: true,
    altFormat: "d/m/Y",
    mode: "range",
});

$(".input-time").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
});

getSub = function getSub(value, child, route, nullFirst) {
    nullFirst = nullFirst === undefined ? true : nullFirst;
    route = route === undefined ? 'subMasters' : route;
    var pathArray = window.location.pathname.split('/');
    var parentId = value;
    if (parentId) {
        $.ajax({
            url: '/' + [pathArray[1], pathArray[2], route, parentId].join('/'),
            type: "GET",
            success: function (data) {
                if (nullFirst) {
                    let childSelect = $('select[name=' + child + ']');
                    let firstOption = childSelect.find("option:first-child").text();
                    childSelect.empty().append('<option value=""> ' + firstOption + '</option>');
                } else {
                    $('select[name=' + child + ']').empty();
                    if (data.length === 0) $('select[name=' + child + ']').append('<option value="">&nbsp;</option>');
                }

                $.each(data, function (key, datas) {
                    $('select[name=' + child + ']').append('<option value="' + datas.id + '">' + datas.name + '</option>');
                });
            }
        });
    } else {
        var firstOption = $('select[name=' + child + ']').find("option:first-child").text();
        $('select[name=' + child + ']').empty();
        $('select[name=' + child + ']').append('<option value=""> ' + firstOption + '</option>');
    }
}

setNumber = function setNumber(elem) {
    replace = elem.value.replace(
        /[\\A-Za-z!"?$%^&*_={};.\:'/@#~,?\<>?|`?\]\[]/g,
        ""
    );
    elem.value = replace;
};

setCurrency = function setCurrency(elem) {
    replace = formatCurrency(
        elem.value.replace(
            /[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,
            ""
        )
    );
    if (replace.length == 0) replace = 0;
    elem.value = replace;
};

setNoSpacing = function setNoSpacing(elem) {
    replace = elem.value.replace(/ /g, "");
    elem.value = replace;
};

formatCurrency = function formatCurrency(val) {
    x = val.split(".");
    num = x[0];

    if (num < 0) return "";
    num = num.toString().replace(/\$|\,/g, "");
    if (isNaN(num)) num = "0";
    sign = num == (num = Math.abs(num));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10) cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num =
            num.substring(0, num.length - (4 * i + 3)) +
            "," +
            num.substring(num.length - (4 * i + 3));

    if (x.length == 1) return (sign ? "" : "-") + num;
    else return (sign ? "" : "-") + num + "." + x[1].substr(0, 4);
};

function createDatatable(datas, route, defOrder = '', defOrderStatus = 'asc', classDefault = '', classFirstColumn = '', method = 'GET', paginate = true) {
    let input_filter_timeout = null;
    let KTDatatablesServerSide = function () {
        let dt;
        let datatables = $('#datatables');

        let initDatatable = function () {
            let columns = [
                {data: 'DT_RowIndex', name: 'id', searchable: false, orderable: true, className: 'text-center ' + classFirstColumn}
            ];

            datas.forEach(function (column) {
                let columnData = column.split("\t");
                columns.push({
                    data: columnData[0],
                    name: columnData[0],
                    className: columnData[1] || classDefault,
                    orderable: columnData[2] !== undefined ? columnData[2] === 'true' : true,
                });
            });

            dt = $("#datatables").DataTable({
                processing: true,
                serverSide: true,
                paginate: paginate,
                ajax: {
                    url: route,
                    method: method
                },
                order: [[defOrder, defOrderStatus]],
                columnDefs: [{targets: "_all", className: classDefault}],
                columns: columns
            });
        };

        let handleSearchDatatable = function () {
            const filterSearch = document.getElementById('filter');
            filterSearch.addEventListener('keyup', function (e) {
                clearTimeout();
                input_filter_timeout=setTimeout(function() {
                    dt.search(e.target.value).draw();
                }, 100);
            });
        }

        $("#filter_1").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_1 = $("#filter_1").val();
            }).DataTable().ajax.reload();
        });
        $("#filter_2").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_2 = $("#filter_2").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_3").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_3 = $("#filter_3").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_4").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_4 = $("#filter_4").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_5").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_5 = $("#filter_5").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_6").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_6 = $("#filter_6").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_7").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_7 = $("#filter_7").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_8").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_8 = $("#filter_8").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_9").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_9 = $("#filter_9").val();
            }).DataTable().ajax.reload();
        });

        $("#filter_10").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.filter_10 = $("#filter_10").val();
            }).DataTable().ajax.reload();
        });



        $("#combo_1").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_1 = $("#combo_1").val();
            }).DataTable().ajax.reload();
        });
        $("#combo_2").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_2 = $("#combo_2").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_3").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_3 = $("#combo_3").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_4").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_4 = $("#combo_4").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_5").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_5 = $("#combo_5").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_6").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_6 = $("#combo_6").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_7").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_7 = $("#combo_7").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_8").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_8 = $("#combo_8").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_9").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_9 = $("#combo_9").val();
            }).DataTable().ajax.reload();
        });

        $("#combo_10").on('change', function () {
            datatables.on('preXhr.dt', function (e, settings, data) {
                data.combo_10 = $("#combo_10").val();
            }).DataTable().ajax.reload();
        });

        return {
            init: function () {
                initDatatable();
                handleSearchDatatable();
            }
        }
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function () {
        KTDatatablesServerSide.init();
    });
}

$(document).on("click", "#btnExport", function (e) {
    e.preventDefault();
    let url = $(this).data("url");
    let type = $(this).data("type");
    if (type === "undefined" || type === "" || type === "excel")
        type = "export";

    let filter = $("#filter").val();

    let filterMonth = $("#filterMonth").val();
    let filterYear = $("#filterYear").val();

    //GET ALL FILTER
    let combo_1 = $("#combo_1").val();
    let combo_2 = $("#combo_2").val();
    let combo_3 = $("#combo_3").val();
    let combo_4 = $("#combo_4").val();
    let combo_5 = $("#combo_5").val();
    let combo_6 = $("#combo_6").val();
    let combo_7 = $("#combo_7").val();
    let combo_8 = $("#combo_8").val();
    let combo_9 = $("#combo_9").val();
    let combo_10 = $("#combo_10").val();

    let filter_1 = $("#filter_1").val();
    let filter_2 = $("#filter_2").val();
    let filter_3 = $("#filter_3").val();
    let filter_4 = $("#filter_4").val();
    let filter_5 = $("#filter_5").val();
    let filter_6 = $("#filter_6").val();
    let filter_7 = $("#filter_7").val();
    let filter_8 = $("#filter_8").val();
    let filter_9 = $("#filter_9").val();
    let filter_10 = $("#filter_10").val();

    let completeURL =
        url +
        "/" +
        type +
        "?filter=" +
        filter +
        "&filterMonth=" +
        filterMonth +
        "&filterYear=" +
        filterYear +
        "&combo_1=" +
        combo_1 +
        "&combo_2=" +
        combo_2 +
        "&combo_3=" +
        combo_3 +
        "&combo_4=" +
        combo_4 +
        "&combo_5=" +
        combo_5 +
        "&combo_6=" +
        combo_6 +
        "&combo_7=" +
        combo_7 +
        "&combo_8=" +
        combo_8 +
        "&combo_9=" +
        combo_9 +
        "&combo_10=" +
        combo_10 +
        "&filter_1=" +
        filter_1 +
        "&filter_2=" +
        filter_2 +
        "&filter_3=" +
        filter_3 +
        "&filter_4=" +
        filter_4 +
        "&filter_5=" +
        filter_5 +
        "&filter_6=" +
        filter_6 +
        "&filter_7=" +
        filter_7 +
        "&filter_8=" +
        filter_8 +
        "&filter_9=" +
        filter_9 +
        "&filter_10=" +
        filter_10;
    window.open(completeURL, "_blank");
});
