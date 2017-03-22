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

    switch ($urlParamKey) {
      case 'book' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;
      case 'clients' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;
      case 'help' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;
      case 'icinga' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;
      case 'support' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;
      case 'users' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;
      case 'repair' :
      if ($request->token == $bonuslyEnvTokens[$urlParamKey]) {
        return $next($request);
      }
      else {
        return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      }
      break;

      default:
      # code...
      return $this->sendEnhancedResponse("token no good!", false, "ephemeral");
      break;
    };
    //
    // if ($request->token == \Config::get('mattermost.book')){
    //     return $next($request);
    //   }
    //   else if ($request->token == \Config::get('mattermost.clients')){
    //     echo $request->token . \Config::get('mattermost.book');
    //     return $next($request);
    //   }
    //   else if ($request->token == \Config::get('mattermost.help')){
    //     echo $request->token . \Config::get('mattermost.book');
    //     return $next($request);
    //   }
    //   else if ($request->token == \Config::get('mattermost.icinga')){
    //     echo $request->token . \Config::get('mattermost.book');
    //     return $next($request);
    //   }
    //   else if ($request->token == \Config::get('mattermost.support')){
    //     echo $request->token . \Config::get('mattermost.book');
    //     return $next($request);
    //   }
    //   else if ($request->token == \Config::get('mattermost.users')){
    //     echo $request->token . \Config::get('mattermost.book');
    //     return $next($request);
    //   }
    //     else {
    //     }
    //   return $next($request);
  }

  function sendEnhancedResponse($message , $successBool, $responseType)	{
    $response = [
      'success'      	=> $successBool,
      'response_type'	=> $responseType,
      'username' 			=> 'Seraph',
      'text'         	=> $message
    ];
    return Response::json($response, ($response['success']) ? 200 : 400);
  }
}

