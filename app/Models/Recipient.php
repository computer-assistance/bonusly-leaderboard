<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'recipients';

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable =   ['bonus_id', 'recipient_id'];

  protected $primaryKey = 'bonus_id';

  public $incrementing = false;

  public $timestamps = false;

  /**
   * Get the amount
   */
  public function bonus()
  {
      return $this->hasOne('App\Models\Bonus', 'id', 'recipient_id');
  }

}
