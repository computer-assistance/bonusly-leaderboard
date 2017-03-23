@extends('layouts.layout')

@section('content')
<div id="leaderboard_title_bar" class="container">
  <div class="row">
    <div class="col-sm-12">
      <img src="img/bonusly_header_logo_wht-1.png">
      <img src="img/Computer-Assistance-aifile_white_300x89.png" class="pull-right">
    </div>
    <div class="col-lg-5 col-lg-offset-" style="text-align: center;">
      <h2>Bonusly Givers</h2>
    </div>
    <div class="col-lg-5 col-lg-offset-1" style="text-align: center;">
      <h2>Bonusly Recievers</h2>
    </div>
  </div>
</div>



<div class="container">
  <div class="row">

    <div id="leaderboard_table" class="col-lg-5 col-lg-offset-">
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
            <td class="name">{{ $d->display_name }}</td>
            <td class="score">{{ $d->giving_balance }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>


    <!-- <div id="leaderboard_bars" class="col-lg-3">
    @foreach($receiverPointsData as $d)
      <span class="progress-bar-img"><img src="{{ $d->profile_pic_url }}"></span>
      <div class="progress box">
        <label class="progress_label">{{ $d->display_name }}</label>
        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
          {{ $d->earning_balance }}
        </div>
      </div>
    @endforeach

  </div> -->


<div id="leaderboard_table" class="col-lg-5 col-lg-offset-1">

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
        <td class="name">{{ $d->display_name }}</td>
        <td class="score">{{ $d->earning_balance }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  </table>
</div>


<!--


    <div id="leaderboard_bars" class="col-lg-3">
    @foreach($giverPointsData as $d)
      <span class="progress-bar-img"><img src="{{ $d->profile_pic_url }}"></span>
      <div class="progress box">
        <label class="progress_label">{{ $d->display_name }}</label>
        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
          {{ $d->giving_balance }}
        </div>
      </div>
    @endforeach
    </div> -->


  </div>
</div>
@stop