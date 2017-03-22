<?php

namespace App\Http\Controllers;

use Cache;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BonuslyLeaderboardController extends Controller
{
  public function showBoard() {

    if (!Cache::has('bonusData')) {
      $data = new \stdClass;

      $data1 = $this->setupBonuslyApiCall();
      $data = $this->analyseData($this->setupBonuslyApiCall());

      $expiresAt = Carbon::now()->addMinutes(2);
			Cache::put('data', $data, $expiresAt);
    }
    else {
      $data = Cache::get('data');
    }
    return view('leaderboard', compact('data'));
  }

  // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

  function setupBonuslyApiCall() {

    $browser = new \Buzz\Browser();
    // dd($browser);
    $bonsulyApiCall = array(
      'url'           => 'https://bonus.ly/api/v1/analytics/standouts',
      'access_token'  => 'e288e7aadf0e48c1d0b3a5b84699e15a'

    );

    // $format = 'json';

    $url = $bonsulyApiCall['url'];
    $access_token = $bonsulyApiCall['access_token'];

    $apiUrl = $url.'?access_token='.$access_token;

    $hostResponse = $browser->get($apiUrl, []);
    $content = json_decode($hostResponse->getContent());

    $res = $content->result;

    return $res;
  }

  function analyseData($data) {
    $res = [];
    $hi = null;
    $values = [];
    $count = 0;
    $total = 0;
    $percentages = [];
    $size = count($data);
    // dump($data);
    foreach ($data as $d) {
      if ($hi == null || $d->count > $hi) {
        $hi = $d->count;
      }
      $total+= $d->count;
      $count++;
    }
    foreach ($data as $key => $val) {
      $res2 = new \stdClass;
      $res2 = $val;
      $res2->percentage = $this->getPrecent($total, $val->count);
      array_push($values, $val->count);
      array_push($res, $res2);
    }

    $res = (object)$res;

    // $res->percentages = $this->getPercentages($total, $values);

    // $res->hi = $hi;
    // $res->count = $count;
    // $res->percentages = $values;
    // $res->total = $total;
    return (array)$res;
  }

  function getPercentages($total, $data) {
    $numItems = count($data);
    $ret = [];
    foreach ($data as $d) {
      array_push($ret, (int)(($d/$total)*100));
    }
    return $ret;
  }

  function getPrecent($numItems, $value) {
    return (int)(($value/$numItems)*100);
  }
}
