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
  protected $unwantedUsers = array('raphael', 'emma', 'cathren', 'hugh', 'junaid', 'bot+5846d65caaf5cb3863ae');

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
    // if (strpos($url, "bonuses")) {
    //   $results = $this->makeTestBonuses($results);
    // }
    return $results;
  }

  public function makeTestBonuses($results)
  {
    $min=0;
    $max = count($results);
    // $index =
    $result = $results[rand($min,$max)];
    // echo $result->id;
    $result->id = str_shuffle($result->id);
    // dump($result);
    echo "giver-> " . $result->giver->username ."\r\n";
    foreach ($result->receivers as $receiver) {
      echo "receiver-> " . $result->giver->username ."\r\n";
    }
    // echo "\n" . $result->id;
    $results[] = $result;
    // dd(count($results));
    // die;
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

    foreach ($data as $key => $d) {
      $pos = Position::where('user_id', '=', $d->id)
      ->where('type', '=', $type)
      ->first();
      if($pos) {
        $d = $this->getPositionClass($d, $pos);
        if (($type == 'giver' && $pos->given_points != 100 - $d->giving_balance) || ($type == 'receiver' && $pos->received_points != $d->received_this_month)) {

          if ($type == 'giver') {
            $pos->given_points = 100 - $d->giving_balance;
            $pos = $this->checkForPositionChanges($pos, $key + 1, 'giver_class');
            $d->giver_class = $pos->giver_class;
          }

          if ($type == 'receiver') {
            $pos->received_points = $d->received_this_month;
            $pos = $this->checkForPositionChanges($pos, $key + 1, 'receiver_class');
            $d->receiver_class = $pos->receiver_class;
          }
          $pos->save();
        }
      }
      else {
        $pos = new Position;
        $pos->user_id = $d->id;
        $pos->type = $type;
        $pos->username = $d->display_name;
        $pos->old_position = $key + 1;
        $pos->giver_class = 'init fa fa-sun-o';
        $pos->receiver_class = 'init fa fa-sun-o';

        if($type == 'giver') {
          $pos->given_points = 100 - $d->giving_balance;
          $d->giver_class = $pos->giver_class;
        }

        if($type == 'receiver') {
          $pos->received_points = $d->received_this_month;
          $d->receiver_class = $pos->receiver_class;
        }
        $pos->save();
      }
    }
    return $data;
  }

  public function getPositionClass($d, $pos) {
    if($pos->type == "giver") {
      $d->giver_class = $pos->giver_class;
    }
    if($pos->type == "receiver") {
      $d->receiver_class = $pos->receiver_class;
    }
    return $d;
  }

  function checkForPositionChanges($pos, $newPosition, $type) {
    $oldPosition = $pos->old_position;
    if ($oldPosition == $newPosition) {
      $pos->$type = 'no_move fa fa-arrows-h';
    }
    if ($oldPosition < $newPosition) {
      $this->swapPlaces($pos, $newPosition, 'down', $type);
    }
    if ($oldPosition > $newPosition) {
      $this->swapPlaces($pos, $newPosition, 'up', $type);
    }
    return $pos;
  }

  function swapPlaces($pos, $newPosition, $direction, $type) {

    switch ($direction) {

      case 'down':
      $pos->old_position = $newPosition;
      $pos->$type = 'lower fa fa-arrow-down';
      return $pos;

      case 'up':
      $pos->$type = 'higher fa fa-arrow-up';
      $pos->old_position = $newPosition;
      return $pos;
    }
  }
}