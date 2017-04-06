<?php

namespace App\Http\Controllers;

use Cache;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    return 'https://bonus.ly/api/v1/bonuses' . '?start_time=' . $start .'&end_time=' . $end . 'limit=500&include_children=false';
  }

  function giveUrl() {
    return    'https://bonus.ly/api/v1/users?show_financial_data=true';
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

    $access_token = 'e288e7aadf0e48c1d0b3a5b84699e15a';

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

  function getHighestReceiverPoints($users) {
    $highest = 0;
    foreach ($users as $user) {
      $highest = $this->getTheHighest($user->earning_balance, $highest);
    }
    return $highest;
  }

  function getGivenTotal($users) {
    $total = 0;
    foreach ($users as $user) {
      $total += $this->sanitisePoints($user, 'giving_balance');
    }
    return $total;
  }

  function getReceivedTotal($users) {
    $total = 0;
    foreach ($users as $user) {
      $total += $this->sanitisePoints($user, 'earning_balance');
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
        if($bonus->receiver->id == $user->id) {
          $user->received_this_month += $bonus->amount;
        }
      }
    }
    return $users;
  }

  function removeWelcomeUser($users) {
    $index = 0;
    foreach ($users as $user) {
      if ($user->display_name == 'Welcome') { // remove this user otherwise results are out by 100,000
        unset($users[$index]);
      }
      $index ++;
    }
    return $users;
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

  function getPos() {
    
  }

}