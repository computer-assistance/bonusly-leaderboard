<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class Bonus extends Model
{
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'bonuses';

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable =   ['bonus_id', 'date_given', 'amount', 'hashtag', 'value', 'reason', 'reason_html', 'amount_with_currency'];

  protected $primaryKey = 'bonus_id';

  public $incrementing = false;

  public $timestamps = false;

  /**
  * The attributes that should be mutated to dates.
  *
  * @var array
  */
  protected $dates = [
    'date_given'
  ];

  /**
  * Get the user who gave this bonus.
  */
  public function giver()
  {
    return $this->hasOne('App\Models\Giver','bonus_id', 'bonus_id');
  }

  /**
  * Get the user who received this bonus.
  */
  public function recipients()
  {
    return $this->belongsToMany('App\Models\Recipient', 'bonus_id', 'recipient_id');
  }

  /**
  * Get the amount
  */
  public function amount()
  {
    return $this->amount;
  }

  /**
  * Get the date
  */
  public function getDateGivenAttribute()
  {
    return $this->date_given;
  }

  /**
  * Set the date
  */
  public function setDateGivenAttribute($date)
  {
    // $this->attributes['date_given'] = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $date);
    $this->attributes['date_given'] = $date;
  }


  /**
  * Restrict bonuses to current month
  */
  public function scopeThisMonth()
  {
    $startOfThisMonth = Carbon::now()->startOfMonth()->startOfDay();
    $endOfThisMonth = Carbon::now()->endOfMonth()->endOfDay();

    return $this->where('date_given', '>=', $startOfThisMonth)->where('date_given', '<=', $endOfThisMonth);
  }


  /**
  * Restrict bonuses to current month
  */
  public function scopeThisWeek()
  {
    $startOfThisWeek = Carbon::now()->startOfWeek()->startOfDay();
    $endOfThisWeek = Carbon::now()->endOfWeek()->endOfDay();

    return $this->where('date_given', '>=', $startOfThisWeek)->where('date_given', '<=', $endOfThisWeek);
  }

  /**
  * Restrict bonuses to current day
  */
  public function scopeToday()
  {
    $startOfToday = Carbon::now()->startOfDay();
    $endOfToday = Carbon::now()->endOfDay();

    return $this->where('date_given', '>=', $startOfToday)->where('date_given', '<=', $endOfToday);
  }

  /**
  * Restrict bonuses to current day
  */
  public function scopeYesterday()
  {
    $startOfYesterday = Carbon::now()->subDay()->startOfDay();
    $endOfYesterday = Carbon::now()->subDay()->endOfDay();

    return $this->where('date_given', '>=', $startOfYesterday)->where('date_given', '<=', $endOfYesterday);
  }







}
