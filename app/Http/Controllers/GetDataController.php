<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\ReceiveData;

class GetDataController extends BaseController
{

    function getdata(LaravelFacebookSdk $fb)
	{

	    $fb->setDefaultAccessToken(Session::get('page_key'));

	    $data = DB::table('receive_data')
                ->select('x1.parent_id','receive_data.oid','x1.type','x1.comments','x1.isroot','x1.sender_id','x1.sender_name','x1.user_id',
                    'x1.post_id','x1.page','x1.data','x1.created_at','x1.updated_at')
                ->leftJoin(DB::raw("(
                    select parent_id,max(created_at) as createtime
                    from receive_data
                    where parent_id <> ''
                    group by parent_id
                    ) x"), function($join)
                {
                    $join->on('receive_data.oid', '=', 'x.parent_id');
                })
                ->join(DB::raw("(
                    select * 
                    from receive_data
                    where (attackment is null or attackment like '') and comments not like ''
                    ) x1"), function($join)
                {
                    $join->on('x1.created_at', '=', 'x.createtime');                        
                    $join->on('x1.parent_id', '=', 'x.parent_id'); 
                })
                ->where('receive_data.Isroot' , 1)
                ->get()->toArray();

        return view('showhang',['manages' => $data]);
	}
    function getdetail(LaravelFacebookSdk $fb)
    {

        $fb->setDefaultAccessToken(Session::get('page_key'));
        $oid=$_GET["oid"];
        $data = DB::table('receive_data')                
                ->where([
                    ['parent_id', $oid]
                    ])
                ->get()->toArray();

        return $data;
    }
}

