<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class position extends Model
{
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'position_changes';

  protected $fillable =   ['user_id', 'old_position', 'new_position'];
  //$timestamps = false;
  protected $primaryKey = 'user_id';

  public $incrementing = false;

  
}
