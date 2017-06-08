<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class BonuslyToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */

  protected $re = '/\/\w*\?/';


  public function handle($request, Closure $next) {

    if(!$request->token) {
      die('no token sent!');
    }
    else {
      $bonuslyEnvTokens = config('bonusly');
      $urlParamKey = substr(strrchr($request->path(), "/"),1);
    }
  }
}

