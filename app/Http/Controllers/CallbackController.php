<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Support\Facades\Session;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\FacebookPages;

class CallbackController extends BaseController
{

    function callback(LaravelFacebookSdk $fb)
	{
	    // Obtain an access token.
	    try {
	        $token = $fb->getAccessTokenFromRedirect();
	    } catch (Facebook\Exceptions\FacebookSDKException $e) {
	        dd($e->getMessage());
	    }

	    // Access token will be null if the user denied the request
	    // or if someone just hit this URL outside of the OAuth flow.
	    if (! $token) {
	        // Get the redirect helper
	        $helper = $fb->getRedirectLoginHelper();

	        if (! $helper->getError()) {
	            abort(403, 'Unauthorized action.');
	        }

	        // User denied the request
	        dd(
	            $helper->getError(),
	            $helper->getErrorCode(),
	            $helper->getErrorReason(),
	            $helper->getErrorDescription()
	        );
	    }

	    if (! $token->isLongLived()) {
	        // OAuth 2.0 client handler
	        $oauth_client = $fb->getOAuth2Client();

	        // Extend the access token.
	        try {
	            $token = $oauth_client->getLongLivedAccessToken($token);
	        } catch (Facebook\Exceptions\FacebookSDKException $e) {
	            dd($e->getMessage());
	        }
	    }

	    $fb->setDefaultAccessToken($token);

	    // Save for later
	    Session::put('fb_user_access_token', (string) $token);

	    // Get basic info on the user from Facebook.
	    try {
	        $response = $fb->get('/me?fields=id,name,email');
	    } catch (Facebook\Exceptions\FacebookSDKException $e) {
	        dd($e->getMessage());
	    }

	    // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
	    $facebook_user = $response->getGraphUser();

	    // Create the user if it does not exist or update the existing entry.
	    // This will only work if you've added the SyncableGraphNodeTrait to your User model.
	    $user = User::createOrUpdateGraphNode($facebook_user);

	    // Log the user into Laravel
	    Auth::login($user);

	    //return redirect('/')->with('message', 'Successfully logged in with Facebook');

		echo '<h1>Danh sách Fanpages</h1>' . "\n\n";

		$requeststring='/me?fields=accounts';
		try{
			$response = $fb->get($requeststring,$token);
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
		    // When Graph returns an error
		    echo 'Graph returned an error: ' . $e->getMessage();
	    	exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		   // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}


	    if ($response->isError()) {
	      $e = $response->getThrownException();
	      echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
	      echo '<p>Graph Said: ' . "\n\n";
	      var_dump($e->getResponse());
	    } else {
	      $json = json_decode($response->getBody(), true);
	      for ($i = 0 ; $i < count($json['accounts']['data']); $i++){		   
	      	echo "<a href='/laravel/public/regis?key=" . $json['accounts']['data'][$i]['id'] . "'>" . $json['accounts']['data'][$i]['name'] . " </a> </br>";
	        echo "</br>";

	        $fbPage = new FacebookPages;
	        $fbPage->pagesid = $json['accounts']['data'][$i]['id'];
	        $fbPage->access_token = $json['accounts']['data'][$i]['access_token'];


		    Session::put('user_id', (string) $json['id']);

	        if ( FacebookPages::where('pagesid', $fbPage->pagesid)->get()->isEmpty() ) {
	        	$fbPage->save();
	        } else {
	        	$fbPage->update();
	        }
	      }
	    }
	  	
	}
}

