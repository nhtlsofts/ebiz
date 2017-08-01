<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacebookPages extends Model
{
    //
    protected $fillable = ['pagesid','pagesname','access_token','user_id','isactive'];

    protected $primaryKey = 'pagesid';
}


