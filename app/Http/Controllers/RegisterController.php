<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use App\FacebookPages;
use Illuminate\Support\Facades\Session;

class RegisterController extends BaseController
{

    function Register(LaravelFacebookSdk $fb,Request $request) {

	    $pageid=$request->input("key");
	  	$pagekey=FacebookPages::where('pagesid',$pageid)->first()->access_token;

	  	Session::put('page_id',$pageid);
	  	Session::put('page_key',$pagekey);

		$fb->setDefaultAccessToken(env("FACEBOOK_APP_ID")."|".env("FACEBOOK_APP_SECRET"));
	  	$requeststring = $fb->request(
	    'POST',
	    '/'.$pageid.'/subscriptions',
	    array(
	      'object' => 'page',
	      'verify_token' => 'sad',
	      'callback_url' => 'https://75d24456.ngrok.io/laravel/public/take',
	      'fields' => 'feed,conversations'
	    )
	  );

	  try {
	    $response = $fb->getClient()->sendRequest($requeststring);
	  } catch(Facebook\Exceptions\FacebookResponseException $e) {
	    // When Graph returns an error
	    echo 'Graph returned an error: ' . $e->getMessage();
	    exit;
	  } catch(Facebook\Exceptions\FacebookSDKException $e) {
	    // When validation fails or other local issues
	    echo 'Facebook SDK returned an error: ' . $e->getMessage();
	    exit;
	  }
	  $graphNode = $response->getGraphNode();

	  //dk page
	  // page token
	  $fb->setDefaultAccessToken($pagekey);
	  $requeststring = $fb->request(
	    'POST',
	    '/'.$pageid.'/subscribed_apps'
	  );

	  try {
	    $response = $fb->getClient()->sendRequest($requeststring);
	  } catch(Facebook\Exceptions\FacebookResponseException $e) {
	    // When Graph returns an error
	    echo 'Graph returned an error: ' . $e->getMessage();
	    exit;
	  } catch(Facebook\Exceptions\FacebookSDKException $e) {
	    // When validation fails or other local issues
	    echo 'Facebook SDK returned an error: ' . $e->getMessage();
	    exit;
	  }
	  $graphNode = $response->getGraphNode();
	  return view('chuyentrang');
	}
}
