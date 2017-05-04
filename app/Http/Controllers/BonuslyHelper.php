<?php

namespace App\Http\Controllers;

use Cache;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Models\Position;

class BonuslyHelper
{

  protected $testData = [
  0 => [
  "id" => "58ac031f7b8d8602dbc373f3",
  "username" => "nadeem",
  "type" => "giver",
  "giver_class" => "init fa fa-sun-o",
  "position" => 3,
  "given_points" => 10,
  "received_points" => 36,
  "updated_at" => "2017-04-12T18:05:29Z",
  "created_at" => "2017-02-21T09:06:39Z"
  ],

  1 => [
  "id" => "5846d6b0387f8a036bc9351c",
  "username" => "hristo",
  "type" => "giver",
  "giver_class" => "init fa fa-sun-o",
  "position" => 5,
  "given_points" => 1,
  "received_points" => 2,
  "updated_at" => "2017-04-12T18:05:29Z",
  "created_at" => "2017-02-21T09:06:39Z"
  ],

  2 => [
  "id" => "58a5d3901cd7d01bae01c427",
  "username" => "ryan",
  "type" => "giver",
  "giver_class" => "init fa fa-sun-o",
  "position" => 2,
  "given_points" => 30,
  "received_points" => 72,
  "updated_at" => "2017-04-12T18:05:29Z",
  "created_at" => "2017-02-21T09:06:39Z"
  ],

  3 => [
  "id" => "5846d65acd7fb260ebf3ca1f",
  "username" => "junaid",
  "type" => "giver",
  "giver_class" => "init fa fa-sun-o",
  "position" => 6,
  "given_points" => 10,
  "received_points" => 32,
  "updated_at" => "2017-04-12T18:05:29Z",
  "created_at" => "2017-02-21T09:06:39Z"
  ],

  4 => [
  "id" => "58a5d5681cd7d04d3001c5df",
  "username" => "emma",
  "type" => "giver",
  "giver_class" => "init fa fa-sun-o",
  "position" => 7,
  "given_points" => 25,
  "received_points" => 13,
  "updated_at" => "2017-04-12T18:05:29Z",
  "created_at" => "2017-02-21T09:06:39Z"
  ],

  5 => [
  "id" => "5846d6ab387f8a0382c9351c",
  "username" => "raphael",
  "type" => "giver",
  "giver_class" => "init fa fa-sun-o",
  "position" => 5,
  "given_points" => 30,
  "received_points" => 2,
  "updated_at" => "2017-04-12T18:05:29Z",
  "created_at" => "2017-02-21T09:06:39Z"
  ]
];

  protected $unwantedUsers = array('raphael', 'emma', 'cathren', 'hugh', 'junaid', 'bot+5846d65caaf5cb3863ae6b06');

  function receiveUrl() {
    // get current year and month
    $year  = Carbon::now()->year;
    $month = Carbon::now()->month;
    // make a partial date string
    $date = $year . '-' . $month;

    // get month start date
    $start = Carbon::parse($date)->startOfMonth()->toDateString();
    // get month end date
    $end = Carbon::parse($date)->endOfMonth()->toDateString();

    return 'https://bonus.ly/api/v1/bonuses' . '?start_time=' . $start .'&end_time=' . $end . '&limit=500&include_children=false';
  }

  function giveUrl() {
    return    'https://bonus.ly/api/v1/users?show_financial_data=true&limit=500';
  }

  function getMonth() {
    return date("F", mktime(0, 0, 0, Carbon::now()->month, 1));
  }

  function getDayNumber() {
    return Carbon::now()->day;
  }

  function makeBonuslyApiCall($url) {
    $results = new \stdClass;
    $browser = new \Buzz\Browser();

    $access_token = config('bonusly.token');

    $apiUrl = $url . '&access_token=' . $access_token;

    $hostResponse = $browser->get($apiUrl, []);

    $content = json_decode($hostResponse->getContent());

    $results = $content->result;

    return $results;
  }

  function getHighestGiverPoints($users) {
    $highest = 0;
    foreach ($users as $user) {
      $highest = $this->getTheHighest((100 - $user->giving_balance), $highest);
    }
    return $highest;
  }

  function getHighestPoints($users, $field) {
    $highest = 0;
    foreach ($users as $user) {
      if($field == 'giving_balance') {
        $highest = $this->getTheHighest(100 - $user->$field, $highest);
      }
      else {
        $highest = $this->getTheHighest($user->$field, $highest);
      }
    }
    return $highest;
  }

  function makeWidthFactor($highestReceiverPoints, $highestGiverPoints) {
    $highest = $this->getTheHighest($highestReceiverPoints, $highestGiverPoints);
    if ($highest > 100) {
      $highest = 100;
    }
    return $highest/100;
  }

  function getTheHighest($dataToCompare, $highestSoFar) {
    if ($dataToCompare > $highestSoFar)
    return $dataToCompare;
    else {
      return $highestSoFar;
    }
  }

  function getTotal($users, $field) {
    $total = 0;
    foreach ($users as $user) {
      if($field == 'giving_balance') {
        $total += 100 - $this->sanitisePoints($user, $field);
      }
      else {
        $total += $this->sanitisePoints($user, $field);
      }
    }
    return $total;
  }

  function makeMonthlyBonusData($users, $bonuses) {
    // add new property to each user - received_this_month
    foreach ($users as $user) {
      $user->received_this_month = 0;
    }

    foreach ($bonuses as $bonus) {
      foreach ($users as $user) {
        foreach ($bonus->receivers as $receiver) {
          if($receiver->id == $user->id) {
            $user->received_this_month += $bonus->amount;
          }
        }
      }
    }
    return $users;
  }

  function makeUsers() {
    return $this->removeUnwantedUsers($this->makeBonuslyApiCall($this->giveUrl()));
  }

  function removeUnwantedUsers($users) {
    $returnArray = array();
    foreach ($users as $user) {
      if (!in_array($user->username, $this->unwantedUsers, true)) {
        $returnArray[] = $user;
      }
    }
    return $returnArray;
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

      case 'received_this_month':

      if ($data->received_this_month < 0) {
        $data->received_this_month = 0;
      }
      return $data->received_this_month;
      break;

      default:
      # code...
      break;
    }
  }

  function setPositions($data, $type) {
    $pos = null;

    $posData = $this->testData;
      $new_array = array();
      foreach ($posData as $to_obj)
      {
        $new_array[] = (object)$to_obj;
      }
    $posData = $new_array;
    foreach ($data as $key => $d) {

      // $pos = Position::where('user_id', '=', $d->id)
      // ->where('type', '=', $type)
      // ->first();

      foreach ($posData as $posD) {
        // die;
        if($posD->id == $d->id) {
          $pos = $posD;
          // dump($posD->id == $d->id, $d);
        }
      }

      if($pos) {
        if (($type == 'giver' && $pos->given_points != 100 - $d->giving_balance) || ($type == 'receiver' && $pos->received_points != $d->received_this_month)) {
          if ($type == 'giver') {
            echo 'hit';
            $pos->given_points = 100 - $d->giving_balance;
            $pos = $this->checkForPositionChanges($pos, $key);
            $d->giver_class = $pos['class'];
            $pos = $pos['pos'];
            $pos->giver_class = $d->giver_class;
          }
          if ($type == 'receiver') {
            $pos->received_points = $d->received_this_month;
            $pos = $this->checkForPositionChanges($pos, $key);
            $d->receiver_class = $pos['class'];
            $pos = $pos['pos'];
            $pos->receiver_class = $d->receiver_class;
          }
            dump($pos);
          $pos->save();
        } else {
          if ($type == 'giver') {
            $d->giver_class = $pos->giver_class;
          }
          if ($type == 'receiver') {
            $d->receiver_class = $pos->receiver_class;
          }
        }
      }
      else {
        $pos = new Position;
        $pos->user_id = $d->id;
        $pos->type = $type;
        $pos->username = $d->display_name;
        $pos->position = $key;
        if($type == 'giver') {
          $pos->given_points = 100 - $d->giving_balance;
          $d->giver_class = 'init fa fa-sun-o';
          $pos->giver_class = $d->giver_class;
        }
        if($type == 'receiver') {
          $pos->received_points = $d->received_this_month;
          $d->receiver_class = 'init fa fa-sun-o';
          $pos->receiver_class = $d->receiver_class;
        }
        $pos->save();
      }
    }
    dump($data);
    return $data;
  }

  function checkForPositionChanges($pos, $newPosition) {
    // $returnArray = array();
    $oldPosition = $pos->position;
    if ($oldPosition == $newPosition) {
      $class = 'no_move fa fa-arrows-h';
    }
    if ($oldPosition < $newPosition) {
      $pos = $this->swapPlaces($pos, $newPosition, 'down');
      // $returnArray['class'] = $pos['class'];
    }
    if ($oldPosition > $newPosition) {
      $pos = $this->swapPlaces($pos, $newPosition, 'up');
    }
    // $returnArray[] = $pos;
    return $pos;
  }

  function swapPlaces($pos, $newPosition, $direction) {
    // dd($pos, $newPosition, $direction);
    $returnArray = array();

    switch ($direction) {

      case 'down':
      $returnArray['class'] = 'lower fa fa-arrow-down';
      $pos->position = $newPosition;
      $returnArray['pos'] = $pos;
      return $returnArray;

      case 'up':
      $returnArray['class'] = 'higher fa fa-arrow-up';
      $pos->position = $newPosition;
      $returnArray['pos'] = $pos;
      return $returnArray;

      default:
      # code...
      break;
    }
  }

  function setCurrentUsersOnLeaderboard() {
    // echo 'set current users';
  }

}