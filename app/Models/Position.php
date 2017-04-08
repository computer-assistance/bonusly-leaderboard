<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'positions';

  protected $fillable =   ['user_id', 'type', 'class', 'old_position', 'new_position'];
  //$timestamps = false;
  // protected $primaryKey = ['user_id', 'type'];

  function getPreviousGiverPos($user_id) {
    return $this->find($user_id)->where('type', '=', 'giver')->pluck('old_position');
  }

  function getPreviousReceiverPos($user_id) {
    return $this->find($user_id)->where('type', '=', 'receiver')->pluck('old_position');
  }

  function getCurrentGiverPos($user_id) {
    return $this->find($user_id)->where('type', '=', 'giver')->pluck('new_position');
  }

  function getCurrentReceiverPos($user_id) {
    return $this->find($user_id)->where('type', '=', 'receiver')->pluck('new_position');
  }

  public static function getCurrentGiverClass($user_id) {
    // dd($user_id);
    return self::where('user_id', $user_id)->where('type', '=', 'giver')->value('class');
  }

  public static function getCurrentReceiverClass($user_id) {
    return self::where('user_id', $user_id)->where('type', '=', 'receiver')->value('class');
  }

}
