<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'users';

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = ['user_id', 'username', 'email'];

  protected $primaryKey = 'user_id';

  public $incrementing = false;

  /**
   * Get the bonus
   */
  public function giver()
  {
      return $this->hasOne('App\Models\Bonus', 'id', 'giver_id');
  }

}
