import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import { Modal } from 'bootstrap';
import * as holiday_jp from '@holiday-jp/holiday_jp';

var createModalEl = document.getElementById('createModal')
var editModalEl = document.getElementById('editModal')
// document.addEventListener('DOMContentLoaded', function() {
//     var createModal = new Modal(createModalEl,{});
//     var editModal = new Modal(editModalEl,{});
// });
var calendarEl = document.getElementById('shift');

let calendar = new Calendar(calendarEl, {
  //表示テーマ
  themeSystem: 'bootstrap',
  contentHeight: '75vh',
  plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin ],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    // right: 'dayGridMonth,timeGridWeek,listWeek'
    right: 'dayGridMonth,listMonth'
  },
  // スマホでタップしたとき即反応
  selectLongPressDelay:0,
  locale: 'ja',
  buttonText: {
    today: '今月',
    month: '月',
    list: 'リスト'
  },
  // all-day表示を終日にする
  allDayText: '終日',
  // デフォルトの6週間表示を自動調整
  fixedWeekCount: false,

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

      let arrayOfDomNodes = [ hol_tag ]
      return { domNodes: arrayOfDomNodes }
    }else{
      //日本語化の日表示を外す
      arg.dayNumberText = arg.dayNumberText.replace('日', '');
      return arg.dayNumberText;
    }
  },

  eventDidMount: function(mountArg) {
    const el = mountArg.el
    if ( mountArg.view.type == "listMonth" ) {
      const date = new Date();
      date.setFullYear(
        mountArg.event.start.getFullYear(),
        mountArg.event.start.getMonth(),
        mountArg.event.start.getDate()
        );
        const holiday = holiday_jp.between(new Date(date), new Date(date));
        if (holiday[0]) {
          console.log(holiday);
          el.previousSibling.classList.add('fc-day-hol');
        }
    };
  },

//   events: function (info, successCallback, failureCallback) {
//     const startDate = new Date();
//     const endDate = new Date();
//     startDate.setFullYear(
//       info.start.getFullYear(),
//       info.start.getMonth(),
//       info.start.getDate()
//     );
//     endDate.setFullYear(
//       info.end.getFullYear(),
//       info.end.getMonth(),
//       info.end.getDate()
//     );

//     const holidays = holiday_jp.between(new Date(startDate), new Date(endDate));
    
//     // Laravelのイベント取得処理の呼び出し
//     axios
//         .post("/dashboard/shift/show", {
//             start_date: info.start.valueOf(),
//             end_date: info.end.valueOf(),
//         })
//         .then((response) => {
//             // 一旦全てのイベントを削除
//             calendar.removeAllEvents();
//             // カレンダーに読み込み
//             successCallback(response.data);
//             console.log(response.data);
//         })
//         .catch(() => {
//             // バリデーションエラーなど
//             alert("取得に失敗しました");
//         });
//     axios
//         .post("/dashboard/shift/show-all", {
//             start_date: info.start.valueOf(),
//             end_date: info.end.valueOf(),
//         })
//         .then((response) => {
//           // カレンダーに読み込み
//           response.data.map((el) => {
//             calendar.addEvent({
//               // PHP側から受け取ったevent_idをeventObjectのidにセット
//               id:el.id,
//               title:el.title,
//               color:el.color,
//               start: el.start,
//               // text:el.extendedProps.text
//             });
//             // console.log(el);
//           });
//           // calendar.refetchEvents();
//           console.log(response.data);
//         })
//         .catch(() => {
//             // バリデーションエラーなど
//             alert("取得に失敗しました");
//         });
//     },
    
//     selectable: true,
//     select: function (info) {
//       // document.getElementById('createModal_description').innerText = info.startStr;
//       document.getElementById('create_date').value = info.startStr;
//       createModal.show();
      
//       const session_times = document.create_form.session_times
//       console.log(session_times);
//       const close = document.getElementById('store-btn');
//       const saveOnClick = () => {
//         // 値を日付型として取得
//         const date = document.getElementById('create_date').valueAsDate
//         const text = document.getElementById('create_text').value

//         // Laravelのaxiosから登録処理の呼び出し
//         axios
//             .post("/dashboard/shift/store", {
//                 start_date: info.start.valueOf(),
//                 end_date: info.end.valueOf(),
//                 date: date,
//                 text: text,
//                 list: [0,1,2]
//               })
//             .then((response) => {
//                 // カレンダーに読み込み
//                 calendar.addEvent({
//                   // PHP側から受け取ったevent_idをeventObjectのidにセット
//                   id:response.data.id,
//                   title:response.data.title,
//                   color:response.data.color,
//                   start: response.data.start
//               });
//               //renderevent();はv3まで
//               calendar.refetchEvents();
//               // console.log(response);
//             })
//             .catch(() => {
//                 // バリデーションエラーなど
//                 alert("取得に失敗しました");
//             });
//       };

//       //保存ボタンによる送信、その後イベントの解除
//       close.addEventListener('click', saveOnClick)
//       createModalEl.addEventListener('hidden.bs.modal', () => {
//         //第二引数に値を指定する必要がある
//         close.removeEventListener('click', saveOnClick);
//       });
//     },

//   eventClick: function(info) {
//     document.getElementById('edit_id').value = info.event.id;
//     document.getElementById('delete_id').value = info.event.id;
//     document.getElementById('edit_text').value = info.event.extendedProps.text;
//     document.getElementById('edit_date').value = info.event.startStr;
//     console.log(info.event);
//     editModal.show();
//   }
});

calendar.render();