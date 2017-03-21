@extends('layouts.layout')

@section('content')
  <div class="flex-center position-ref full-height">

      <div class="input-append">
        <input id="player_name" type="text" placeholder="New player"/>
        <button id="add_button" class="add_user btn" type="button">Add Player</button>
      </div>
      <table class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th>Score</th>
            <th style="width: 20px"></th>
            <th style="width: 20px"></th>
          </tr>
        @foreach($data as $d)
        <tr class="player">
          <td class="pic"><img src="{{ $d->user->profile_pic_url }}"></td>
          <td class="name">{{ ucfirst($d->user->display_name) }}</td>
          <td class="score">{{ $d->count }}</td>
          <td><i class="increment icon-heart" rel="tooltip" data-original-title="Add five points to the score"></i></td>
          <td><i class="remove icon-remove" rel="tooltip" data-original-title="Delete player"></i></td>
        </tr>
        @endforeach

              </tbody>
            </table>

      <div class="details">
        <div class="name"></div>
      </div>

      <div class="none">Click a player to select</div>
  </div>
@stop