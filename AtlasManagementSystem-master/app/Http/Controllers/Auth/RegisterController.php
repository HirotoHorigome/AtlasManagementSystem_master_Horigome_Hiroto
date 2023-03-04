<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Users\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rules\KanaRule;
use App\Rules\SexRule;
use App\Rules\RoleRule;
use DB;

use App\Models\Users\Subjects;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function registerView()
    {
        $subjects = Subjects::all();
        return view('auth.register.register', compact('subjects'));
    }

    public function registerPost(Request $request)
    {
        // 年月日を結合して日付オブジェクトを作成する
        $year = $request->input('old_year');
        $month = $request->input('old_month');
        $day = $request->input('old_day');
        $birth_date = sprintf("%04d-%02d-%02d", $year, $month, $day);

        // バリデーション設定
        $rules = [
            'over_name' => 'required|string|max:10',
            'under_name' => 'required|string|max:10',
            'over_name_kana' => ['required', 'string', 'max:30', new KanaRule()],
            'under_name_kana' => ['required', 'string', 'max:30', new KanaRule()],
            'mail_address' => 'required|email|unique:users|max:100',
            'sex' => ['required', new SexRule()],
            'role' => ['required', new RoleRule()],
            'password' => 'required|string|min:8|max:20|confirmed|alpha_num',
            'password_confirmation' => 'required|string|min:8|max:20|alpha_num',
            'birth_date' => 'required|date|before_or_equal:today|after_or_equal:2000-01-01',
        ];

        // バリデーションを実行する
        $validator = Validator::make([
            'over_name' => $request->over_name,
            'under_name' => $request->under_name,
            'over_name_kana' => $request->over_name_kana,
            'under_name_kana' => $request->under_name_kana,
            'mail_address' => $request->mail_address,
            'sex' => $request->sex,
            'role' => $request->role,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
            'birth_date' => $birth_date,
        ], $rules);

        // バリデーションが失敗した場合はエラーを返す
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // トランザクション:処理のまとまりのこと、トランザクションを開始して、コミットされるまでをひとまとまりとして処理します。
        // エラーが発生したり、例外が投げられた場合は処理をなかったことにして、トランザクションの開始前に戻します。

        try {
            DB::beginTransaction();
            $old_year = $request->old_year;
            $old_month = $request->old_month;
            $old_day = $request->old_day;
            $data = $old_year . '-' . $old_month . '-' . $old_day;
            $birth_day = date('Y-m-d', strtotime($data));
            $subjects = $request->subject;

            $user_get = User::create([
                'over_name' => $request->over_name,
                'under_name' => $request->under_name,
                'over_name_kana' => $request->over_name_kana,
                'under_name_kana' => $request->under_name_kana,
                'mail_address' => $request->mail_address,
                'sex' => $request->sex,
                'birth_day' => $birth_day,
                'role' => $request->role,
                'password' => bcrypt($request->password)
            ]);
            $user = User::findOrFail($user_get->id);
            // attach：中間テーブルにアクセスし、値の追加をするメソッド
            $user->subjects()->attach($subjects);
            DB::commit();
            return view('auth.login.login');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('loginView');
        }
    }
}
