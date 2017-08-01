<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Http\Request;
use React\EventLoop\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Customer;
use App\Receipt;
use App\ReceiptDetail;

class SaveReceiptcontroller extends BaseController
{
	function createcustomercode($user_id)
	{
		$number = Customer::select( DB::raw('MAX(`customer_code`) AS number'))->where('facebookuser',$user_id)->first();
		return sprintf("%'.04d", (int)$number['number']+1);
	}
	function searchreceiptbycodenuser($code,$user_id)
	{
		$number = Receipt::select('id')->where([['facebookuser',$user_id],['code',$code]])->first();
		return $number['id'];
	}
	function createreceiptcode($user_id)
	{
		$number = Receipt::select( DB::raw('MAX(`code`) AS number'))->where('facebookuser',$user_id)->first();
		if(isset($number)){
			return sprintf("%'.06d", (int)$number['number']+1);
		}
		else {
			return sprintf("%'.06d", 1);
		}
	}
    function Save(LaravelFacebookSdk $fb,Request $request) {
    	$productlist =  $request->input('product');
    	$quantity =  $request->input('quantity');
    	$price =  $request->input('price');   	
    	$id =  $request->input('id');    	
    	$oid =  $request->input('oid');  	
    	$amount =  $request->input('amount');
    	$name =  $request->input('name');
		$tel =  $request->input('tel');
		$email =  $request->input('email');
		$address =  $request->input('address');
		$subtotal =  $request->input('subtotal');
		$vat =  $request->input('vat');
		$ship =  $request->input('ship');
		$total =  $request->input('total');
		$Salepage =  $request->input('Salepage');
		$Cus_id =  $request->input('Cus_id');
		$Cus_fbid =  $request->input('Cus_fbid');
        $province = $request->input('province');
        $district = $request->input('district');
        $receipt_code= $request->input('code');

        $user_id= Session::get('user_id');

        if(!isset($receipt_code)) {
			$customer = new Customer();
			$receipt = new Receipt();		

			if(!isset($Cus_id)){
				$Cus_id=SaveReceiptcontroller::createcustomercode($user_id);	
				$customer->customer_code=$Cus_id;
	            $customer->fbid=$Cus_fbid;
	            $customer->facebookuser=$user_id;
	            $customer->name=$name;
	            $customer->email=$email;
	            $customer->address=$address;
	            $customer->tel=$tel;
	            $customer->province=$province;
	            $customer->district=$district;
	            $customer->banned=0;
	            $customer->save();
			}
			$receipt_code=SaveReceiptcontroller::createreceiptcode($user_id);
			$receipt->code=$receipt_code;
	        $receipt->customer_id= $Cus_id ;   
	        $receipt->page_id= $Salepage ;   
	        $receipt->facebookuser=$user_id;
	        $receipt->address=$address;
	        $receipt->tel= $tel;
	        $receipt->detailamount=(int)$total-(int)$ship-(int)$vat;
	        $receipt->detaildiscount=0;
	        $receipt->discount=0;
	        $receipt->vat=$vat;
	        $receipt->shipcost=$ship;
	        $receipt->total= $total;     
	        $receipt->paid=0;
	        $receipt->save();

	    	for ($x = 0; $x < count($productlist); $x++) {
				$receiptdetail = new ReceiptDetail();
	    		$one=json_decode($productlist[$x],true);
	            $receiptdetail->receipt=SaveReceiptcontroller::searchreceiptbycodenuser($receipt_code,$user_id);
	            $receiptdetail->product=$one['id'];
	            $receiptdetail->product_name=$one['name'];
	            $receiptdetail->unit=$one['unit'];
	            $receiptdetail->price= $price[$x];
	            $receiptdetail->quanlity=$quantity[$x];
	            $receiptdetail->discount=0;
	            $receiptdetail->amount=$amount[$x];
	            $receiptdetail->facebookuser=$user_id;
	            $receiptdetail->save();
			}
		}
		else
		{

			Receipt::updateorcreate([
			    'id' => $id
			],[
			    'code'   => $receipt_code,
			    'customer_id'     => $Cus_id,
			    'page_id' => $Salepage,
			    'facebookuser'    => $user_id,
			    'address'   => $address,
			    'tel'       => $tel,
			    'detailamount'   => (int)$total-(int)$ship-(int)$vat,
			    'detaildiscount'    => 0,
			    'discount'   => 0,
			    'vat'   => $vat,
			    'shipcost'   => $ship,
			    'total'   => $total,
			    'paid'   => 0
			]);

	        foreach (ReceiptDetail::select('id')->where('receipt',$id)->get()->toArray() as $key => $value){
	        	$array1[$key]=(string)$value['id'];
	        }
	        if(!isset($oid)){
	        	$oid[0]='-99';
	        }
			$delete=array_diff($array1,$oid);
            foreach ($delete as $key => $value) {
            	ReceiptDetail::where('id',$value)->delete();
            }
            for ($x = 0; $x < count($productlist); $x++) {
            	$one=json_decode($productlist[$x],true);
            	if (!isset($oid[$x])){
            		$oid[$x]='-99';
            	}
	            ReceiptDetail::updateorcreate([
				    'id' => $oid[$x]
				],[
				    'receipt'   => $id,
				    'product'     => $one['id'],
				    'product_name' => $one['name'],
				    'unit'    => $one['unit'],
				    'price'   => $price[$x],
				    'quanlity'       => $quantity[$x],
				    'discount'   => 0,
				    'amount'    => $amount[$x],
	            	'facebookuser' => $user_id
				]);
        	}
		}
	}
}
