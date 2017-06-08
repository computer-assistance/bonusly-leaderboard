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

    return 'https://bonus.ly/api/v1/bonuses' . '?start_time=' . $start .'&end_time=' . $end . '&limit=499&include_children=false';
  }

  function giveUrl() {
    return    'https://bonus.ly/api/v1/users?show_financial_data=true&limit=499';
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

    $unwantedUsers = explode('-', config('bonusly.unwantedUsers'));

    $returnArray = array();
    foreach ($users as $user) {
      if (!in_array($user->username, $unwantedUsers, true)) {
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

  function checkForPositionChanges($userArray, $type) {
// dd($userArray);
    $userAttribueToAdd = $type.'_class';

    foreach ($userArray as $key => $user) {

      $pos =  Position::where('type', $type)->where('user_id', $user->id)->first();

      if($key+1 > $pos->old_position) {
        $user->$userAttribueToAdd = $this->getPositionClass('down');
      }

      if($key+1 == $pos->old_position) {
        $user->$userAttribueToAdd = $this->getPositionClass('same');
      }

      if($key+1 < $pos->old_position) {
        $user->$userAttribueToAdd = $this->getPositionClass('up');
      }

      $pos->old_position = $key+1;
      $pos->save();
    }
    return $userArray;
  }

  function getPositionClass($evidence) {

    switch ($evidence) {

      case 'down':
        return 'lower fa fa-arrow-down';
        break;

      case 'up':
        return 'higher fa fa-arrow-up';
        break;

      case 'same':
        return 'no_move fa fa-arrows-h';
        break;

      default:
        # code...
        break;
    }

  }

  function storePosition($position) {

  }
}
