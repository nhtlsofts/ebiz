<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Receipt;

class ReceiptDetailListController extends BaseController
{
    function get(Request $request) {
        $user_id = Session::get('user_id');
    	$receipt = Receipt::select('receipt.id','code as receipt_code','customer_code','Receipt.facebookuser','Receipt.page_id','customer_id','fbid','district','province','name','receipt.tel','email','receipt.address')
    									->leftJoin('customer','customer.oid','=','Receipt.customer_id')
    									->where([['Receipt.id', $request->input('receipt')],['receipt.facebookuser',$user_id]])
                                        ->first(); 
    	$data = db::table('Receipt_Detail')->where('receipt', $request->input('receipt'))->get()->toArray(); 
    	$province = db::table('province')->get()->toArray();        
        $district = db::table('district')->get()->toArray();
        if ($receipt==null){
        	$receipt=new Receipt();
        }
	    return view('ReceiptDetail',['receipt'=>$receipt,'data'=>$data,'province' => $province,'district' => $district]);
	}
}