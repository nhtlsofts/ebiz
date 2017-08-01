<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class ReceiptListController extends BaseController
{

    function get() {
	    return view('ReceiptList');
	}
}
