<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\ReceiveData;
use App\product;
use App\Customer;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Receipt;
use App\ReceiptDetail;

class GetDataController extends BaseController
{

    function getdata(LaravelFacebookSdk $fb)
	{

	    //$fb->setDefaultAccessToken(Session::get('user_id'));
        $user_id = Session::get('user_id');
        $table = 'receive_data_'.$user_id;
	    $data = DB::table($table)
                ->select('x1.parent_id',$table.'.oid','x1.type','x1.comments','x1.isroot',$table.'.sender_id',$table.'.sender_name','x1.facebookuser',
                    'x1.post_id','x1.page','x1.data','x1.created_at','x1.updated_at')
                ->leftJoin(DB::raw("(
                    select parent_id,max(created_at) as createtime
                    from receive_data_".$user_id."
                    where parent_id <> ''
                    group by parent_id
                    ) x"), $table.'.oid', '=', 'x.parent_id')
                ->join(DB::raw("(
                    select * 
                    from receive_data_".$user_id."
                    ) x1"), function($join)
                {
                    $join->on('x1.created_at', '=', 'x.createtime');                        
                    $join->on('x1.parent_id', '=', 'x.parent_id'); 
                })
                ->leftJoin( 'facebook_pages' , 'facebook_pages.pagesid' , '=' , 'x1.page' )
                ->where([[$table.'.Isroot' , 1] , ['facebook_pages.isactive', 1 ] , ['x1.facebookuser',$user_id]])
                ->get()->toArray();

                    //where (attackment is null or attackment like '') and comments not like ''


        $province = db::table('province')->get()->toArray();        
        $district = db::table('district')->get()->toArray();

        return view('showhang',['manages' => $data,'province' => $province,'district' => $district]);
	}
    function getdetail(LaravelFacebookSdk $fb)
    {

        //$fb->setDefaultAccessToken(Session::get('page_key'));
        $user_id = Session::get('user_id');
        $table = 'receive_data_'.$user_id;
        $oid=$_GET["oid"];
        $data = DB::table($table.'')                
                ->where([
                    ['parent_id', $oid]
                    ])
                ->where(function($q) {
                    $q  ->where('comments','<>','')
                        ->orWhereNotNull('attackment');
                })
                ->orderBy('created_at', 'asc')
                ->get()->toArray();

        return $data;
        //
    }

    function getProvince(LaravelFacebookSdk $fb)
    {
        $data = DB::table('province')
                ->select('provinceid','name')
                ->orderBy('provinceid', 'asc')
                ->get()->toArray();

        return $data;
        //
    }

    function getDistrict(LaravelFacebookSdk $fb)
    {
        $data = DB::table('district')
                ->select('districtid','name')
                ->orderBy('districtid', 'asc')
                ->get()->toArray();

        return $data;
        //
    }
    
    function search(Request $request){
        $term = $request->input('q');
        $page = $request->input('page',1);
        $type = $request->input('type');
        $user_id = Session::get('user_id');
        
        // force current page to 5
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        // list all users, with 10 users per page, on page 5

        if ($type == 'product'){
            $return = Product::select('product_name', DB::raw('CONCAT("{\"name\":\"", product_name, "\",\"price\":\"" , price , "\",\"product_code\":\"" , product_code , "\",\"unit\":\"" , unit , "\"}") AS data_value'),DB::raw('"'.$term.'" as text'))->where([['facebookuser',$user_id],['product_name','like','%'.$term.'%']])->paginate(5)->toArray();
        }

        if ($type == 'customer'){
             $return = customer::select('name', DB::raw('CONCAT("{\"oid\":\"", oid, "\",\"fbid\":\"" , ifNull(fbid,\'\') , "\",\"name\":\"" , name , "\",\"email\":\"" , email , "\",\"address\":\"" , address , "\",\"tel\":\"" , tel , "\",\"province\":\"" , province , "\",\"district\":\"" , district , "\"}") AS data_value'),DB::raw('"'.$term.'" as text'))->where([['facebookuser',$user_id],['name','like','%'.$term.'%']])->paginate(5)->toArray();
        }
        if ($type == 'onecustomer'){
             $return = customer::where([['facebookuser',$user_id],['fbid',$term]])->first();
        }
        if ($type == 'onecustomer'){
            return $return;
        }
        else{
            return $return['data'];
        }
    }
    //Receipt
    function searchReceiptList(Request $request){         
        $user_id = Session::get('user_id');
        $fromdate=$request->input('issue_date')['from'];
        $todate=$request->input('issue_date')['to'];
        $ship=$request->input('shipcost');
        $discount=$request->input('totaldiscount');
        $total=$request->input('total');
        if(!isset($fromdate)){
            $fromdate = '1990-01-01 00:00:00';
        } 
        else{
            $fromdate = $fromdate . ' 00:00:00';
        }
        if(!isset($todate)){
            $todate = '2300-01-01 00:00:00';
        } 
        else{
            $todate = $todate . ' 23:59:59';
        }
        if(!isset($ship)){
            $ship = 0;
        }  
        if(!isset($discount)){
            $discount = 0;
        } 
        if(!isset($total)){
            $total = 0;
        }
        $data = Receipt::select('receipt.oid','receipt.customer_id as customer_id','customer.name as customername',
            'customer.address as customeradd','district.name as dname','province.name as pname',
            'customer.tel as tel','receipt.total as total','receipt.shipcost as shipcost',
            DB::raw('receipt.discount+receipt.detaildiscount as totaldiscount'),'receipt.created_at as issue_date'
            )
            ->leftJoin('customer', 'customer.oid', '=', 'receipt.customer_id')
            ->leftJoin('district', 'district.districtid', '=', 'customer.district')
            ->leftJoin('province', 'province.provinceid', '=', 'customer.province')
            ->where(function ($query) use ($request) {
                $query->where('receipt.customer_id','like','%'.$request->input('customer_id').'%')
                    ->orWhereRaw('"'.$request->input('customer_id').'"=""');
            })
            ->where(function ($query) use ($request) {
                $query->where('customer.name','like','%'.$request->input('customername').'%')
                    ->orWhereRaw('"'.$request->input('customername').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('customer.address','like','%'.$request->input('customeradd').'%')
                    ->orWhereRaw('"'.$request->input('customeradd').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('district.name','like','%'.$request->input('dname').'%')
                    ->orWhereRaw('"'.$request->input('dname').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('province.name','like','%'.$request->input('pname').'%')
                    ->orWhereRaw('"'.$request->input('pname').'"=""');
            })   
            ->where(function ($query) use ($request,$ship) {
                $query->where('receipt.shipcost',$ship)
                    ->orWhereRaw('"'.$request->input('shipcost').'"=""');
            })   
            ->where(function ($query) use ($request,$discount) {
                $query->whereRaw('receipt.discount+receipt.detaildiscount = '.$discount .'')
                    ->orWhereRaw('"'.$request->input('totaldiscount').'"=""');
            })   
            ->where(function ($query) use ($request,$total) {
                $query->where('receipt.total',$total)
                    ->orWhereRaw('"'.$request->input('total').'"=""');
            })
            ->where('receipt.facebookuser',$user_id)
            ->whereBetween('receipt.created_at',[$fromdate,$todate])          
            ->get()
            ->toArray();
        return $data;
    }
    function DeleteReceiptList(Request $request){
        ReceiptDetail::where('receipt',$request->input('oid'))->delete();
        Receipt::where('oid',$request->input('oid'))->delete();
    }

    //Product
    function searchProductList(Request $request){
        $user_id = Session::get('user_id');
        $price=$request->input('price');
        if(!isset($price)){
            $price = 0;
        }
        $data = Product::where(function ($query) use ($request) {
                $query->where('product_code','like','%'.$request->input('product_code').'%')
                    ->orWhereRaw('"'.$request->input('product_code').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('product_name','like','%'.$request->input('product_name').'%')
                    ->orWhereRaw('"'.$request->input('product_name').'"=""');
            })   
            ->where(function ($query) use ($request) {
                $query->where('unit','like','%'.$request->input('unit').'%')
                    ->orWhereRaw('"'.$request->input('unit').'"=""');
            })
            ->where(function ($query) use ($request,$price) {
                $query->where('price',$price)
                    ->orWhereRaw('"'.$request->input('price').'"=""');
            })
            ->where('facebookuser',$user_id)         
            ->get()
            ->toArray();
        return $data;
    }
    function DeleteProductList(Request $request){
        Product::where('oid',$request->input('oid'))->delete();
    }
    function UpdateProductList(Request $request){
        $user_id = Session::get('user_id');
        Product::where('oid',$request->input('oid'))
                ->update(['product_code' => $request->input('product_code'),
                'product_name' => $request->input('product_name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit')]);
    }
    function InsertProductList(Request $request){
        $user_id = Session::get('user_id');
        $product = new Product();
        $product->oid = $request->input('product_code');
        $product->product_code = $request->input('product_code');
        $product->product_name = $request->input('product_name');
        $product->price = $request->input('price');
        $product->unit = $request->input('unit');
        $product->facebookuser=$user_id;
        $product->picture='';
        $product->save();
    }

    //customer
    function searchCustomerList(Request $request){
        $user_id = Session::get('user_id');
        if(!isset($price)){
            $price = 0;
        }
        $data = Customer::where(function ($query) use ($request) {
                $query->where('name','like','%'.$request->input('name').'%')
                    ->orWhereRaw('"'.$request->input('name').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('email','like','%'.$request->input('email').'%')
                    ->orWhereRaw('"'.$request->input('email').'"=""');
            })   
            ->where(function ($query) use ($request) {
                $query->where('tel','like','%'.$request->input('tel').'%')
                    ->orWhereRaw('"'.$request->input('tel').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('address','like','%'.$request->input('address').'%')
                    ->orWhereRaw('"'.$request->input('address').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('district','like','%'.$request->input('district').'%')
                    ->orWhereRaw('"'.$request->input('districtid').'"=""');
            })  
            ->where(function ($query) use ($request) {
                $query->where('province','like','%'.$request->input('province').'%')
                    ->orWhereRaw('"'.$request->input('provinceid').'"=""');
            })
            ->where('facebookuser',$user_id)         
            ->get()
            ->toArray();
        return $data;
    }
    function DeleteCustomerList(Request $request){
        Customer::where('oid',$request->input('oid'))->delete();
    }
    function UpdateCustomerList(Request $request){
        $user_id = Session::get('user_id');
        Customer::where('oid',$request->input('oid'))
                ->update(['name' => $request->input('name'),
                'email' => $request->input('email'),
                'tel' => $request->input('tel'),
                'address' => $request->input('address'),
                'district' => $request->input('district'),
                'province' => $request->input('province')]);
    }
    function createcustomercode($user_id)
    {
        $number = Customer::select( DB::raw('MAX(`customer_code`) AS number'))->where('facebookuser',$user_id)->first();
        return sprintf("%'.04d", (int)$number['number']+1);
    }
    function InsertCustomerList(Request $request){
        $user_id = Session::get('user_id');
        $Cus_id=GetDataController::createcustomercode($user_id);
        $Customer = new Customer();
        $Customer->customer_code = $Cus_id;
        $Customer->fbid = null;
        $Customer->facebookuser = $user_id;
        $Customer->name = $request->input('name');
        $Customer->email = $request->input('email');
        $Customer->address=$request->input('address');
        $Customer->tel=$request->input('tel');
        $Customer->province=$request->input('district');
        $Customer->district=$request->input('province');
        $Customer->save();
    }
}
