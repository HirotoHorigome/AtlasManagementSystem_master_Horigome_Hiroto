<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Users\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rules\SexRule;
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
        // トランザクション:処理のまとまりのこと、トランザクションを開始して、コミットされるまでをひとまとまりとして処理します。
        // エラーが発生したり、例外が投げられた場合は処理をなかったことにして、トランザクションの開始前に戻します。
        $old_year = $request->old_year;
        $old_month = $request->old_month;
        $old_day = $request->old_day;
        $data = $old_year . '-' . $old_month . '-' . $old_day;
        $birth_day = date('Y-m-d', strtotime($data));
        $subjects = $request->role;

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

        // バリデーション追加後上と差し替え
        // try {
        //     DB::beginTransaction();
        //     $old_year = $request->old_year;
        //     $old_month = $request->old_month;
        //     $old_day = $request->old_day;
        //     $data = $old_year . '-' . $old_month . '-' . $old_day;
        //     $birth_day = date('Y-m-d', strtotime($data));
        //     $subjects = $request->subject;

        //     $user_get = User::create([
        //         'over_name' => $request->over_name,
        //         'under_name' => $request->under_name,
        //         'over_name_kana' => $request->over_name_kana,
        //         'under_name_kana' => $request->under_name_kana,
        //         'mail_address' => $request->mail_address,
        //         'sex' => $request->sex,
        //         'birth_day' => $birth_day,
        //         'role' => $request->role,
        //         'password' => bcrypt($request->password)
        //     ]);
        //     $user = User::findOrFail($user_get->id);
        //     // attach：中間テーブルにアクセスし、値の追加をするメソッド
        //     $user->subjects()->attach($subjects);
        //     DB::commit();
        //     return view('auth.login.login');
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return redirect()->route('loginView');
        // }
    }
}
