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
			if ( ReceiveData::where('oid','m_'.str_replace('.$','_',$request->input('entry.0.messaging.0.message.mid')))->get()->isEmpty()){
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

				if ( ! ReceiveData::where([['sender_id',$json],['receive_id',$request->input('entry.0.id')],['isRoot',1]])->orwhere([['sender_id',$request->input('entry.0.id')],['receive_id',$json],['isRoot',1]])->get()->isEmpty()){

					$data = new ReceiveData;
					$sender_db = ReceiveData::where([['sender_id',$json],['receive_id',$request->input('entry.0.id')],['type','message']])->orwhere([['sender_id',$request->input('entry.0.id')],['receive_id',$json],['isRoot',1]],['type','message'])->get()->first();	
					var_dump($sender_db);
					$data->type = 'message';
					$data->user_id = '';
					$data->comments = $request->input('entry.0.messaging.0.message.text');
					$data->attackment = $request->input('entry.0.messaging.0.message.attachments.0.payload.url');
					$data->post_id = $sender_db['oid'];		
					$data->page =  $request->input('entry.0.messaging.0.recipient.id');
					$data->data = $request->input('entry.0.messaging');
				    $data->Isroot = 2 ;
					$data->oid = 'm_'.str_replace('.$','_',$request->input('entry.0.messaging.0.message.mid'));						
					$data->receive_id = $sender_db['receive_id'];
					$data->sender_name = $sender_db['sender_name'];
					$data->sender_id = $sender_db['sender_id'];
			    	$data->parent_id = $sender_db['oid'];
			    	$data->like = false;
					$data->hidden = false;
			    	$data->is_read = false;
					if( $request->input('entry.0.id') == $request->input('entry.0.messaging.0.sender.id')){
						$data->receive_name = 'Me';					
					}
					else{					
						$data->receive_name = $sender_db['sender_name'];;
					}
					$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.messaging.0.timestamp')/1000);
					$data->save();
				}
				else{
					$a=1;
					$b=$a[0]['1']->abc;
				}
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
					$data->attackment = null;
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
			    	$data->like = false;
					$data->hidden = false;
			    	$data->is_read = false;
					$data->save();
				}
			}
			else if ( $request->input('entry.0.changes.0.value.item')  == 'comment' )
			{
				if ( $request->input('entry.0.changes.0.value.verb')  == 'add'){
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
					//////////////////////////////
					if (null === $request->input('entry.0.changes.0.value.message') && null ===$request->input('entry.0.changes.0.value.photo')) {						
						$pagekey=FacebookPages::where('pagesid',$request->input('entry.0.id'))->first()->access_token;
						$requeststring='/'. $request->input('entry.0.changes.0.value.comment_id') .'?fields=attachment';
						$response2 = $fb->get($requeststring,$pagekey);
						$json3 = json_decode($response2->getBody(), true);
						if($json3['attachment']['type']=='sticker'){
							$data->attackment = $json3['attachment']['media']['image']['src'];
						}
						else{							
							$attach = $json3['attachment']['media']['image']['src'];
							$data->attackment = urldecode(substr($attach,strpos($attach,'url=')+4,-5-strpos($attach,'&url=')+strpos($attach,'&_nc_hash=')));
						}
					}
					else{
						$data->attackment = $request->input('entry.0.changes.0.value.photo');
					}
					$data->sender_id = $request->input('entry.0.changes.0.value.sender_id');
					$data->sender_name = $request->input('entry.0.changes.0.value.sender_name');
					$data->user_id = '';
					$data->post_id = $request->input('entry.0.changes.0.value.post_id');	
					$data->page =  $request->input('entry.0.id');
					$data->data = $request->input('entry.0.changes');
					$data->created_at = date("Y-m-d H:i:s", $request->input('entry.0.changes.0.value.created_time'));
					$data->receive_id = $request->input('entry.0.id');
			    	$data->like = false;
					$data->hidden = false;
			    	$data->is_read = false;
					if( $request->input('entry.0.id') != $request->input('entry.0.changes.0.value.sender_id')){
						$data->receive_name = 'You';
					}
					else{
						$data->receive_name = 'Me';
					}
					$data->save();
				}
				else if ( $request->input('entry.0.changes.0.value.verb')  == 'remove'){
					if( ReceiveData::where('oid',$request->input('entry.0.changes.0.value.comment_id'))->select('isroot')->get()[0]['isroot'] == 1 ) {						
						$oidarray = ReceiveData::where('parent_id', $request->input('entry.0.changes.0.value.comment_id'))->select('oid')->get();
						ReceiveData::where('parent_id', $request->input('entry.0.changes.0.value.comment_id'))
														->delete();
					}
					else {						
						$oidarray[] = array('oid'=>$request->input('entry.0.changes.0.value.comment_id'));
						ReceiveData::where('oid', $request->input('entry.0.changes.0.value.comment_id'))
														->delete();
					}
					var_dump($oidarray);
					$data = array("oid"=>$oidarray, "delete"=>1);
				}
				else if ( $request->input('entry.0.changes.0.value.verb')  == 'hide'){
					ReceiveData::where('oid', $request->input('entry.0.changes.0.value.comment_id'))
														->update(['hidden'=>true]);
					$data = array("oid"=>$request->input('entry.0.changes.0.value.comment_id'), "updatehide"=>true);
				}
				else if ( $request->input('entry.0.changes.0.value.verb')  == 'unhide'){
					ReceiveData::where('oid', $request->input('entry.0.changes.0.value.comment_id'))
														->update(['hidden'=>false]);
					$data = array("oid"=>$request->input('entry.0.changes.0.value.comment_id'), "updatehide"=>false);
				}
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
		    	$data->like = false;
				$data->hidden = false;
		    	$data->is_read = false;
				$data->save();
			}
			else if ($request->input('entry.0.changes.0.value.item')  == 'like'){
				if ( $request->input('entry.0.id') == $request->input('entry.0.changes.0.value.sender_id')){
					if($request->input('entry.0.changes.0.value.verb')=='add'){
						$update = true;
					}
					else{
						$update = false;
					}
					ReceiveData::where('oid', $request->input('entry.0.changes.0.value.comment_id'))
														->update(['like'=>$update]);
					$data = array("oid"=>$request->input('entry.0.changes.0.value.comment_id'), "updatelike"=>$update);
				}
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

        if ( strpos($oid,'mid_') ){
        	$requeststring = $fb->request(
		    'POST',
		    '/'.str_replace('t_mid_','t_mid.$',$oid).'/messages',
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
		else{			
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
    }
    function onLike(LaravelFacebookSdk $fb)
    {

        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];
        $type=$_GET["type"];

        if ($type==1){
		  	$requeststring = $fb->request(
		    'POST',
		    '/'.$oid.'/likes'
		    );
		}
		else{
			$requeststring = $fb->request(
		    'DELETE',
		    '/'.$oid.'/likes'
		    );
		}
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
    function onHide(LaravelFacebookSdk $fb)
    {

        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];
        $type=$_GET["type"];

        if ($type==1){
		  	$requeststring = $fb->request(
		    'POST',
		    '/'.$oid,
		    array(
			      'is_hidden' => true,
			    )
		    );
		}
		else{
			$requeststring = $fb->request(
		    'POST',
		    '/'.$oid,
		    array(
			      'is_hidden' => false,
			    )
		    );
		}	  	
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
    function onInbox(LaravelFacebookSdk $fb)
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
