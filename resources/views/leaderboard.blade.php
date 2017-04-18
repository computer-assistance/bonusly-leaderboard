@extends('layouts.layout')

@section('content')
<div id="leaderboard_title_bar" class="container">
  <div class="row">
    <div class="col-sm-12">
      <div class="row">
        <div class="col-lg-3 col-lg-offset-1">
          <img src="img/bonusly_header_logo_wht-1_grad_green2.png">
        </div>
        <div id="my_calendar" class="col-lg-1 col-lg-offset-1">
          <h1 class="month_label">{{ $thisMonth }}</h1>
          <span class="my_day">{{ $thisDay }}</span>
        </div>
        <div class="col-lg-3 col-lg-offset-1">
          <img src="img/Computer-Assistance-aifile_white_300x89_grad_green2.png" class="pull-right">
        </div>
      </div>
    </div>
  </div>


    <!-- Junaid said hide monthly totals -->
    <div class="row">
     <div class="col-lg-4 col-lg-offset-1" style="text-align: center;">
      <h2 class="my_title">Givers</h2>
    <!--  <h3>Total {{ $givenTotal }} points </h3>  -->
    </div>
    <div class="col-lg-5 col-lg-offset-1" style="text-align: center;">
      <h2 class="my_title">Receivers</h2>
    <!--  <h3>Total {{ $receivedTotal }} points </h3> -->
    </div>

  </div>
</div>

<div id="progress_bars" class="container">
  <div class="row">

    <div class="col-lg-6">
      <div class="col-lg-10 col-lg-offset-4">
        <div class="leaderboard_bars">
        @foreach($giverPointsData as $d)
        <i class="position {{ \App\Models\Position::getCurrentGiverClass($d->id) }}"></i>
        <span class="progress-bar-img"><img src="{{ $d->profile_pic_url }}"></span>
        <div class="progress">
          <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: {{ (((100 - $d->giving_balance)/$divisor) * $widthFactor) * 75 }}%;">
            {{ 100 - $d->giving_balance }}
          </div>
          <div class="progress-shadow" style="width: {{ (((100 - $d->giving_balance)/$divisor) * $widthFactor) * 75 }}%;"></div>
          <label class="progress_label" style="width: 25%;">{{ ucfirst($d->display_name) }}</label>
        </div>
        @endforeach
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="col-lg-10 col-lg-offset-2">
        <div class="leaderboard_bars">
        @foreach($receiverPointsData as $d)
        <i class="position {{ \App\Models\Position::getCurrentReceiverClass($d->id) }}"></i>
        <span class="progress-bar-img"><img src="{{ $d->profile_pic_url }}"></span>
        <div class="progress">
          <div class="progress-bar" style="width: {{ (($d->received_this_month/$divisor) * $widthFactor) * 75 }}%;">
            {{ $d->received_this_month }}
          </div>
          <div class="progress-shadow" style="width: {{ (($d->received_this_month/$divisor) * $widthFactor) * 75 }}%;"></div>
          <label class="progress_label" style="width: 25%;">{{ ucfirst($d->display_name) }}</label>
        </div>
        @endforeach
        </div>
      </div>
    </div>

  </div>
</div>

<div id="tables" class="container">
  <div class="row">

    <div id="" class="col-lg-4 col-lg-offset-1">
      <div class="leaderboard_table">
        <table class="table">
          <thead>
            <tr>
              <th>Pos</th>
              <th></th>
              <th>Name</th>
              <th>Given</th>
            <tr>
          </thead>
          <tbody>
            @foreach($giverPointsData as $indexKey => $d)
            <tr class="player">
              <th>{{ $indexKey +1 }}</th>
              <td class="pic"><img src="{{ $d->profile_pic_url }}"></td>
              <td class="name">{{ ucfirst($d->display_name) }}</td>
              <td class="score">{{ 100 - $d->giving_balance }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div id="" class="col-lg-4 col-lg-offset-1">
      <div class="leaderboard_table">
        <table class="table">
          <thead>
            <tr>
              <th>Pos</th>
              <th></th>
              <th>Name</th>
              <th>Received</th>
            </tr>
          </thead>
          <tbody>
            @foreach($receiverPointsData as $indexKey => $d)
            <tr class="player">
              <th>{{ $indexKey +1 }}</th>
              <td class="pic"><img src="{{ $d->profile_pic_url }}"></td>
              <td class="name">{{ ucfirst($d->display_name) }}</td>
              <td class="score">{{ $d->received_this_month }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
  @stop