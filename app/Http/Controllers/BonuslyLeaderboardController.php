<?php

namespace App\Http\Controllers;

use Cache;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\BonuslyHelper;

class BonuslyLeaderboardController extends Controller
{

  public function __construct()
  {
    //Load helper functions
    $this->bonusHelper = new BonuslyHelper();
  }

  public function showBoard() {

    if (!Cache::has('giverPointsData') && !Cache::has('receiverPointsData')) {

      $givenTotal = 0;
      $receivedTotal = 0;

      $giverPointsData = [];
      $receiverPointsData = [];
      $highestGiverPoints = 0;
      $highestReceiverPoints = 0;

      $users = $this->getUsers();
      $index = 0;

      $bonuses = $this->bonusHelper->makeBonuslyApiCall($this->bonusHelper->receiveUrl());
      dd($bonuses);


      foreach ($users as $user) {
        if ($user->display_name != 'Welcome') { // remove this user otherwise results are out by 100,000
          $highestGiverPoints = $this->getTheHighest((100 - $user->giving_balance), $highestGiverPoints);
          $highestReceiverPoints = $this->getTheHighest(($user->earning_balance), $highestReceiverPoints);
          $givenTotal += $this->sanitisePoints($user, 'giving_balance');
          $receivedTotal += $this->sanitisePoints($user, 'earning_balance');
        }
        else {
          unset($users[$index]);
        }
        $index++;
      }

      $giverPointsData = $users;
      $receiverPointsData = $users;


      usort($giverPointsData, function($a, $b)
      {
      return $a->giving_balance - $b->giving_balance;
      });

      usort($receiverPointsData, function($a, $b)
      {
      return $b->earning_balance - $a->earning_balance;
      });

      $giverPointsData = array_slice($giverPointsData,0, 10); // limit to top ten
      $receiverPointsData = array_slice($receiverPointsData,0, 10);
      $divisor = 0;
      $divisor = $this->getTheHighest($highestReceiverPoints, $highestGiverPoints);
      // dd($giverPointsData, $receiverPointsData);

      $expiresAt = Carbon::now()->addMinutes(0);

      Cache::put('giverPointsData', $giverPointsData, $expiresAt);
      Cache::put('receiverPointsData', $receiverPointsData, $expiresAt);
      Cache::put('givenTotal', $givenTotal, $expiresAt);
      Cache::put('receivedTotal', $receivedTotal, $expiresAt);
      Cache::put('divisor', $divisor, $expiresAt);
    }
    else {
      $giverPointsData = Cache::get('giverPointsData');
      $receiverPointsData = Cache::get('receiverPointsData');
      $givenTotal = Cache::get('givenTotal');
      $receivedTotal = Cache::get('receivedTotal');
      $divisor = Cache::get('divisor');

    }

    return view('leaderboard', compact('giverPointsData', 'receiverPointsData', 'givenTotal', 'receivedTotal', 'divisor'));
  }


  function sanitisePoints($data, $prop) {

    switch ($prop) {
      case 'giving_balance':

        if ($data->giving_balance > 100) {
          $data->giving_balance = 100;
        }
        if ($data->giving_balance < 0) {
          $data->giving_balance = 0;
        }
      return $data->giving_balance;
      break;

      case 'earning_balance':

        if ($data->earning_balance < 0) {
          $data->earning_balance = 0;
        }
        return $data->earning_balance;
      break;

      default:
      # code...
      break;
    }
  }

  function getTheHighest($dataToCompare, $highestSoFar) {
    if ($dataToCompare > $highestSoFar)
      return $dataToCompare;
    else {
      return $highestSoFar;
    }
  }



  function makeBonuslyApiCall2() {

    // get current year and month
    $year  = Carbon::now()->year;
    $month = Carbon::now()->month;
    // make a partial date string
    $date = $year . '-' . $month;

    // get month start date
    $start = Carbon::parse($date)->startOfMonth()->toDateString();
    // get month end date
    $end = Carbon::parse($date)->endOfMonth()->toDateString();

    // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

    $bonsulyApiCall = [];

    // $url = 'https://bonus.ly/api/v1/users'; //old 4.4.17
    $url = 'https://bonus.ly/api/v1/bonuses';

    $res = new \stdClass;
    $browser = new \Buzz\Browser();

    $access_token = 'e288e7aadf0e48c1d0b3a5b84699e15a';

    $apiUrl = $url . '?access_token=' . $access_token . '&start_time=' . $start .'&end_time=' . $end . 'limit=500&include_children=false';

    $hostResponse = $browser->get($apiUrl, []);
    $content = json_decode($hostResponse->getContent());

    $res = $content->result;

    return $res;
  }

  function getUsers() {
    return $this->bonusHelper->makeBonuslyApiCall();
  }


}
