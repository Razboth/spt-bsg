@props(['route-start' => '', 'route-progress' => '', 'route-end' => ''])
<div>
    <div id="divSubmitData" style="display: block">
        <div class="d-flex justify-content-center mt-10">
            <button type="button" class="btn btn-primary w-100" id="btnProcess"
                    data-kt-users-modal-action="submit">
                <span class="indicator-label"><i class="fa-regular fa-floppy-disk"></i> Proses</span>
                <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>

    <div id="divProgress" class="mt-10" style="display: none">
        Diproses
        <div class="progress mb-2" style="height: 30px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                 aria-valuemax="100"></div>
        </div>
        <div id="progressText" class="text-end">
            0 / 0 (0%)
        </div>
    </div>

    <div id="divDownloadLog" class="mt-10" style="display: none">
        Proses Selesai
        <div class="d-flex justify-content-center mt-2">
            <a href="#" class="btn btn-primary w-100" id="btnLog" download>
                <span class="indicator-label"><i class="fa-regular fa-floppy-disk"></i> Download Log</span>
            </a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#btnProcess').on('click', async function () {
            $('#btnCloseModal').css('display', 'none');
            $('#divSubmitData').css('display', 'none');
            $('#divProgress').css('display', 'block');

            try {
                let startResponse = await $.ajax({
                    url: "{{ $routeStart }}",
                    type: "GET",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    dataType: 'json'
                });

                var maxLoop = startResponse.total;

                async function processLoop(currentLoop) {
                    if (currentLoop < maxLoop) {
                        var currentData = currentLoop + 1;
                        try {
                            await $.ajax({
                                url: "{{ $routeProgress }}",
                                type: "POST",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "currentData": currentData
                                }
                            });

                            // Update progress bar
                            var percentage = ((currentLoop + 1) / maxLoop) * 100;
                            $('#progressBar').css('width', percentage + '%').attr('aria-valuenow', percentage);

                            // Update progress text
                            $('#progressText').text(`${currentLoop + 1} / ${maxLoop} (${percentage.toFixed(2)}%)`);

                            // Pemanggilan rekursif setelah AJAX selesai
                            await processLoop(currentLoop + 1);
                        } catch (error) {
                            console.log(error.responseText);
                        }
                    }

                    if (currentLoop === maxLoop) {
                        $('#divDownloadLog').css('display', 'block');
                        $('#divProgress').css('display', 'none');
                        $('#btnCloseModal').css('display', 'block');

                        try {
                            let response = await $.ajax({
                                url: "{{ $routeEnd }}",
                                type: "POST",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                },
                                dataType: 'json',
                            });

                            console.log('ini end');

                            $('#btnLog').attr('href', response.log);
                        } catch (error) {
                            console.log(error.responseText);
                        }
                    }
                }

                // Mulai loop dari 0
                await processLoop(0);

            } catch (error) {
                console.log(error.responseText);
            }

        });
    });


</script>
