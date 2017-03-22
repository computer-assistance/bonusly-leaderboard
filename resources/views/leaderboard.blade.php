@extends('layouts.layout')

@section('content')
<div id="leaderboard_title_bar" class="container">
  <div class="row">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <img src="img/bonusly_header_logo_wht-1.png">
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 pull-right" style="text-align: center;">
      <h2>Bonusly Givers</h2>
    </div>
  </div>
</div>
  <div class="container">
    <div class="row">
      <div id="leaderboard_bars" class="col-lg-8">
          @foreach($data as $d)
          <div class="progress">
            <label class="progress_label">{{ ucfirst($d->user->display_name) }}</label>
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="$d->percentage" aria-valuemin="0" aria-valuemax="100" style="width: {{ $d->percentage }}%;">
              {{ $d->percentage }}%
            </div>
          </div>
          @endforeach
    </div>
    <div id="leaderboard_table" class="col-lg-3 col-md-12 pull-right">

      <table class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th>Score</th>
            <th style="width: 20px">%</th>
            <th style="width: 20px"></th>
          </tr>
                @foreach($data as $d)
                <tr class="player">
                  <td class="pic"><img src="{{ $d->user->profile_pic_url }}"></td>
                  <td class="name">{{ ucfirst($d->user->short_name) }}</td>
                  <td class="score">{{ $d->count }}</td>
                  <td><i class="increment icon-heart" rel="tooltip" data-original-title="Add five points to the score">{{ $d->percentage  }}</i></td>
                  <td><i class="remove icon-remove" rel="tooltip" data-original-title="Delete player"></i></td>
                </tr>
                @endforeach
              </tbody>
            </table>
    </div>
    </div>
  </div>
@stop