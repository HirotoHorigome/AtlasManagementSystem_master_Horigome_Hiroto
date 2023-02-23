<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Calendars\Calendar;
use App\Models\USers\User;
use Auth;
use DB;

class CalendarsController extends Controller
{
    public function show()
    {
        $calendar = new CalendarView(time());
        return view('authenticated.calendar.general.calendar', compact('calendar'));
    }

    public function reserve(Request $request)
    {
        DB::beginTransaction();
        try {
            $getPart = $request->getPart;
            $getDate = $request->getData;
            // 配列のみを取り出している
            $reserveDays = array_filter(array_combine($getDate, $getPart));
            // keyが日付　getDate(例：2023-02-28)、valueが部　getPart(例　リモ1)　
            foreach ($reserveDays as $key => $value) {
                // 日付と部が等しいものを取り出す
                $reserve_settings = ReserveSettings::where('setting_reserve', $key)->where(
                    'setting_part',
                    $value
                )->first();
                //その部の予約枠数を減らす
                $reserve_settings->decrement('limit_users');
                // 中間テーブルにデータ作成
                $reserve_settings->users()->attach(Auth::id());
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
        return redirect()->route('calendar.general.show', ['user_id' => Auth::id()]);
    }

    //以下、予約枠削除コード
    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $getPart = $request->getPart;
            // 配列のみを取り出している
            $deleteDate = $request->delete_date;
            // 日付と部が等しいものを取り出す
            $delete_settings = ReserveSettings::where('setting_reserve', $deleteDate)->where('setting_part', $getPart)->first();
            //その部の予約枠数を増やす
            $delete_settings->increment('limit_users');
            // 中間テーブルにデータ削除
            $delete_settings->users()->detach(Auth::id());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
        return redirect()->route('calendar.general.show', ['user_id' => Auth::id()]);
    }
}
