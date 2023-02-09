<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AtlasBulletinBoard</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Oswald:wght@200&display=swap" rel="stylesheet">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
</head>
<body>
  <form action="{{ route('registerPost') }}" method="POST">
    <div class="w-100 vh-100 d-flex" style="align-items:center; justify-content:center;">
      <div class="w-25 vh-75 border p-3">
        <div class="register_form">
          <div class="d-flex mt-3" style="justify-content:space-between">
            <div class="" style="width:140px">
              <label class="d-block m-0" style="font-size:13px">姓</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 over_name" name="over_name">
              </div>
            </div>
            <div class="" style="width:140px">
              <label class=" d-block m-0" style="font-size:13px">名</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 under_name" name="under_name">
              </div>
            </div>
          </div>
          <div class="d-flex mt-3" style="justify-content:space-between">
            <div class="" style="width:140px">
              <label class="d-block m-0" style="font-size:13px">セイ</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 over_name_kana" name="over_name_kana">
              </div>
            </div>
            <div class="" style="width:140px">
              <label class="d-block m-0" style="font-size:13px">メイ</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 under_name_kana" name="under_name_kana">
              </div>
            </div>
          </div>
          <div class="mt-3">
            <label class="m-0 d-block" style="font-size:13px">メールアドレス</label>
            <div class="border-bottom border-primary">
              <input type="mail" class="w-100 border-0 mail_address" name="mail_address">
            </div>
          </div>
        </div>
        <div class="mt-3">
          <input type="radio" name="sex" class="sex" value="1">
          <label style="font-size:13px">男性</label>
          <input type="radio" name="sex" class="sex" value="2">
          <label style="font-size:13px">女性</label>
          <input type="radio" name="sex" class="sex" value="3">
          <label style="font-size:13px">その他</label>
        </div>
        <div class="mt-3">
          <label class="d-block m-0 aa" style="font-size:13px">生年月日</label>
          <select class="old_year" name="old_year">
            <?php $years = array_reverse(range(2000, today()->year)); ?>
            @foreach($years as $year)
            <option value="{{ $year }}">
              {{ $year }}
            </option>
            @endforeach
          </select>
          <label style="font-size:13px">年</label>
          <select class="old_month" name="old_month">
            @foreach(range(1, 12) as $month)
            <option value="{{ $month }}">
              {{ $month }}
            </option>
            @endforeach
          </select>
          <label style="font-size:13px">月</label>
          <select class="old_day" name="old_day">
            @foreach(range(1, 31) as $day)
            <option value="{{ $day }}">
              {{ $day }}
            </option>
            @endforeach
          </select>
          <label style="font-size:13px">日</label>
        </div>
        <div class="mt-3">
          <label class="d-block m-0" style="font-size:13px">役職</label>
          <input type="radio" name="role" class="admin_role role" value="1">
          <label style="font-size:13px">教師(国語)</label>
          <input type="radio" name="role" class="admin_role role" value="2">
          <label style="font-size:13px">教師(数学)</label>
          <input type="radio" name="role" class="admin_role role" value="3">
          <label style="font-size:13px">教師(英語)</label>
          <input type="radio" name="role" class="other_role role" value="4">
          <label style="font-size:13px" class="other_role">生徒</label>
        </div>
        <div class="select_teacher d-none">
          <label class="d-block m-0" style="font-size:13px">選択科目</label>
          @foreach($subjects as $subject)
          <div class="">
            <input type="checkbox" name="subject[]" value="{{ $subject->subject }}">
            <label>{{ $subject->subject }}</label>
          </div>
          @endforeach
        </div>
        <div class="mt-3">
          <label class="d-block m-0" style="font-size:13px">パスワード</label>
          <div class="border-bottom border-primary">
            <input type="password" class="border-0 w-100 password" name="password">
          </div>
        </div>
        <div class="mt-3">
          <label class="d-block m-0" style="font-size:13px">確認用パスワード</label>
          <div class="border-bottom border-primary">
            <input type="password" class="border-0 w-100 password_confirmation" name="password">
          </div>
        </div>
        <div class="mt-5 text-right">
          <input type="submit" class="btn btn-primary register_btn" disabled value="新規登録" onclick="return confirm('登録してよろしいですか？')">
        </div>
        <div class="text-center">
          <a href="{{ route('loginView') }}">ログイン</a>
        </div>
      </div>
      {{ csrf_field() }}
    </div>
  </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="{{ asset('js/register.js') }}" rel="stylesheet"></script>
</body>
</html>
