@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <x-adminlte-card>
        <div id='calendar'></div>
        <!-- CreateModal -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">予定を登録</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form name="create_form">
                        <div class="modal-body">
                            <label for="create_date" class="col-label">登録予定日:</label>
                            <input type="date" class="form-control" id="create_date" name="date" value=""
                                required>
                            <div class="col py-1 ml my-auto" id="create_session_time">
                                {{ Form::label('session_time', 'セッション時間') }}
                                {{ Form::select('session_time', $session_times, [], ['id' => 'session_time', 'class' => 'form-control', 'required' => 'required']) }}
                            </div>
                            @foreach ($users as $key => $val)
                                <div class="col-md-2 py-1 ml-4 my-auto" id="create_user">
                                    {{ Form::checkbox('user', $key, [], ['id' => 'user' . $key, 'class' => 'form-check-input', 'required' => 'required']) }}
                                    {{ Form::label($val->name, [], ['class' => 'form-check-label']) }}
                                </div>
                            @endforeach
                            <div class="col py-1 ml my-auto" id="create_text">
                                {{ Form::label('text', '備考欄') }}
                                {{ Form::textarea('text', old('text'), ['class' => 'form-control', 'rows' => '5', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                            <button type="button" class="btn btn-primary" id="store-btn"
                                data-bs-dismiss="modal">保存する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- deleteModal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">予定を編集</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    @method('patch')
                    <form method="POST" action="">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" id="edit_id" value="" name="id">
                            <label for="edit_date" class="col-label">登録予定日:</label>
                            <input type="date" class="form-control" id="edit_date" name="date" value="">
                            <label for="edit_text" class="col-form-label">連絡事項:</label>
                            <input type="text" class="form-control" id="edit_text" name="text" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn btn-primary">保存する</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">
                                削除する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" style="margin: 150px auto;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">削除確認</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">削除しますか？</div>
                    <div class="modal-footer">
                        @method('delete')
                        <form method="POST" action="">
                            @csrf
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">やめる</button>
                            <input type="hidden" id="delete_id" value="" name="id">
                            <button type="submit" class="btn btn-danger">はい</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- showModal -->
        <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">予定を編集</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    @method('patch')
                    <form method="POST" action="">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" id="show_id" value="" name="id">
                            <label for="show_date" class="col-label">登録予定日:</label>
                            <input type="date" class="form-control" id="show_date" name="date" value="">
                            <label for="show_text" class="col-form-label">連絡事項:</label>
                            <input type="text" class="form-control" id="show_text" name="text" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn btn-primary">保存する</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                data-target="#exampleModal">
                                削除する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-adminlte-card>
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="/js/holiday_jp.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // const editModal = new Modal(document.getElementById('editModal'));
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
                    locale: 'ja',

                    //祝日に赤spanタグを挿入
                    dayCellContent: function(arg) {
                        // console.log(arg);
                        const date = new Date();
                        date.setFullYear(
                            arg.date.getFullYear(),
                            arg.date.getMonth(),
                            arg.date.getDate()
                        );
                        const holiday = holiday_jp.between(new Date(date), new Date(date));
                        let hol_tag = document.createElement('span')
                        if (holiday[0]) {
                            hol_tag.innerHTML = `${arg.date.getDate()}`
                            hol_tag.className = 'fc-day-hol';

                            let arrayOfDomNodes = [hol_tag]
                            return {
                                domNodes: arrayOfDomNodes
                            }
                        } else {
                            //日本語化の日表示を外す
                            arg.dayNumberText = arg.dayNumberText.replace('日', '');
                            return arg.dayNumberText;
                        }
                    },

                    events: function(info, successCallback, failureCallback) {
                        // Laravelのイベント取得処理の呼び出し
                        axios
                            .post("/admin/home/events", {
                                start_date: info.start.valueOf(),
                                end_date: info.end.valueOf(),
                            })
                            .then((response) => {
                                // 一旦全てのイベントを削除
                                calendar.removeAllEvents();
                                // // カレンダーに読み込み
                                successCallback(response.data);
                                // console.log(response.data);
                            })
                            .catch(() => {
                                // バリデーションエラーなど
                                alert("取得に失敗しました");
                            });
                    },

                    selectable: true,
                    select: function(info) {
                        document.getElementById('create_date').value = info.startStr;
                        $('#createModal').modal('show');

                        const close = document.getElementById('store-btn');
                        const saveOnClick = () => {
                            // 値を日付型として取得
                            const date = document.getElementById('create_date').valueAsDate
                            const text = document.getElementById('create_text').value
                            const session_time = document.getElementById('create_session_time').value
                            const user = document.getElementById('create_user').value

                            // Laravelのaxiosから登録処理の呼び出し
                            axios
                                .post("/admin/home/store", {
                                    start_date: info.start.valueOf(),
                                    end_date: info.end.valueOf(),
                                    date: date,
                                    text: text,
                                    user: user,
                                    session_time: session_time,
                                })
                                .then((response) => {
                                    // カレンダーに読み込み
                                    calendar.addEvent({
                                        // PHP側から受け取ったevent_idをeventObjectのidにセット
                                        id: response.data.id,
                                        title: response.data.title,
                                        color: response.data.color,
                                        start: response.data.start
                                    });
                                    //renderevent();はv3まで
                                    calendar.refetchEvents();
                                    // console.log(response);
                                })
                                .catch(() => {
                                    // バリデーションエラーなど
                                    alert("取得に失敗しました");
                                });
                        };

                        //保存ボタンによる送信、その後イベントの解除
                        close.addEventListener('click', saveOnClick)
                        var createModalEl = document.getElementById('createModal')
                        createModalEl.addEventListener('hidden.bs.modal', () => {
                            //第二引数に値を指定する必要がある
                            close.removeEventListener('click', saveOnClick);
                        });
                    },

                    eventClick: function(info) {

                        if (info.event.role = 'admin') {
                            $('#deleteModal').modal('show');
                            // document.getElementById('edit_id').value = info.event.id;
                            // document.getElementById('edit_text').value = info.event.extendedProps.text;
                            // document.getElementById('edit_date').value = info.event.startStr;
                            // console.log(info);
                        }
                        if (info.event.role = 'staff') {
                            $('#showModal').modal('show');
                        };
                    }

                });
                calendar.render();
            });
        </script>
    @endpush
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // console.log('Hi!');
    </script>
@stop
