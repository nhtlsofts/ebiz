<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Http\Request;
use App\ReceiveData;
use App\FacebookPages;
use \ZMQContext;
use \ZMQ;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use App\Services\Pusher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TakeController extends BaseController
{
    function Take(LaravelFacebookSdk $fb,Request $request) {

		$access_token = env("FACEBOOK_APP_ID")."|".env("FACEBOOK_APP_SECRET");
		$verify_token = env("VERIFY_TOKEN");
		$hub_verify_token = null;
		 
		if(isset($_REQUEST['hub_challenge'])) {
		    $challenge = $_REQUEST['hub_challenge'];
		    $hub_verify_token = $_REQUEST['hub_verify_token'];
		}
		 
		 
		if ($hub_verify_token === $verify_token) {
		    echo $challenge;
		}

		/////////////////////////

		$receive1 = $request->input('entry.0.messaging');
		$receive2 = $request->input('entry.0.changes');
		$receive3 = $request->input('entry.0.messaging.0.message.is_echo');

		if(isset($receive1)){
			if( $request->input('entry.0.id') != $request->input('entry.0.messaging.0.sender.id')){
				$pagekey=FacebookPages::where('pagesid',$request->input('entry.0.messaging.0.recipient.id'))->first()->access_token;
				$fb->setDefaultAccessToken($pagekey);
				$requeststring='/m_'.$request->input('entry.0.messaging.0.message.mid').'?fields=from';				
				$response1 = $fb->get($requeststring,$pagekey);				
				$json = json_decode($response1->getBody(), true)['from']['id'];
			}
			else {
				$pagekey=FacebookPages::where('pagesid',$request->input('entry.0.messaging.0.sender.id'))->first()->access_token;
				$fb->setDefaultAccessToken($pagekey);				
				$requeststring='/m_'.$request->input('entry.0.messaging.0.message.mid').'?fields=to';
				$response1 = $fb->get($requeststring,$pagekey);				
				$json = json_decode($response1->getBody(), true)['to']['data'][0]['id'];
			}

			/*
			$data = new ReceiveData;
			$data->type = 'message';
			$data->user_id = '';
			$data->comments = $request->input('entry.0.messaging.0.message.text');
			$data->sender_name = $json['first_name'].$json['last_name'];
			$data->attackment = $request->input('entry.0.messaging.0.message.attachments.0.url');
			$data->post_id = 'm_'.$request->input('entry.0.messaging.0.sender.id');			
			$data->page =  $request->input('entry.0.messaging.0.recipient.id');
			$data->data = $request->input('entry.0.messaging');
			if( $request->input('entry.0.id') != $request->input('entry.0.messaging.0.sender.id')){
				if ( ReceiveData::where('sender_id', $request->input('entry.0.messaging.0.sender.id'))->get()->isEmpty() ) {
		        	$data->Isroot = 1 ;
					$data->oid = 'm_'.$request->input('entry.0.messaging.0.sender.id') ;
		        } else {
		        	$data->Isroot = 2 ;
					$data->oid = 'm_'.$request->input('entry.0.messaging.0.message.mid');
		        }
				$data->receive_id = $request->input('entry.0.messaging.0.sender.id');
				$data->receive_name = $json['first_name'].$json['last_name'];				
		    	$data->parent_id = 'm_'.$request->input('entry.0.messaging.0.sender.id') ;
				$data->sender_id = $request->input('entry.0.messaging.0.sender.id');
			}
			else{
				if ( ReceiveData::where('sender_id', $request->input('entry.0.messaging.0.recipient.id'))->get()->isEmpty() ) {
		        	$data->Isroot = 1 ;
					$data->oid = 'm_'.$request->input('entry.0.messaging.0.sender.id') ;
		        } else {
		        	$data->Isroot = 2 ;
					$data->oid = 'm_'.$request->input('entry.0.messaging.0.message.mid');
		        }
				$data->receive_id = $request->input('entry.0.messaging.0.recipient.id');
				$data->receive_name = 'Me';
		    	$data->parent_id = 'm_'.$request->input('entry.0.messaging.0.recipient.id') ;
				$data->sender_id = $request->input('entry.0.messaging.0.recipient.id');
			}
			$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.messaging.0.timestamp')/1000);
			$data->save();*/



			if ( ! ReceiveData::where([['sender_id',$json],['receive_id',$request->input('entry.0.id')],['isRoot',1]])->orwhere([['sender_id',$request->input('entry.0.id')],['receive_id',$json],['isRoot',1]])->get()->isEmpty()){

				$data = new ReceiveData;
				$sender_db = ReceiveData::where([['sender_id',$json],['receive_id',$request->input('entry.0.id')],['isRoot',1]])->orwhere([['sender_id',$request->input('entry.0.id')],['receive_id',$json],['isRoot',1]])->get()->first();	
				$data->type = 'message';
				$data->user_id = '';
				$data->comments = $request->input('entry.0.messaging.0.message.text');
				$data->attackment = $request->input('entry.0.messaging.0.message.attachments.0.url');
				$data->post_id = $sender_db['oid'];		
				$data->page =  $request->input('entry.0.messaging.0.recipient.id');
				$data->data = $request->input('entry.0.messaging');
			    $data->Isroot = 2 ;
				$data->oid = 'm_'.$request->input('entry.0.messaging.0.message.mid');
		    	$data->parent_id = $sender_db['oid'];
				if( $request->input('entry.0.id') == $request->input('entry.0.messaging.0.sender.id')){
					$data->receive_name = $sender_db['receive_name'];									
					$data->receive_id = $sender_db['receive_id'];
					$data->sender_name = 'Me';
					$data->sender_id = $sender_db['sender_id'];
				}
				else{					
					$data->receive_name = 'Me';								
					$data->receive_id = $sender_db['sender_id'];
					$data->sender_name = $sender_db['receive_name'];
					$data->sender_id = $sender_db['receive_id'];	
				}
				$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.messaging.0.timestamp')/1000);
				$data->save();return 1;
			}
			else{
				$a=1;
				$b=$a[0]['1']->abc;
				return $b;
			}

		}
		else if (isset($receive2)){
			if ( $request->input('entry.0.changes.0.field')  == 'conversations'){
				$data = new ReceiveData;
				if ( ReceiveData::where([['oid', str_replace('.$','_',$request->input('entry.0.changes.0.value.thread_id'))],['isRoot',1]])->get()->isEmpty() ) {										
	        		$data->Isroot = 1 ;
					$data->parent_id = str_replace('.$','_',$request->input('entry.0.changes.0.value.thread_id'));
					$data->oid = str_replace('.$','_',$request->input('entry.0.changes.0.value.thread_id'));				
					$data->type = 'message';
					$data->comments = '';
					$data->attackment = '';
					$data->user_id = '';
					$data->post_id = str_replace('.$','_',$request->input('entry.0.changes.0.value.thread_id'));
					$data->page =  $request->input('entry.0.id');
					$data->data = $request->input('entry.0.changes');
					$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.time'));
					////// lấy sender
					$pagekey=FacebookPages::where('pagesid',$request->input('entry.0.id'))->first()->access_token;
					$fb->setDefaultAccessToken($pagekey);				
					$requeststring='/'.$request->input('entry.0.changes.0.value.thread_id').'?fields=senders';
					$response2 = $fb->get($requeststring,$pagekey);
					$json2 = json_decode($response2->getBody(), true);
					///////////////////
					$data->sender_name = $json2["senders"]['data'][0]['name'];					
					$data->sender_id = $json2["senders"]['data'][0]['id'];	
					$data->receive_id = $json2["senders"]['data'][1]['id'];
					$data->receive_name = $json2["senders"]['data'][1]['name'];
					$data->save();
				}
			}
			else if ( $request->input('entry.0.changes.0.value.item')  == 'comment')
			{
				$data = new ReceiveData;

				if ( ReceiveData::where([['oid', $request->input('entry.0.changes.0.value.parent_id')],['isRoot',1]])->get()->isEmpty() ) {
					if ($request->input('entry.0.changes.0.value.parent_id')==$request->input('entry.0.changes.0.value.post_id'))
					{						
		        		$data->Isroot = 1 ;
						$data->parent_id = $request->input('entry.0.changes.0.value.comment_id');
						$data->oid = $request->input('entry.0.changes.0.value.comment_id');
					}
					else
					{						
		        		$data->Isroot = 1 ;
						$data->parent_id = $request->input('entry.0.changes.0.value.parent_id');
						$data->oid = $request->input('entry.0.changes.0.value.parent_id');
					}
		        } else {
		        	$data->Isroot = 2 ;		        	
					$data->parent_id = $request->input('entry.0.changes.0.value.parent_id');
					$data->oid = $request->input('entry.0.changes.0.value.comment_id');
		        }
				$data->type = 'comment';
				$data->comments = $request->input('entry.0.changes.0.value.message');
				$data->attackment = $request->input('entry.0.changes.0.value.attachments.0.url');
				$data->sender_id = $request->input('entry.0.changes.0.value.sender_id');
				$data->sender_name = $request->input('entry.0.changes.0.value.sender_name');
				$data->user_id = '';
				$data->post_id = $request->input('entry.0.changes.0.value.post_id');	
				$data->page =  $request->input('entry.0.id');
				$data->data = $request->input('entry.0.changes');
				$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.changes.0.value.created_time'));
				$data->receive_id = $request->input('entry.0.id');
				if( $request->input('entry.0.id') != $request->input('entry.0.changes.0.value.sender_id')){
					$data->receive_name = 'You';
				}
				else{
					$data->receive_name = 'Me';
				}
				$data->save();
			}
			else if ($request->input('entry.0.changes.0.value.item')  == 'post'){
				$data = new ReceiveData;
				$data->Isroot = 0 ;
				$data->parent_id = '';
				$data->type = 'post';
				$data->oid = $request->input('entry.0.changes.0.value.post_id');
				$data->comments = $request->input('entry.0.changes.0.value.message');
				$data->sender_id = $request->input('entry.0.changes.0.value.sender_id');
				$data->sender_name = $request->input('entry.0.changes.0.value.sender_name');
				$data->user_id = '';
				$data->post_id = $request->input('entry.0.changes.0.value.post_id');	
				$data->page =  $request->input('entry.0.id');
				$data->data = $request->input('entry.0.changes');
				$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.changes.0.value.created_time'));
				$data->receive_id= $request->input('entry.0.id');
				$data->receive_name = '';
				$data->save();
			}
		}

		/////////////////////////

		$context = new ZMQContext();
	    $socket = $context->getSocket(ZMQ::SOCKET_PUSH);
	    $socket->connect("tcp://127.0.0.1:5555", 'my pusher');
	    if (isset($data)){
	    	$socket->send(json_encode($data));
		}
	}

	function onChat(LaravelFacebookSdk $fb)
    {

        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];
        $chatdata=$_GET["chatdata"];

	  	$requeststring = $fb->request(
	    'POST',
	    '/'.$oid.'/comments',
	    array(
	      'message' => $chatdata,
	    ));
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
    }
    function onLike(LaravelFacebookSdk $fb)
    {

        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];

	  	$requeststring = $fb->request(
	    'POST',
	    '/'.$oid.'/likes'
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
    }
    function onDelete(LaravelFacebookSdk $fb)
    {

        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];

	  	$requeststring = $fb->request(
	    'DELETE',
	    '/'.$oid
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
    }
    function onPicture(LaravelFacebookSdk $fb)
    {
        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];
        $path=$_GET["path"];
	  	$requeststring = $fb->request(
	    'POST',
	    '/'.$oid.'/comments',
	    array(
        'source' => new \CURLFile($path, 'image/png'),
        'message' => 'User provided message')
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
    }
}
