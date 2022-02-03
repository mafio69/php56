<?php

namespace Idea\Gap;

use Auth;

trait UserInfo
{
  public static function bootUserInfo()
    {
	  static::creating(function($model)
	        {
	        if(Auth::user())
	          $model->user_id = Auth::user()->id;
	        });
    }
}
