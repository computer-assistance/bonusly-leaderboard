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


      $giverData = $this->analyseData($this->makeBonuslyApiCall($resource = 'analytics', $id = null, $role = 'giver'));

      $receiverData = $this->analyseData($this->makeBonuslyApiCall($resource = 'analytics',$id = null, $role = 'receiver'));

      $users = $this->getUsers();
      $giverPointsData = [];

      $receiverPointsData = [];
      $receiverPercentages = [];

      $count = count($users) -1; // Welcome user is not counted as ignored in later code c.line 50

      foreach ($users as $key => $value) {
        if ($value->display_name != 'Welcome') {
          $givenTotal += $this->sumPoints($value, 'giving_balance');
          $receivedTotal += $this->sumPoints($value, 'earning_balance');
        }
      }

      foreach ($users as $key => $value) {
        if($value->display_name != 'Welcome') {
          $giverPointsLoopData['id'] = $value->id;
          $giverPointsLoopData['profile_pic_url'] = $value->profile_pic_url;
          $giverPointsLoopData['display_name'] = ucfirst($value->display_name);
          $giverPointsLoopData['giving_balance'] =  100 - $value->giving_balance;
          $giverPointsLoopData['giver_percentage'] =  (int)(((100 - $value->giving_balance)/$givenTotal) * 100) ;

          array_push($giverPointsData, (object)$giverPointsLoopData);

          // $givenTotal += $giverPointsLoopData['giving_balance'];

          $receiverPointsLoopData['id'] = $value->id;
          $receiverPointsLoopData['profile_pic_url'] = $value->profile_pic_url;
          $receiverPointsLoopData['display_name'] = ucfirst($value->display_name);
          $receiverPointsLoopData['earning_balance'] = $value->earning_balance;
          $receiverPointsLoopData['receiver_percentage'] =  (int)(($value->earning_balance/$receivedTotal) * 100) ;


          array_push($receiverPointsData, (object)$receiverPointsLoopData);

          // $receivedTotal += $receiverPointsLoopData['earning_balance'];
        }
      }

      usort($giverPointsData, function($a, $b)
      {
          return $b->giving_balance - $a->giving_balance;
      });

      usort($receiverPointsData, function($a, $b)
      {
      return $b->earning_balance - $a->earning_balance;
      });

      // dd ($givenTotal, $giverPointsData, $receivedTotal, $receiverPointsData);

      $giverPointsData = array_slice($giverPointsData,0, 10);
      $receiverPointsData = array_slice($receiverPointsData,0, 10);

      $expiresAt = Carbon::now()->addMinutes(10);

      Cache::put('giverPointsData', $giverPointsData, $expiresAt);
      Cache::put('receiverPointsData', $receiverPointsData, $expiresAt);
    }
    else {
      $giverPointsData = Cache::get('giverPointsData');
      $receiverPointsData = Cache::get('receiverPointsData');
    }

    return view('leaderboard', compact('giverPointsData', 'receiverPointsData'));
  }

  // https://bonus.ly/api/v1/analytics/standouts?access_token=e288e7aadf0e48c1d0b3a5b84699e15a

  // 'url' => 'http://monitor.wiseserve.net',

  function sumPoints($data, $prop) {

    $temp = null;

    switch ($prop) {
      case 'giving_balance':

      foreach ($data as $key => $value) {
        if($key == $prop) {
          if ($value > 100) {
            $value = 100;
          }
          if ($value < 0) {
            $value = 0;
          }
          $temp += (100 - $value);
        }
      }
      return $temp;
      break;

      case 'earning_balance':

      foreach ($data as $key => $value) {
        if($key == $prop) {
          if ($value < 0) {
            $value = 0;
          }
          $temp += $value;
        }
      }
      return $temp;
      break;

      default:
      # code...
      break;
    }
  }

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

    $res = $content->result;

    return $res;
  }

  function analyseData($data) {

    if(!is_object($data)) {
      $data = (object)$data;
    }

    $res = [];
    $hi = null;
    $values = [];
    $count = 0;
    $total = 0;
    $percentages = [];
    $size = count($data);

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
      $res2->percentage = $this->getPercent($total, $val->count);
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

//   function analyseUserData($data, $type) {
//
//     switch ($type) {
//       case 'giver':
//       $k ='giving_balance';
//         break;
//
//       case 'receiver':
//       $k ='earning_balance';
//         break;
//
//       default:
//         # code...
//         break;
//     }
//
//     // if(!is_object($data)) {
//     //   $data = (object)$data;
//     // }
//
//     $res = [];
//     $hi = null;
//     $values = [];
//     $count = 0;
//     $count2 = 0;
//     $total = 0;
//     $percentages = [];
//     $size = count($data);
//     var_dump($data);
//     foreach ($data as $type => $v) {
//       $count2++;
//     }
//
//     echo "num arrays = $count2 \n";
//
//     foreach ($data as $d) {
//       foreach ($d as $k => $v) {
//         echo $v . "\n";
//         if ($hi == null || $v > $hi) {
//           $hi = $v;
//         }
//         $total+= $v;
//         $count++;
//       }
//     }
//     die;
//
//     foreach ($data as $d) {
//       $res2 = new \stdClass;
//       $res2 = $d;
//       foreach ($d as $k => $v) {
//         if ($hi == null || $v > $hi) {
//           $hi = $v;
//         }
//       $res2->percentage = $this->getPercent($total, $val->$k);
//       array_push($values, $val->$k);
//       array_push($res, $res2);
//     }
//
//     $res = (object)$res;
//
//     // $res->percentages = $this->getPercentages($total, $values);
//
//     // $res->hi = $hi;
//     // $res->count = $count;
//     // $res->percentages = $values;
//     // $res->total = $total;
//     return (array)$res;
//   }
// }

  function getPercentages($total, $data) {
    $ret = [];
    foreach ($data as $key => $value) {
      foreach ($value as $k => $v) {
        echo $k . '<k | v>' . $v ."|\n";
      }
      // array_push($ret, (int)(($value/$total)*100));
    }
    return $ret;
  }

  function getPercent($numItems, $value) {
    return (int)(($value/$numItems)*100);
  }

  function getUsers() {
    return $this->makeBonuslyApiCall($resource = 'users');
  }

  function getUserData($id) {
    return $this->makeBonuslyApiCall($resource = 'users', $id);
  }

}
