<?php

namespace App\Calendars\General;

use Carbon\Carbon;
use Auth;

class CalendarView
{

  private $carbon;
  // ①ライブラリ Carbon を使うために、クラス内で $carbon という名前のプライベート変数を宣言するコードです。

  // Carbonライブラリのインスタンスを作成し、$date 変数で初期化した後、現在のクラスの $carbon プロパティに代入するコードです。
  function __construct($date)
  {
    $this->carbon = new Carbon($date);
  }
  // ここまでがCarbonライブラリを使うまでの準備

  // 現在の年月を取得　例）2023年2月
  public function getTitle()
  {
    return $this->carbon->format('Y年n月');
  }

  // カレンダーの表(外側)を作成している
  function render()
  {
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] = '<table class="table">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th>月</th>';
    $html[] = '<th>火</th>';
    $html[] = '<th>水</th>';
    $html[] = '<th>木</th>';
    $html[] = '<th>金</th>';
    $html[] = '<th>土</th>';
    $html[] = '<th>日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';

    // カレンダーの中身を作成している
    // 週を取得
    $weeks = $this->getWeeks();
    // 週を表示させている
    foreach ($weeks as $week) {
      $html[] = '<tr class="' . $week->getClassName() . '">';

      // 以下、日を表示させるためのコード
      $days = $week->getDays();
      foreach ($days as $day) {
        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay = $this->carbon->copy()->format("Y-m-d");

        // 過去の日か今日以降の日付かを判断し、クラスづけをしている
        if ($startDay <= $day->everyDay() && $toDay >= $day->everyDay()) {
          $html[] = '<td class="calendar-td past-day border">';
        } else {
          $html[] = '<td class="calendar-td border' . $day->getClassName() . '">';
        }
        // 日を表示させている
        $html[] = $day->render();

        // どの部に予約されているかによって、表示される文字を場合分けしている
        // 予約している予約枠があるか否かで条件分岐
        // 配列　$day->authReserveDay()に$day->everyDay()が含まれているかで条件分岐
        if (in_array($day->everyDay(), $day->authReserveDay())) {
          $reservePart = $day->authReserveDate($day->everyDay())->first()->setting_part;
          if ($reservePart == 1) {
            $reservePart = "リモ1部";
          } else if ($reservePart == 2) {
            $reservePart = "リモ2部";
          } else if ($reservePart == 3) {
            $reservePart = "リモ3部";
          }
          if ($startDay <= $day->everyDay() && $toDay >= $day->everyDay()) {
            $html[] = '<p class="m-auto p-0 w-75" style="font-size:12px">' . $reservePart . '</p>';
            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          } else {
            $html[] = '<button type="submit" form="deleteParts" class="btn btn-danger p-0 w-75" name="delete_date" style="font-size:12px" value="' . $day->authReserveDate($day->everyDay())->first()->setting_reserve . '">' . $reservePart . '</button>';
            $html[] = '<input type="hidden" name="deletePart" value="' . $day->authReserveDate($day->everyDay())->first()->setting_part . '" form="deleteParts">';
            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          }
        } else {
          if ($startDay <= $day->everyDay() && $toDay >= $day->everyDay()) {
            $html[] = '受付終了';
            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          } else {
            // カレンダーに予約枠のセレクトタグ表示
            $html[] = $day->selectPart($day->everyDay());
          }
        }

        $html[] = $day->getDate();
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';
    $html[] = '<form action="/reserve/calendar" method="post" id="reserveParts">' . csrf_field() . '</form>';
    $html[] = '<form action="/delete/calendar" method="post" id="deleteParts">' . csrf_field() . '</form>';

    return implode('', $html);
  }

  protected function getWeeks()
  {
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth();
    $lastDay = $this->carbon->copy()->lastOfMonth();
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;
    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();
    while ($tmpDay->lte($lastDay)) {
      $week = new CalendarWeek($tmpDay, count($weeks));
      $weeks[] = $week;
      $tmpDay->addDay(7);
    }
    return $weeks;
  }
}
