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

class CallbackController extends BaseController
{

    function callback(LaravelFacebookSdk $fb)
	{
	    // Obtain an access token.	    
    	if(Session::get('user_id') == null ) {
		    try {
		        $token = $fb->getAccessTokenFromRedirect();
		    } catch (Facebook\Exceptions\FacebookSDKException $e) {
		        //dd($e->getMessage());
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
		    Session::put('user_token', (string) $token);

		    // Get basic info on the user from Facebook.
		    try {
		        $response = $fb->get('/me?fields=id,name,email');
		    } catch (Facebook\Exceptions\FacebookSDKException $e) {
		        dd($e->getMessage());
		    }

		    $user_id = (string) json_decode($response->getBody(), true)['id'];

		    Session::put('user_id', $user_id );
		    // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
		    $facebook_user = $response->getGraphUser();

		    // Create the user if it does not exist or update the existing entry.
		    // This will only work if you've added the SyncableGraphNodeTrait to your User model.
		    $user = User::createOrUpdateGraphNode($facebook_user);

		    // Log the user into Laravel
		    Auth::login($user);

		    if ( !Schema::hasTable('receive_data_'.$user_id) ) {
			    Schema::create('receive_data_'.$user_id, function (Blueprint $table) {
		            $table->string('parent_id',100);
		            $table->string('oid',100);
		            $table->string('type',100);
		            $table->text('comments')->nullable();
		            $table->integer('Isroot');
		            $table->string('sender_id',100);
		            $table->string('sender_name',100)->nullable();
		            $table->string('receive_id',100);
		            $table->string('receive_name',100)->nullable();
		            $table->string('facebookuser',100);
		            $table->string('post_id',100);
		            $table->string('page',100);
		            $table->string('attackment',500)->nullable();
		            $table->boolean('like');
		            $table->boolean('hidden');
		            $table->boolean('is_read');
		            $table->json('data');
		            $table->timestamps();
		        });
			}
		    //return redirect('/')->with('message', 'Successfully logged in with Facebook');


		}
		$token=Session::get('user_token');
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
	      //var_dump($e->getResponse());
	    } else {
	      $json = json_decode($response->getBody(), true);
	      if ( count($json['accounts']['data']) > 0 ) {
		      for ($i = 0 ; $i < count($json['accounts']['data']); $i++){
		        $fbPage = new FacebookPages;
		        $fbPage->pagesid = $json['accounts']['data'][$i]['id'];
		        $fbPage->pagesname = $json['accounts']['data'][$i]['name'];
		        $fbPage->access_token = $json['accounts']['data'][$i]['access_token'];
		        $fbPage->user_id = $json['id'];		        
		        if ( FacebookPages::where('pagesid', $fbPage->pagesid)->get()->isEmpty() ) {
		        	$fbPage->isactive = false;
		        	db::table('option')->insert(['pageid'=>$fbPage->pagesid,'facebookuser'=>$json['id'],'autohideemailandphone'=>0]);
		        } else {
		        	$fbPage->isactive=FacebookPages::where('pagesid', $fbPage->pagesid)->get()->toArray()[0]['isactive'];
		        }
		        Session::put('Pages'.$json['accounts']['data'][$i]['id'],$json['accounts']['data'][$i]['access_token']);
		        FacebookPages::updateorcreate([
				    'pagesid' =>$fbPage->pagesid
				],[
				    'pagesname'   =>  $fbPage->pagesname,
				    'access_token'     =>  $fbPage->access_token,
				    'user_id' =>  $fbPage->user_id,
				    'isactive'    =>  $fbPage->isactive
				]);
				$return[$i]=$fbPage;
		      }
		  }
	      return view('regis',['pages'=>$return]);
	    }
	  	
	}
}

