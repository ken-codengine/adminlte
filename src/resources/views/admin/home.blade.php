@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
  <h1>Dashboard</h1>
@stop

@section('content')
  <x-adminlte-card>
    <!-- 年のプルダウン -->
    <p>カレンダーをロック</p>
    <label for="yearSelect">西暦</label>
    <select id="yearSelect">
      <?php for ($i = 2024; $i <= 2099; $i++): ?>
      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>

    <div class="calendar-lock">
      <!-- 月のチェックボックス -->
      <?php for ($i = 1; $i <= 12; $i++): ?>
      <div>
        <input type="checkbox" class="monthCheckbox" id="monthCheckbox<?php echo $i; ?>">
        <label for="monthCheckbox<?php echo $i; ?>"><?php echo $i; ?>月</label>
      </div>
      <?php endfor; ?>
    </div>
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
              <input type="date" class="form-control" id="create_date" name="date" value="" required>
              <div class="col py-1 ml my-auto" id="create_session_time">
                {{ Form::label('session_time', 'セッション時間') }}
                {{ Form::select(
                    'session_time',
                    array_map(function ($times) {
                        return $times['start_time'] . ' ~ ' . $times['end_time'];
                    }, $session_times),
                    null,
                    ['id' => 'session_time', 'class' => 'form-control', 'required' => 'required'],
                ) }}
              </div>
              @foreach ($users as $key => $val)
                <div class="col-md-2 py-1 ml-4 my-auto" id="create_user">
                  {{ Form::checkbox('user', $key, false, ['id' => 'user' . $key, 'class' => 'form-check-input', 'required' => 'required']) }}
                  {{ Form::label($val->name, null, ['class' => 'form-check-label']) }}
                </div>
              @endforeach
              <div class="col py-1 ml my-auto" id="create_text">
                {{ Form::label('text', '備考欄') }}
                {{ Form::textarea('text', old('text'), ['class' => 'form-control', 'rows' => '5', 'required' => 'required']) }}
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
              <button type="button" class="btn btn-primary" id="store-btn" data-bs-dismiss="modal">保存する</button>
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
    <!-- confirmModal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">提出されたシフト</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            {{-- <h5>提出されたシフト</h5> --}}
            <div id="confirm-text"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
            <button type="button" class="btn btn-danger" id="delete-btn" data-dismiss="modal">削除する</button>
          </div>
        </div>
      </div>
    </div>
    {{-- <!-- showModal -->
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
              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">
                削除する</button>
            </div>
          </form>
        </div>
      </div>
    </div> --}}
  </x-adminlte-card>
  @push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="/js/holiday_jp.js"></script>
    <script>
      for (var i = 1; i <= 12; i++) {
        (function(i) { // 即時関数で i の値をキャプチャ
          var checkbox = document.getElementById('monthCheckbox' + i);
          checkbox.addEventListener('change', function() {
            var year = parseInt(document.getElementById('yearSelect').value, 10);
            var month = i;
            // var month = i.toString().padStart(2, '0'); // 月を2桁の形式で取得
            // var date = '01'
            // var formattedDate = year + '-' + month + date; // 年-月-日の形式を作成
            // console.log(formattedDate);
            axios.post('/your-endpoint', {
                checkboxState: this.checked,
                year: year, // ここで作成した日付を送信
                month: month // ここで作成した日付を送信
              })
              .then(function(response) {
                // console.log(response);
              })
              .catch(function(error) {
                // console.log(error);
              });
          });
        })(i);
      }

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
            document.getElementById('yearSelect').value = info.start.getFullYear();

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
                // console.log(info);
              })
              .catch(() => {
                // バリデーションエラーなど
                alert("取得に失敗しました");
              });
          },

          eventClick: function(info) {

            if (info.event.role = 'admin') {
              $('#deleteModal').modal('show');
              $('#delete-text').text(info.event.startStr + " " + info.event.title);
              // document.getElementById('edit_id').value = info.event.id;
              // document.getElementById('edit_text').value = info.event.extendedProps.text;
              // document.getElementById('edit_date').value = info.event.startStr;
              // console.log(info);
            }
            if (info.event.role = 'staff') {
              console.log(info);
              $('#confirmModal').modal('show');
              $('#confirm-text').text(info.event.startStr + " " + info.event.title);
              $(document).ready(function() {
                var close = $('#delete-btn');
                var deleteOnClick = function() {
                  axios
                    .post("/admin/home/destroy", {
                      id: info.event.id
                    })
                    .then((response) => {
                      var event = calendar.getEventById(response.data.id)
                      event.remove();
                    })
                    .catch(() => {
                      // バリデーションエラーなど
                      alert("取得に失敗しました");
                    });
                };

                // 保存ボタンによる送信、その後イベントの解除
                close.on('click', deleteOnClick);
                $('#confirmModal').on('hidden.bs.modal', function() {
                  console.log('hidden');
                  // 第二引数に値を指定する必要がある
                  close.off('click', deleteOnClick);
                });
              });
            }
          }

          // selectable: true,
          // select: function(info) {
          //   document.getElementById('create_date').value = info.startStr;
          //   $('#createModal').modal('show');

          //   const close = document.getElementById('store-btn');
          //   const saveOnClick = () => {
          //     // 値を日付型として取得
          //     const date = document.getElementById('create_date').valueAsDate
          //     const session_time = document.getElementById('session_time');
          //     const selected_option_text = session_time.options[session_time.selectedIndex].text;
          //     // const [start_time, end_time] = selected_option_text.split(' ~ ');
          //     const user = document.getElementById('create_user').value

          //     // Laravelのaxiosから登録処理の呼び出し
          //     axios
          //       .post("/admin/home/store", {
          //         start_date: info.start.valueOf(),
          //         end_date: info.end.valueOf(),
          //         date: date,
          //         text: text,
          //         user: user,
          //         session_time: session_time,
          //       })
          //       .then((response) => {
          //         // カレンダーに読み込み
          //         calendar.addEvent({
          //           // PHP側から受け取ったevent_idをeventObjectのidにセット
          //           id: response.data.id,
          //           title: response.data.title,
          //           color: response.data.color,
          //           start: response.data.start
          //         });
          //         //renderevent();はv3まで
          //         calendar.refetchEvents();
          //         // console.log(response);
          //       })
          //       .catch(() => {
          //         // バリデーションエラーなど
          //         alert("取得に失敗しました");
          //       });
          //   };

          //   //保存ボタンによる送信、その後イベントの解除
          //   close.addEventListener('click', saveOnClick)
          //   var createModalEl = document.getElementById('createModal')
          //   createModalEl.addEventListener('hidden.bs.modal', () => {
          //     //第二引数に値を指定する必要がある
          //     close.removeEventListener('click', saveOnClick);
          //   });
          // },
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
