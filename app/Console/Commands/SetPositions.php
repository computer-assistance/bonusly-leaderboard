<?php

namespace App\Console\Commands;



use Illuminate\Console\Command;
use App\Models\Position;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class SetPositions extends Command
{
    /**
     * A friendly name of the command that will be used when loggin messages.
     *
     * @var string
     */
    protected $command_title = 'Set giver and Receiver Positions';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sets positions';

    /**
    * Create a new command instance.
    *
    * @return void
    */

    protected $obj1;
    protected $obj2;
    protected $giverPositions = null;
    protected $receiverPositions = null;


    public function receiveUrl() {
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

    public function giveUrl() {
      return    'https://bonus.ly/api/v1/users?show_financial_data=true';
    }

    public function getMonth() {
      return date("F", mktime(0, 0, 0, Carbon::now()->month, 1));
    }

    public function getDayNumber() {
      return Carbon::now()->day;
    }

    public function makeBonuslyApiCall($url) {
      $results = new \stdClass;
      $browser = new \Buzz\Browser();

      $access_token = config('bonusly.token');

      $apiUrl = $url . '&access_token=' . $access_token;

      $hostResponse = $browser->get($apiUrl, []);

      $content = json_decode($hostResponse->getContent());

      $results = $content->result;

      return $results;
    }


    public function __construct()
    {
      parent::__construct();
      $this->obj1 = new \stdClass();
      $this->obj2 = new \stdClass();
      $this->obj3 = new \stdClass();
      $this->obj4 = new \stdClass();
      $this->obj5 = new \stdClass();
      $this->obj6 = new \stdClass();
      $this->obj7 = new \stdClass();
      $this->obj8 = new \stdClass();
      // $this->obj1->id = "58ac031d7b8d8601a3c373f3";//, "type" : "giver", "class" : "fa fa-arrows-h", "old_position" : 1, "new_position" : 1, "points" : 24);
    }

    // protected $obj2 = new stdClass();
    // user def vars

    protected $posArray1 =
      array (
        array ("user_id" => "5846d6b0387f8a036bc9351c", "username" => "bob", "type" => "giver", "class" => "fa fa-arrows-h", "old_position" => 2, "new_position" => 2, "points" => 2),
        array ("user_id" => "58ac031d7b8d8601a3c373f3", "username" => "fred", "type" => "receiver", "class" => "fa fa-arrows-h", "old_position" => 1, "new_position" => 1, "points" => 5),
        array ("user_id" => "58b8134f92d700723871d765", "username" => "farzan", "type" => "giver", "class" => "fa fa-arrow-up", "old_position" => 3, "new_position" => 4, "points" => 4),
        array ("user_id" => "5846d6b0387f8a036bc9351c", "username" => "joolee", "type" => "receiver", "class" => "fa fa-arrows-h", "old_position" => 2, "new_position" => 2, "points" => 6),
        array ("user_id" => "58ac031f7b8d8602dbc373f3", "username" => "jamie", "type" => "giver", "class" => "fa fa-arrow-down", "old_position" => 4, "new_position" => 3, "points" => 14),
        array ("user_id" => "58ac031f7b8d8602dbc373f3", "username" => "moonba", "type" => "receiver", "class" => "fa fa-arrow-down", "old_position" => 4, "new_position" => 3, "points" => 11),
        array ("user_id" => "58ac031e7b8d86024ec373f3", "username" => "teeko", "type" => "giver", "class" => "fa fa-arrows-h", "old_position" => 5, "new_position" => 5, "points" => 23),
        array ("user_id" => "58b8134f92d700723871d765", "username" => "hinty", "type" => "receiver", "class" => "fa fa-arrow-up", "old_position" => 3, "new_position" => 4, "points" => 32),
      );


    public function arrayPush($arrayIn, $dataIn) {
      return array_push($arrayIn, $dataIn);
    }


    protected $objArray = array();

    // protected $obj1 = ("id"->"58ac031e7b8d86024ec373f3", "type"->"giver", "class"->"fa fa-arrows-h", "old_position"->5, "new_position"->5, "points"->23);
    public function assignSingleValToObject($obj, $prop, $val) {
      // echo  $prop . $val;
      // $obj = (object) array_merge( (array)$obj, array( $prop => $val ) );
      $obj = array($obj);
      $obj[$prop] = $val;
      // dump($obj);

      array_shift($obj);
      $obj = (object)$obj;

      return $obj;
    }

    public function assignMultipleValuesToObject($obj, $propVals) {
      // echo  $prop . $val;
      // $obj = (object) array_merge( (array)$obj, array( $prop => $val ) );
      $obj = array($obj);
      foreach ($propVals as $key => $value) {

        $obj[$key] = $value;

      }
      array_shift($obj); // remove original object
      $obj = (object)$obj;
      return $obj;
    }

    function compareByName($a, $b) {
      return strcmp($a->name, $b->name);
    }

    function compareById($a, $b) {
      return ($a->id - $b->id);
    }

    function compareByCar($a, $b) {
      return strcmp($a->car, $b->car);
    }

    function setPositions2($data, $type) {
      // dd($data);
      // $c = 0;
      foreach ($data as $key => $d) {
        // $c++;

          if ($d->old_position > $key + 1 && $d->old_position !=0) {
            $d->class = 'lower fa fa-arrow-down';
            $d->old_position = $key + 1;
          }
          if ($d->old_position < $key + 1 && $d->old_position !=0) {
            $d->class = 'higher fa fa-arrow-up';
            $d->old_position = $key + 1;
          }
          if ($d->old_position == $key + 1) {
            $d->class = 'no_move fa fa-arrows-h';
          }
          array_shift($data);
          $data[] = $d;
        }
      return $data;
    }

    function setPositions3($data, $type) {

      foreach ($data as $key => $d) {
        $pos = Position::where('user_id', '=', $d->id)
        ->where('type', '=', $type)
        ->first();

        if($pos) {
          if (($type == 'giver' && $pos->given_points != 100 - $d->giving_balance) || ($type == 'receiver' && $pos->received_points != $d->received_this_month)) {
            $pos = $this->checkForPositionChanges($pos, $key + 1);
            $pos->save();
          }
        }
        else {
          $pos = new Position;
          $pos->user_id = $d->id;
          $pos->type = $type;
          $pos->username = $d->display_name;
          $pos->old_position = $key + 1;
          $pos->class = 'init fa fa-sun-o';
          if($type == 'giver') {
            $pos->given_points = 100 - $d->giving_balance;
          }
          if($type == 'receiver') {
            $pos->received_points = $d->received_this_month;
          }
          $pos->save();
        }
      }
    }

    function checkForPositionChanges($pos, $new_position) {
      if ($pos->old_position == $new_position) {
        echo 'hit == ' . "\n";
        $pos->class = 'no_move fa fa-arrows-h';
      }
      if ($new_position > $pos->old_position) {
        echo 'hit > ' . "\n";
        $pos->class = 'lower fa fa-arrow-down';
        $pos->old_position = $new_position;
      }
      if ($new_position < $pos->old_position) {
        echo 'hit < ' . "\n";
        $pos->class = 'higher fa fa-arrow-up';
        $pos->old_position = $new_position;
      }
      return $pos;
    }

    function hotwire(){
      $name = $this->anticipate('user name?', ['hristo', 'denis', 'cristian']);
      $idx = $this->ask('New position?');
      $type = $this->anticipate('receiver or giver?', ['receiver','giver']);
      $id = null;

      switch ($name) {
        case 'hristo':
        $id = '5846d6b0387f8a036bc9351c';
        break;
        case 'denis':
        $id = '58ac031e7b8d860264c373f3';
        break;
        case 'cristian':
        $id = '58ac031e7b8d86024ec373f3';
        break;

        default:
          # code...
          break;
      }

      $pos = Position::where('user_id', '=', $id)
      ->where('type', '=', $type)
      ->first();
      //  dump($pos);

      if($pos) {
        $pos = $this->checkForPositionChanges($pos, $idx);
        // $pos->username = "fote";
        // $pos->old_position = 9;
      }
      dump($pos);
      $pos->save();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->hotwire();
    }
}








