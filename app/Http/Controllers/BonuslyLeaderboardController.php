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

      $userReceivedTotals = [];
      $userGivenTotals = [];


      $giverData = $this->analyseData($this->makeBonuslyApiCall($resource = 'analytics', $id = null, $role = 'giver'));

      $receiverData = $this->analyseData($this->makeBonuslyApiCall($resource = 'analytics',$id = null, $role = 'receiver'));

      // foreach ($receiverData as $key => $value) {
      //   array_push($userReceivedTotals, $this->getUserReceipts($value->user->id));
      // }
      //
      // // dd($userReceivedTotals);
      //
      // $sortedTotals = array();
      //
      // $keys = array_keys($userReceivedTotals);

      // dd($keys);
      //
      // foreach ($userReceivedTotals as $key => $row)
      // {
      //     $sortedTotals[$key] = $row['earnings'];
      // }
      // array_multisort($sortedTotals, SORT_DESC, $userReceivedTotals);

      // dd($sortedTotals);

      $users = $this->getUsers();

      $sortedUsers = null;
            foreach ($users as $key => $value) {
              $v = array(&$value);
              print_r( $v);
              // array_push($sortedUsers, usort($v,array($this, "cmp")) );
            }

      // $sortedUsers = usort($users,array($this, "cmp"));
      dd("sorted users $sortedUsers");
      $expiresAt = Carbon::now()->addMinutes(0);

      Cache::put('giverData', $giverData, $expiresAt);
      Cache::put('receiverData', $receiverData, $expiresAt);
      // dd($users); //, $receiverData);
    }
    else {
      $giverData = Cache::get('giverData');
      $receiverData = Cache::get('receiverData');
    }
    return view('leaderboard', compact('giverData', 'receiverData'));
  }

  // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

  // 'url' => 'http://monitor.wiseserve.net',


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
    // dd($content);
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

  function getUsers() {
    return $this->makeBonuslyApiCall($resource = 'users');
  }

  function getUserData($id) {

    $returnData = [];

    return $this->makeBonuslyApiCall($resource = 'users', $id);
      // $returnData['id'] = $user->id;
      // $returnData['name'] = $user->display_name;
      // $returnData['earnings'] = $user->earning_balance;

      // dd($returnData);
    return $returnData;
  }

  function cmp($a, $b)
{
  echo "$a";
    return strcmp($a->earning_balance, $b->earning_balance);
}
}
