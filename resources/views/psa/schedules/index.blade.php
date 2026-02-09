@extends('layouts.app')
@section('content')
    @php
        $arrClass = ['Contacting' => 'bg-dark border-dark', 'Make Appointment' => 'bg-info border-info',  'Need Analysis' => 'bg-warning border-warning', 'Presenting' => 'bg-primary border-primary', 'Handling Objection' => 'bg-danger border-danger', 'Closing' => 'bg-success border-success'];
    @endphp
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body py-4">
            <div id="kt_docs_fullcalendar_basic"></div>
        </div>
        <div class="card-footer">
            <div class="d-flex flex-wrap">
                @foreach($arrClass as $k => $value)
                    <div class="d-flex me-3 mb-2">
                        <div style="width: 20px; height: 20px; border-radius: 5px" class="me-2 {{ $value }}"></div>
                        <span>{{ $k }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <x-modal-form/>

    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
@endsection
@section('scripts')
    <script>
        // Class definition
        var KTGeneralFullCalendarBasicDemos = function () {
            // Private functions

            var exampleBasic = function () {
                var todayDate = moment().startOf('day');
                var YM = todayDate.format('YYYY-MM');
                var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
                var TODAY = todayDate.format('YYYY-MM-DD');
                var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

                var calendarEl = document.getElementById('kt_docs_fullcalendar_basic');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },

                    height: 800,
                    contentHeight: 780,
                    aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio

                    nowIndicator: true,
                    now: TODAY + 'T09:25:00', // just for demo

                    views: {
                        dayGridMonth: { buttonText: 'month' },
                        timeGridWeek: { buttonText: 'week' },
                        timeGridDay: { buttonText: 'day' }
                    },

                    initialView: 'dayGridMonth',
                    initialDate: TODAY,

                    editable: true,
                    dayMaxEvents: true, // allow "more" link when too many events
                    navLinks: true,
                    events: [
                        @foreach($activities as $k => $activity)
                                @foreach($activity->details as $j => $detail)
                                    @php
                                        $planDate = date('Y-m-d', strtotime($detail->plan_date));
                                        $realizationDate = date('Y-m-d', strtotime($detail->end_date));
                                    @endphp
                                    @if($realizationDate != '' && $realizationDate != '0000-00-00')
                                    {
                                        title: '{{ $activity->customer->name }}',
                                        start: '{{ $planDate }}',
                                        description: '{{ $activity->plan_note }}',
                                        end: '{{ $planDate }}',
                                        className: '{{ $arrClass[$detail->typeActivity->name] }}',
                                    },
                                    @else
                                    {
                                        title: '{{ $activity->number }}',
                                        start: '{{ $realizationDate }}',
                                        description: '{{ $activity->realization_note }}',
                                        end: '{{ $realizationDate }}',
                                        className: '{{ $arrClass[$detail->typeActivity->name] }}',
                                    },
                                    @endif
                                @endforeach
                        @endforeach

                    ],
                    eventContent: function (info) {
                        var element = $(info.el);

                        if (info.event.extendedProps && info.event.extendedProps.description) {
                            if (element.hasClass('fc-day-grid-event')) {
                                element.data('content', info.event.extendedProps.description);
                                element.data('placement', 'top');
                                KTApp.initPopover(element);
                            } else if (element.hasClass('fc-time-grid-event')) {
                                element.find('.fc-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>');
                            } else if (element.find('.fc-list-item-title').lenght !== 0) {
                                element.find('.fc-list-item-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>');
                            }
                        }
                    }
                });

                calendar.render();
            }

            return {
                // Public Functions
                init: function () {
                    exampleBasic();
                }
            };
        }();

        // On document ready
        KTUtil.onDOMContentLoaded(function () {
            KTGeneralFullCalendarBasicDemos.init();
        });
    </script>
@endsection