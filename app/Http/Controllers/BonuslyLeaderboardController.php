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

      $divisor = 0;
      $givenTotal = 0;
      $receivedTotal = 0;
      $highestGiverPoints = 0;
      $highestReceiverPoints = 0;

      $giverPointsData = [];
      $receiverPointsData = [];


      $thisMonth = $this->bonusHelper->getMonth();
      $thisDay = $this->bonusHelper->getDayNumber();


      $users = $this->bonusHelper->removeWelcomeUser($this->bonusHelper->makeBonuslyApiCall($this->bonusHelper->giveUrl()));
      $bonuses = $this->bonusHelper->makeBonuslyApiCall($this->bonusHelper->receiveUrl());


      $giverPointsData = $users;
      $receiverPointsData = $this->bonusHelper->makeMonthlyBonusData($users, $bonuses);


      $givenTotal = $this->bonusHelper->getTotal($users, 'giving_balance');
      $receivedTotal = $this->bonusHelper->getTotal($receiverPointsData, 'received_this_month');


      $highestGiverPoints = $this->bonusHelper->getHighestPoints($giverPointsData, 'giving_balance');
      $highestReceiverPoints = $this->bonusHelper->getHighestPoints($giverPointsData, 'received_this_month');

      $divisor = $this->bonusHelper->getTheHighest($highestReceiverPoints, $highestGiverPoints);

      $widthFactor = $this->bonusHelper->makeWidthFactor($highestReceiverPoints, $highestGiverPoints);


      usort($giverPointsData, function($a, $b)
      {
      return $a->giving_balance - $b->giving_balance;
      });

      usort($receiverPointsData, function($a, $b)
      {
      return $b->received_this_month - $a->received_this_month;
      });


      $this->bonusHelper->setPositions($giverPointsData, 'giver');
      $this->bonusHelper->setPositions($receiverPointsData, 'receiver');


      $giverPointsData = array_slice($giverPointsData,0, 10); // limit to top ten
      $receiverPointsData = array_slice($receiverPointsData,0, 10);

      // dd($giverPointsData, $receiverPointsData, $givenTotal, $receivedTotal, $highestGiverPoints, $highestReceiverPoints, $divisor); 

      $expiresAt = Carbon::now()->addMinutes(0);

      Cache::put('receiverPointsData', $receiverPointsData, $expiresAt);
      Cache::put('giverPointsData', $giverPointsData, $expiresAt);
      Cache::put('receivedTotal', $receivedTotal, $expiresAt);
      Cache::put('widthFactor', $widthFactor, $expiresAt);
      Cache::put('givenTotal', $givenTotal, $expiresAt);
      Cache::put('thisMonth', $thisMonth, $expiresAt);
      Cache::put('divisor', $divisor, $expiresAt);
      Cache::put('thisDay', $thisDay, $expiresAt);
    }
    else {
      $receiverPointsData = Cache::get('receiverPointsData');
      $giverPointsData = Cache::get('giverPointsData');
      $receivedTotal = Cache::get('receivedTotal');
      $widthFactor = Cache::get('widthFactor');
      $givenTotal = Cache::get('givenTotal');
      $thisMonth = Cache::get('thisMonth');
      $divisor = Cache::get('divisor');
      $thisDay = Cache::get('thisDay');
    }
    return view('leaderboard', compact('giverPointsData', 'receiverPointsData', 'givenTotal', 'receivedTotal', 'divisor', 'thisMonth', 'thisDay', 'widthFactor'));
  }
}
