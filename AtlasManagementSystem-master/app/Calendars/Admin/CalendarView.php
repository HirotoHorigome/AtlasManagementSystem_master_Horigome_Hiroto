<?php

namespace App\Calendars\Admin;

use Carbon\Carbon;
use App\Models\Users\User;

class CalendarView
{
  // ①ライブラリ Carbon を使うために、クラス内で $carbon という名前のプライベート変数を宣言するコードです。
  private $carbon;

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


  public function render()
  {
    // カレンダーの表(外側)を作成している
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] = '<table class="table m-auto border">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th class="border">月</th>';
    $html[] = '<th class="border">火</th>';
    $html[] = '<th class="border">水</th>';
    $html[] = '<th class="border">木</th>';
    $html[] = '<th class="border">金</th>';
    $html[] = '<th class="border">土</th>';
    $html[] = '<th class="border">日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';

    // カレンダーの中身を作成している
    // 週を取得
    $weeks = $this->getWeeks();
    // 週を表示させている
    foreach ($weeks as $week) {
      $html[] = '<tr class="' . $week->getClassName() . '">';
      $days = $week->getDays();
      // 日を表示させる
      foreach ($days as $day) {
        // 月の最初の日を$startDayに代入
        $startDay = $this->carbon->format("Y-m-01");
        // 今日の日付を＄toDayに代入
        $toDay = $this->carbon->format("Y-m-d");
        // 過去の日か今日以降の日付かを判断し、クラスづけをしている
        if ($startDay <= $day->everyDay() && $toDay >= $day->everyDay()) {
          $html[] = '<td class="past-day border">';
        } else {
          $html[] = '<td class="border ' . $day->getClassName() . '">';
        }
        // カレンダーの中身(日付)を作成
        $html[] = $day->render();
        // スクールの残り枠数を表示している？
        $html[] = $day->dayPartCounts($day->everyDay());
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';

    return implode("", $html);
  }
  // その月の週を取得している
  protected function getWeeks()
  {
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth();
    $lastDay = $this->carbon->copy()->lastOfMonth();
    // 最初の週のカレンダー週 ($week) を作成し、カレンダー週の配列 ($weeks) に追加する
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;
    // ◉2週目以降、1週間後の日付 ($tmpDay) を取得し、その週のカレンダー週を作成し、カレンダー週の配列に追加する。
    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();
    // 最後の日付に到達するまで、◉を繰り返す。
    while ($tmpDay->lte($lastDay)) {
      $week = new CalendarWeek($tmpDay, count($weeks));
      $weeks[] = $week;
      $tmpDay->addDay(7);
    }
    return $weeks;
  }
}
