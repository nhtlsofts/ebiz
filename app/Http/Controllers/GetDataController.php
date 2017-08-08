<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\ReceiveData;
use App\product;
use App\Customer;
use App\FacebookPages;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Receipt;
use App\ReceiptDetail;

class GetDataController extends BaseController
{
    function read(Request $request){
        $user_id = Session::get('user_id');
        $table = 'receive_data_'.$user_id;
        db::table($table)->where('parent_id',$request->input('oid'))
                        ->update(['is_read'=>1]);
    }
    function getdata(LaravelFacebookSdk $fb)
	{

	    //$fb->setDefaultAccessToken(Session::get('user_id'));
        /////////////////////////////// lấy comment
        $user_id = Session::get('user_id');
        $table = 'receive_data_'.$user_id;
	    $data1 = DB::table($table)
                ->select($table.'.ava','x1.parent_id',$table.'.oid','x1.type','x1.comments','x1.isroot',$table.'.sender_id',$table.'.sender_name','x1.facebookuser',
                    'x1.post_id','x1.page','x1.data','x1.created_at','x1.updated_at','x1.is_read')
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
                ->leftJoin('customer', function ($join) use ($table) {
                    $join->on('customer.fbid', '=', $table.'.sender_id')
                        ->on('customer.facebookuser', '=', $table.'.facebookuser');
                })
                ->leftJoin( 'facebook_pages' , 'facebook_pages.pagesid' , '=' , 'x1.page' )
                ->where([[$table.'.Isroot' , 1] , ['facebook_pages.isactive', 1 ] ,[$table.'.type','comment'],['x1.facebookuser',$user_id]])
                ->whereRaw('ifnull(customer.banned,0) = 0 ')
                ->orderBy('created_at', 'desc')->take(15)->get()->toArray();

                    //where (attackment is null or attackment like '') and comments not like ''
        ////////////////////////////////////// lấy chat
        $data2 = DB::table($table)
                ->select($table.'.ava','x1.parent_id',$table.'.oid','x1.type','x1.comments','x1.isroot',$table.'.sender_id',$table.'.sender_name','x1.facebookuser',
                    'x1.post_id','x1.page','x1.data','x1.created_at','x1.updated_at','x1.is_read')
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
                ->leftJoin('customer', function ($join) use ($table) {
                    $join->on('customer.fbid', '=', $table.'.sender_id')
                        ->on('customer.facebookuser', '=', $table.'.facebookuser');
                })
                ->leftJoin( 'facebook_pages' , 'facebook_pages.pagesid' , '=' , 'x1.page' )
                ->where([[$table.'.Isroot' , 1] , ['facebook_pages.isactive', 1 ] , [$table.'.type','message'],['x1.facebookuser',$user_id]])
                ->whereRaw('ifnull(customer.banned,0) = 0 ')
                ->orderBy('created_at', 'desc')->take(15)->get()->toArray();

                    //where (attackment is null or attackment like '') and comments not like ''

        $province = db::table('province')->get()->toArray();        
        $district = db::table('district')->get()->toArray();

        return view('showhang',['comments' => $data1,'messages' => $data2,'province' => $province,'district' => $district]);
	}
    function getmoredata(LaravelFacebookSdk $fb,Request $request)
    {
        $time = $request->input('time');
        //$fb->setDefaultAccessToken(Session::get('user_id'));
        $user_id = Session::get('user_id');
        $table = 'receive_data_'.$user_id;
        $data = DB::table($table)
                ->select($table.'.ava','x1.parent_id',$table.'.oid','x1.type','x1.comments','x1.isroot',$table.'.sender_id',$table.'.sender_name','x1.facebookuser',
                    'x1.post_id','x1.page','x1.data','x1.created_at','x1.updated_at','x1.is_read')
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
                ->leftJoin('customer', function ($join) use ($table) {
                    $join->on('customer.fbid', '=', $table.'.sender_id')
                        ->on('customer.facebookuser', '=', $table.'.facebookuser');
                })
                ->leftJoin( 'facebook_pages' , 'facebook_pages.pagesid' , '=' , 'x1.page' )
                ->where([[$table.'.Isroot' , 1] , ['facebook_pages.isactive', 1 ] , [$table.'.type','comment'],['x1.facebookuser',$user_id]])
                ->whereRaw('ifnull(customer.banned,0) = 0 ')
                ->whereRaw('x1.created_at < "'.$time.'"')
                ->orderBy('created_at', 'desc')
                ->take(15)->get()->toArray();
        return $data;
    }
    function getmoredata2(LaravelFacebookSdk $fb,Request $request)
    {
        $time = $request->input('time');
        //$fb->setDefaultAccessToken(Session::get('user_id'));
        $user_id = Session::get('user_id');
        $table = 'receive_data_'.$user_id;
        $data = DB::table($table)
                ->select($table.'.ava','x1.parent_id',$table.'.oid','x1.type','x1.comments','x1.isroot',$table.'.sender_id',$table.'.sender_name','x1.facebookuser',
                    'x1.post_id','x1.page','x1.data','x1.created_at','x1.updated_at','x1.is_read')
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
                ->leftJoin('customer', function ($join) use ($table) {
                    $join->on('customer.fbid', '=', $table.'.sender_id')
                        ->on('customer.facebookuser', '=', $table.'.facebookuser');
                })
                ->leftJoin( 'facebook_pages' , 'facebook_pages.pagesid' , '=' , 'x1.page' )
                ->where([[$table.'.Isroot' , 1] , ['facebook_pages.isactive', 1 ] , [$table.'.type','message'],['x1.facebookuser',$user_id]])
                ->whereRaw('ifnull(customer.banned,0) = 0 ')
                ->whereRaw('x1.created_at < "'.$time.'"')
                ->orderBy('created_at', 'desc')
                ->take(15)->get()->toArray();
        return $data;
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
            $return = Product::select('product_name', DB::raw('CONCAT("{\"name\":\"", product_name, "\",\"price\":\"" , price , "\",\"product_code\":\"" , product_code , "\",\"id\":\"" , id , "\",\"unit\":\"" , unit , "\"}") AS data_value'),DB::raw('"'.$term.'" as text'))->where([['facebookuser',$user_id],['product_name','like','%'.$term.'%']])->paginate(5)->toArray();
        }

        if ($type == 'customer'){
             $return = customer::select('name', DB::raw('CONCAT("{\"oid\":\"", oid, "\",\"fbid\":\"" , ifNull(fbid,\'\') , "\",\"name\":\"" , name , "\",\"email\":\"" , email , "\",\"address\":\"" , address , "\",\"tel\":\"" , tel , "\",\"province\":\"" , province , "\",\"district\":\"" , district , "\",\"banned\":\"" , banned , "\",\"banned_date\":\"" , banned_date , "\"}") AS data_value'),DB::raw('"'.$term.'" as text'))->where([['facebookuser',$user_id],['name','like','%'.$term.'%']])->paginate(5)->toArray();
        }
        if ($type == 'onecustomer'){
             $return = customer::where([['facebookuser',$user_id],['fbid',$term],['banned',0]])->first();
        }
        /// return data
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
        $data = Receipt::select('receipt.id','receipt.code','receipt.customer_id as customer_id','customer.name as customername',
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
        ReceiptDetail::where([['receipt',$request->input('id')],['facebookuser',Session::get('user_id')]])->delete();
        Receipt::where([['id',$request->input('id')],['facebookuser',Session::get('user_id')]])->delete();
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
        Product::where('id',$request->input('id'))->delete();
    }
    function UpdateProductList(Request $request){
        $user_id = Session::get('user_id');
        $code = $request->input('product_code');
        if (!isset($code)){
            $tmpcode = Product::select( DB::raw('MAX(`product_code`) AS number'))->where('facebookuser',$user_id)->first();
            $code = sprintf("%'.04d", (int)$tmpcode['number']+1);
        }
        Product::where('id',$request->input('id'))
                ->update(['product_code' => $code,
                'product_name' => $request->input('product_name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit')]);
    }
    function InsertProductList(Request $request){
        $user_id = Session::get('user_id');
        $product = new Product();
        $code = $request->input('product_code');
        if (!isset($code)){
            $tmpcode = Product::select( DB::raw('MAX(`product_code`) AS number'))->where('facebookuser',$user_id)->first();
            $code = sprintf("%'.04d", (int)$tmpcode['number']+1);
        }
        $product->id = $request->input('id');
        $product->product_code = $code;
        $product->product_name = $request->input('product_name');
        $product->price = $request->input('price');
        $product->unit = $request->input('unit');
        $product->facebookuser=$user_id;
        $product->picture=null;
        //try {
            $product->save();            
       /* }
        catch(\Illuminate\Database\QueryException $ex){ 
            $error = str_replace('product_code','Mã mặt hàng',$ex->errorInfo[2]);
            $error = str_replace('product_name','Tên mặt hàng',$error);
            $error = str_replace('unit','Đơn vị tính',$error);
            $error = str_replace('price','Giá',$error);
            $error = str_replace('picture','hình ảnh',$error);
            throw new \Exception($error);
        }*/
    }

    //customer
    function searchCustomerList(Request $request){
        $user_id = Session::get('user_id');
        if(!isset($price)){
            $price = 0;
        }
        $fromdate=$request->input('banned_date')['from'];
        $todate=$request->input('banned_date')['to'];
        if(!isset($fromdate)){
            $fromdate1 = '1990-01-01 00:00:00';
        } 
        else{
            $fromdate1 = $fromdate . ' 00:00:00';
        }
        if(!isset($todate)){
            $todate1 = '2300-01-01 00:00:00';
        } 
        else{
            $todate1 = $todate . ' 23:59:59';
        }
        $banned =$request->input('banned');
        if ($banned == 'true'){
            $banned = 1;
        }
        if ($banned == 'false'){
            $banned = 0;
        }
        $district=$request->input('district');
        if($district=='0'){
            $district=null;
        }
        $province=$request->input('province');
        if($province=='0'){
            $province=null;
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
            ->where(function ($query) use ($district) {
                $query->where('district','like','%'.$district.'%')
                    ->orWhereRaw('"'.$district.'"=""');
            })  
            ->where(function ($query) use ($province) {
                $query->where('province','like','%'.$province.'%')
                    ->orWhereRaw('"'.$province.'"=""');
            })  
            ->where(function ($query) use ($banned) {
                $query->where('banned',$banned)
                    ->orWhereRaw('"'.$banned.'"=""');
            })
            ->where(function ($query) use ($fromdate1,$todate1,$fromdate,$todate) {
                $query->whereBetween('banned_date',[$fromdate1,$todate1]) 
                    ->orWhereRaw('("'.$fromdate.'"="" and "' .$todate.'"="")');
            })
            ->where('facebookuser',$user_id)         
            ->get()
            ->toArray();
        return $data;
    }
    function DeleteCustomerList(Request $request){
        Customer::where('oid',$request->input('oid'))->delete();
    }
    function UpdateCustomerList(LaravelFacebookSdk $fb,Request $request){
        $user_id = Session::get('user_id');
        $banned =$request->input('banned');
        $baned_date=null;
        if ($banned == 'true'){
            $banned = 1;
            $baned_date = date("Y-m-d H:i:s");
            if ( $request->input('fbid') != null ) {
            //////////////////////// ban nó
                $pageids = FacebookPages::select('pagesid','access_token')->where([['user_id',$user_id],['isactive',1]])->get()->toArray();
                foreach ($pageids as $page) {
                    $fb->setDefaultAccessToken($page['access_token']);
                    $requeststring = $fb->request(
                    'POST',
                    '/'.$page['pagesid'].'/blocked',
                    array(
                      'asid' => [$request->input('fbid')]
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
            //////////////////////////////
            }
        }
        if ($banned == 'false'){
            $banned = 0;
            $baned_date = $request->input('banned_date');
            if ( $request->input('fbid') != null ) {
            //////////////////////// ban nó
                $pageids = FacebookPages::select('pagesid','access_token')->where('user_id',$user_id)->get()->toArray();
                foreach ($pageids as $page) {
                    $fb->setDefaultAccessToken($page['access_token']);
                    $requeststring = $fb->request(
                    'DELETE',
                    '/'.$page['pagesid'].'/blocked',
                    array(
                      'user' => $request->input('fbid')
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
            //////////////////////////////
            }
        }
        Customer::where('oid',$request->input('oid'))
                ->update(['name' => $request->input('name'),
                'email' => $request->input('email'),
                'tel' => $request->input('tel'),
                'address' => $request->input('address'),
                'district' => $request->input('district'),
                'province' => $request->input('province'),
                'banned' => $banned,
                'banned_date' => $baned_date]);
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
        $Customer->banned=0;
        $Customer->save();
    }
}
