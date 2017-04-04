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

  function makeBonuslyApiCall($url) {

    // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

    $bonsulyApiCall = [];

    // $url = 'https://bonus.ly/api/v1/users'; //old 4.4.17


    $results = new \stdClass;
    $browser = new \Buzz\Browser();

    $access_token = 'e288e7aadf0e48c1d0b3a5b84699e15a';

    $apiUrl = $url . '?access_token=' . $access_token;

    $hostResponse = $browser->get($apiUrl, []);
    $content = json_decode($hostResponse->getContent());

    $results = $content->result;

    return $results;
  }

}