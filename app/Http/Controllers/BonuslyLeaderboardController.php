<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;

class BonuslyLeaderboardController extends Controller
{
    public function showBoard() {
      // dd($request);
      // echo('hi');

      // $max = $this->getPercentages($this->setupBonuslyApiCall());
      $max = $this->getPercentages([0,1,9,5,3,12,32,5,6,12,232,4,34]);
       dd($max);
      $data = $this->setupBonuslyApiCall();

      return view('leaderboard', compact('data', 'max'));

    }



      // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

      // 'url' => 'http://monitor.wiseserve.net',


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
        //dd($apiUrl);
        $hostResponse = $browser->get($apiUrl, []);
        $content = json_decode($hostResponse->getContent());

        //dd($content);
        $res = $content->result;
        // dd($h);
        return $res;
      }

      function getPercentages($data) {
        $res = new \stdClass;
        $hi = null;
        $count = 0;
        foreach ($data as $d) {
          echo $d;
          if ($hi == null || $d > $hi) {
            $hi = $d;
          }
          $count++;
        }
        $res->hi = $hi;
        $res->count = $count;
        return $res;
      }
}
