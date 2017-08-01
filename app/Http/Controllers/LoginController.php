<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class LoginController extends BaseController
{

    function login(LaravelFacebookSdk $fb) {

	    $permissions = ['user_likes, user_friends, user_posts, email, manage_pages, publish_pages ,pages_messaging, public_profile','read_page_mailboxes',];
    	//$permissions = ['public_profile,pages_show_list,email,']; 
	    // Optional permissions
	    $login_link = $fb
	            ->getRedirectLoginHelper()
	            ->getLoginUrl('http://'.env('LINK').'/laravel/public/facebook/callback', $permissions);

	   	return view('login',['link' => $login_link]);

	}
}
