<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
// use Illuminate\Http\Request;
use Closure;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;


class login extends Controller
{
    // use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function index(Request $request){
    	if($request->session()->has('user_id') && $request->session()->get('user_id') !=""){
          return redirect('event/');
        }else{
          return view('login');  
        }  
    }

    public function login(Request $request){

    	$post =  $request->all();
    	$email_id = $post['email_id'];
    	$password = $post['password'];
      $results = DB::table('tblusers')
                   ->select(DB::raw('*'))
                   ->where('email_id', '=', $email_id)
                   ->where('password', '=', md5($password))
                   ->where('is_active', '=', '1')
                   ->first();

    	if(empty($results)){
    		return view('login',["msg"=>'email and password are wrong.']);
    	}
       
      $request->session()->put('user_id', $results->user_id);
      $request->session()->put('email_id', $results->email_id);
      $request->session()->put('name', $results->name);

      $user_event = $request->session()->get('user_event'); 
      $key = base64_decode(urldecode($user_event));
      $key = explode('&',$key);
      $user_id = $key[0]; 
      if($user_event !="" && $user_id == $results->user_id){
       return redirect('event/'.$user_event);
      }else{
       return redirect('event');
      }
    	
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user_id');
        $request->session()->forget('email_id');
        return redirect('/');
    }

}
