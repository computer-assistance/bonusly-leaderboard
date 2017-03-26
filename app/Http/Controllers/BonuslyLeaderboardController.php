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

      $earners = [];

      $users = $this->getUsers();

      $giverPointsData = [];
      $receiverPointsData = [];
      $highestGiverPoints = 0;
      $highestReceiverPoints = 0;

      $index = 0;
      foreach ($users as $user) {
        if ($user->display_name != 'Welcome') {
          $highestGiverPoints = $this->getTheHighest((100 - $user->giving_balance), $highestGiverPoints);
          $highestReceiverPoints = $this->getTheHighest(($user->earning_balance), $highestReceiverPoints);
          $givenTotal += $this->sumPoints($user, 'giving_balance');
          $receivedTotal += $this->sumPoints($user, 'earning_balance');
        }
        else {
          unset($users[$index]);
        }
        $index++;
      }

      // dd($highestReceiverPoints);
      /* the main sub-routine
      *
      * loop through returned collection to create 2 arrays of specific data
      *
      *
      */
       $giverPointsData = $users;
       $receiverPointsData = $users;

      // foreach ($users as $user) {
      //
      //   $giverPointsLoopData = new \stdClass;
      //   $receiverPointsLoopData = new \stdClass;
      //
      //   if($user->display_name != 'Welcome') {
      //     $giverPointsLoopData->id = $user->id;
      //     $giverPointsLoopData->profile_pic_url = $user->profile_pic_url;
      //     $giverPointsLoopData->display_name = ucfirst($user->display_name);
      //     $giverPointsLoopData->giving_balance =  100 - $user->giving_balance;
      //     $giverPointsLoopData->giver_percentage =  (int)(((100 - $user->giving_balance)/$givenTotal) * 100) ;
      //
      //     array_push($giverPointsData, $giverPointsLoopData);
      //
      //     $receiverPointsLoopData->id = $user->id;
      //     $receiverPointsLoopData->profile_pic_url = $user->profile_pic_url;
      //     $receiverPointsLoopData->display_name = ucfirst($user->display_name);
      //     $receiverPointsLoopData->earning_balance = $user->earning_balance;
      //     $receiverPointsLoopData->receiver_percentage =  (int)(($user->earning_balance/$receivedTotal) * 100) ;
      //
      //     array_push($receiverPointsData, $receiverPointsLoopData);
      //   }
      // }

      // dump('eranersb4->', $earners);
      // usort($earners, function($a, $b)
      // {
      //     return strcmp($b->earning_balance, $a->earning_balance);
      // });
      //
      //
      // usort($giverPointsData, function($a, $b)
      // {
      //     return $b->giving_balance - $a->giving_balance;
      // });


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

      $expiresAt = Carbon::now()->addMinutes(10);

      Cache::put('giverPointsData', $giverPointsData, $expiresAt);
      Cache::put('receiverPointsData', $receiverPointsData, $expiresAt);
      Cache::put('givenTotal', $givenTotal, $expiresAt);
      Cache::put('receivedTotal', $receivedTotal, $expiresAt);
    }
    else {
      $giverPointsData = Cache::get('giverPointsData');
      $receiverPointsData = Cache::get('receiverPointsData');
      $givenTotal = Cache::get('givenTotal');
      $receivedTotal = Cache::get('receivedTotal');
    }

    return view('leaderboard', compact('giverPointsData', 'receiverPointsData', 'givenTotal', 'receivedTotal', 'highestGiverPoints', 'highestReceiverPoints', 'divisor'));
  }


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
          // echo "d->gb -> $data->giving_balance ,calc-> ";
          // echo (100 - $data->giving_balance);
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

  function getTheHighest($dataToCompare, $highestSoFar) {
    if ($dataToCompare > $highestSoFar)
      return $dataToCompare;
    else {
      return $highestSoFar;
    }
  }

  function makeBonuslyApiCall() {

    // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

    $bonsulyApiCall = [];

    $url = 'https://bonus.ly/api/v1/users';

    $res = new \stdClass;
    $browser = new \Buzz\Browser();

    $access_token = 'e288e7aadf0e48c1d0b3a5b84699e15a';

    $apiUrl = $url . '?access_token=' . $access_token . '&show_financial_data=true';

    $hostResponse = $browser->get($apiUrl, []);
    $content = json_decode($hostResponse->getContent());

    $res = $content->result;

    return $res;
  }

  function getUsers() {
    return $this->makeBonuslyApiCall();
  }


}
