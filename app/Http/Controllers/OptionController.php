<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Receipt;

class OptionController extends BaseController
{
    function get() {
	    return view('option');
	}
	function SearchOption(Request $request){
        $user_id = Session::get('user_id');
        $autohideemailandphone =$request->input('autohideemailandphone');
        if ($autohideemailandphone == 'true'){
            $autohideemailandphone = 1;
        }
        if ($autohideemailandphone == 'false'){
            $autohideemailandphone = 0;
        }
        $data = db::table('option')->leftJoin('facebook_pages','option.pageid','=','facebook_pages.pagesid')
        	->where(function ($query) use ($request) {
                $query->where('facebook_pages.pagesname','like','%'.$request->input('pagesname').'%')
                    ->orWhereRaw('"'.$request->input('pagesname').'"=""');
            })  
            ->where(function ($query) use ($autohideemailandphone) {
                $query->where('autohideemailandphone',$autohideemailandphone)
                    ->orWhereRaw('"'.$autohideemailandphone.'"=""');
            })
            ->where('facebookuser',$user_id)         
            ->get()
            ->toArray();
        return $data;
    }
    function UpdateOption(Request $request){
        $user_id = Session::get('user_id');
        $autohideemailandphone =$request->input('autohideemailandphone');
        if ($autohideemailandphone == 'true'){
            $autohideemailandphone = 1;
        }
        if ($autohideemailandphone == 'false'){
            $autohideemailandphone = 0;
        }
        db::table('option')->where('pageid',$request->input('pageid'))
                ->update(['autohideemailandphone' => $autohideemailandphone]);
    }
}