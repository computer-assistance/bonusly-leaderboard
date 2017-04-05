<?php

namespace App\Http\Controllers;

use Cache;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\BonuslyHelper;

class BonuslyLeaderboardController extends Controller
{

  public function __construct()
  {
    //Load helper functions
    $this->bonusHelper = new BonuslyHelper();
  }

  public function showBoard() {

    if (!Cache::has('giverPointsData') && !Cache::has('receiverPointsData')) {

      $givenTotal = 0;
      $receivedTotal = 0;

      $giverPointsData = [];
      $receiverPointsData = [];
      $highestGiverPoints = 0;
      $highestReceiverPoints = 0;

      $thisMonth = $this->bonusHelper->getMonth();
      $thisDay = $this->bonusHelper->getDayNumber();

      $users = $this->bonusHelper->removeWelcomeUser($this->bonusHelper->makeBonuslyApiCall($this->bonusHelper->giveUrl()));

      $bonuses = $this->bonusHelper->makeBonuslyApiCall($this->bonusHelper->receiveUrl());

      $highestGiverPoints = $this->bonusHelper->getHighestGiverPoints($users);
      $highestReceiverPoints = $this->bonusHelper->getHighestReceiverPoints($users);
      $givenTotal = $this->bonusHelper->getGivenTotal($users);
      $receivedTotal = $this->bonusHelper->getReceivedTotal($users);

      $giverPointsData = $users;

      $receiverPointsData = $this->bonusHelper->makeMonthlyBonusData($users, $bonuses);

      usort($giverPointsData, function($a, $b)
      {
      return $a->giving_balance - $b->giving_balance;
      });

      usort($receiverPointsData, function($a, $b)
      {
      return $b->received_this_month - $a->received_this_month;
      });

      $giverPointsData = array_slice($giverPointsData,0, 10); // limit to top ten
      $receiverPointsData = array_slice($receiverPointsData,0, 10);
      $divisor = 0;
      $divisor = $this->bonusHelper->getTheHighest($highestReceiverPoints, $highestGiverPoints);
      // dd($giverPointsData, $receiverPointsData);

      $expiresAt = Carbon::now()->addMinutes(10);

      Cache::put('giverPointsData', $giverPointsData, $expiresAt);
      Cache::put('receiverPointsData', $receiverPointsData, $expiresAt);
      Cache::put('givenTotal', $givenTotal, $expiresAt);
      Cache::put('receivedTotal', $receivedTotal, $expiresAt);
      Cache::put('divisor', $divisor, $expiresAt);
      Cache::put('thisMonth', $thisMonth, $expiresAt);
      Cache::put('$thisDay', $thisDay, $expiresAt);
    }
    else {
      $receiverPointsData = Cache::get('receiverPointsData');
      $giverPointsData = Cache::get('giverPointsData');
      $receivedTotal = Cache::get('receivedTotal');
      $givenTotal = Cache::get('givenTotal');
      $thisMonth = Cache::get('thisMonth');
      $divisor = Cache::get('divisor');
      $thisDay = Cache::get('thisDay');

    }
    return view('leaderboard', compact('giverPointsData', 'receiverPointsData', 'givenTotal', 'receivedTotal', 'divisor', 'thisMonth', 'thisDay'));
  }
}
