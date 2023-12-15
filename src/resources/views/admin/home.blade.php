@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <x-adminlte-card>
        <div id='calendar'></div>
    </x-adminlte-card>
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    //表示テーマ
                    themeSystem: 'bootstrap',
                    contentHeight: '90vh',
                    // plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    // スマホでタップしたとき即反応
                    selectLongPressDelay: 0,
                    // locale: 'ja',

                });
                calendar.render();
                console.log('calendar');
            });
        </script>
    @endpush
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop
