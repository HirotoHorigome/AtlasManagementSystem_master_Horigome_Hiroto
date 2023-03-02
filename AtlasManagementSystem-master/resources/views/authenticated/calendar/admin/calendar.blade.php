@extends('layouts.sidebar')

@section('content')
<div class="w-75 m-auto">
  <div class="calender_admin w-100">
    <h4 class="calender_admin_title">{{ $calendar->getTitle() }}</h4>
    <p>{!! $calendar->render() !!}</p>
  </div>
</div>
@endsection
