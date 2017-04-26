<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Giver extends Model
{
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'givers';

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = ['bonus_id', 'giver_id'];

  protected $primaryKey = 'bonus_id';

  public $incrementing = false;

  public $timestamps = false;

  /**
   * Get the bonus
   */
  public function bonuses()
  {
      return $this->hasMany('App\Models\Bonus', 'bonus_id', 'bonus_id');
  }

}
