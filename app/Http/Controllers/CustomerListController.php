<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Support\Facades\DB;

class CustomerListController extends BaseController
{

    function get() {
    	$province = db::table('province')->select('provinceid as id','name as name')->get()->toArray();        
        $district = db::table('district')->select('districtid as id','name as name')->get()->toArray();

        return view('CustomerList',['province' => $province,'district' => $district]);
	}
}
