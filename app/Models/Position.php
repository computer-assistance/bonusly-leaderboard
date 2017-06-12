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

  protected $fillable =   ['user_id', 'username', 'type', 'old_position'];

  protected $primaryKey = 'user_id';

  public $incrementing = false;

  function getPreviousGiverPos($user_id) {
    return $this->find($user_id)->where('type', '=', 'giver')->pluck('old_position');
  }

  function getPreviousReceiverPos($user_id) {
    return $this->find($user_id)->where('type', '=', 'receiver')->pluck('old_position');
  }

  function givers() {
    return self::where('type', 'giver');
  }
}
