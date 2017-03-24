<?php

namespace App\Http\Controllers;

use Cache;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BonuslyLeaderboardController extends Controller
{

  public function showBoard() {

    if (!Cache::has('giverData') && !Cache::has('receiverData')) {

      $givenTotal = 0;
      $receivedTotal = 0;

      $users = $this->getUsers();

      $giverPointsData = [];
      $receiverPointsData = [];

      $count = count($users) -1; // Welcome user is not counted as ignored in later code c.line 50

      foreach ($users as $user) {
        if ($user->display_name != 'Welcome') {
          $givenTotal += $this->sumPoints($user, 'giving_balance');
          $receivedTotal += $this->sumPoints($user, 'earning_balance');
        }
      }

      foreach ($users as $user) {

        $giverPointsLoopData = new \stdClass;
        $receiverPointsLoopData = new \stdClass;

        if($user->display_name != 'Welcome') {
          $giverPointsLoopData->id = $user->id;
          $giverPointsLoopData->profile_pic_url = $user->profile_pic_url;
          $giverPointsLoopData->display_name = ucfirst($user->display_name);
          $giverPointsLoopData->giving_balance =  100 - $user->giving_balance;
          $giverPointsLoopData->giver_percentage =  (int)(((100 - $user->giving_balance)/$givenTotal) * 100) ;

          array_push($giverPointsData, $giverPointsLoopData);

          $receiverPointsLoopData->id = $user->id;
          $receiverPointsLoopData->profile_pic_url = $user->profile_pic_url;
          $receiverPointsLoopData->display_name = ucfirst($user->display_name);
          $receiverPointsLoopData->earning_balance = $user->earning_balance;
          $receiverPointsLoopData->receiver_percentage =  (int)(($user->earning_balance/$receivedTotal) * 100) ;

          array_push($receiverPointsData, $receiverPointsLoopData);
        }
      }

      usort($giverPointsData, function($a, $b)
      {
          return $b->giving_balance - $a->giving_balance;
      });

      usort($receiverPointsData, function($a, $b)
      {
      return $b->earning_balance - $a->earning_balance;
      });

      $giverPointsData = array_slice($giverPointsData,0, 10);
      $receiverPointsData = array_slice($receiverPointsData,0, 10);

      $expiresAt = Carbon::now()->addMinutes(10);

      Cache::put('giverPointsData', $giverPointsData, $expiresAt);
      Cache::put('receiverPointsData', $receiverPointsData, $expiresAt);
    }
    else {
      $giverPointsData = Cache::get('giverPointsData');
      $receiverPointsData = Cache::get('receiverPointsData');
    }

    return view('leaderboard', compact('giverPointsData', 'receiverPointsData'));
  }

  // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

  // 'url' => 'http://monitor.wiseserve.net',

  function sumPoints($data, $prop) {
    $temp = null;

    switch ($prop) {
      case 'giving_balance':

        // dd($data->giving_balance, $prop);
          if ($data->giving_balance > 100) {
            $data->giving_balance = 100;
          }
          if ($data->giving_balance < 0) {
            $data->giving_balance = 0;
          }
          $temp += (100 - $data->giving_balance);
      return $temp;
      break;

      case 'earning_balance':

          if ($data->earning_balance < 0) {
            $data->earning_balance = 0;
          }
          $temp += $data->earning_balance;
      return $temp;
      break;

      default:
      # code...
      break;
    }
  }

  function makeBonuslyApiCall($resource, $id = null, $role = null) {

    $bonsulyApiCall = [];

    switch ($resource) {
      case 'users':
        $url = 'https://bonus.ly/api/v1/users';
        break;

      case 'analytics':
        $url = 'https://bonus.ly/api/v1/analytics/standouts';
        break;

      default:
        # code...
        break;
    }


    $res = new \stdClass;
    $browser = new \Buzz\Browser();

    $access_token = 'e288e7aadf0e48c1d0b3a5b84699e15a';

    if ($resource == 'users' && $id == null) {
      $apiUrl = $url . '?access_token=' . $access_token . '&show_financial_data=true';
    }
    else if ($resource == 'users' && $id != null) {
      $apiUrl = $url . '/' . $id . '/bonuses'. '?access_token=' . $access_token;
    }
    else {
      $apiUrl = $url . '?access_token=' . $access_token . '&role=' . $role;
    }

    $hostResponse = $browser->get($apiUrl, []);
    $content = json_decode($hostResponse->getContent());

    $res = $content->result;

    return $res;
  }

  function getUsers() {
    return $this->makeBonuslyApiCall($resource = 'users');
  }


}
