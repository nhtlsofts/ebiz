<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Support\Facades\Session;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\FacebookPages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class LogOutController extends BaseController
{

    function logout(LaravelFacebookSdk $fb){
    	Session::flush();
    	return view('chuyentrang');
    }
}

