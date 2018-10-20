<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
// use Illuminate\Http\Request;
use Mail;
use Closure;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Cookie;

class event extends Controller
{   
    
    public function __construct(Request $request) {
        
    }

    public function event(Request $request){
        $session = $request->session()->get('user_id');
        if($session == ""){
            return redirect('/');
        }
        return view('event',["msg"=>'Now you can view the event which is shared with you. To view the event use the link you received from Eclat App.',
                             "event_error"=>'']); 
    }

    public function event_by_id(Request $request,$user_event = 0){
        $session = $request->session()->get('user_id');
        if($session == ""){
            $request->session()->put('user_event', $user_event);
            return redirect('/');
        }
        $key = base64_decode(urldecode($user_event));
        $key = explode('&',$key);
        $user_id = @$key[0];
        $event_id = @$key[1];
        $start_date_time = @$key[2];
        $end_date_time = @$key[3];
        if( !isset($user_id) || is_int($user_id) || $user_id == "" || !isset($event_id) || is_int($event_id) || $event_id == ""){
            return $this->event_error();
        }
        $request->session()->put('user_event', $user_event);
        $request->session()->get('user_event');
        $current_user = $request->session()->get('user_id');
       
        if((string) $current_user != (string) $user_id){
            // $request->session()->forget('user_id');
            // $request->session()->forget('email_id');
            //return redirect('/');
           return $this->event_error();
        }

        $request->session()->forget('user_event');
        $event = DB::table('tblevents AS e')
                   ->select(DB::raw('*'))
                   ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                   ->where('e.event_id','=',$event_id)
                   ->first(); 
                   
        $API = new api_model;           
        $user = $API->getUserData($event->user_id);
        $created_by = (isset($user[0]->name) && $user[0]->name !="")? $user[0]->name : "";
        $user = $API->getUserData($event->last_updated_by);
        $last_updated_by = (isset($user[0]->name) && $user[0]->name !="")? $user[0]->name : "";
        if(count($event) > 0){ 
            $media =DB::table('tblevent_media')
                     ->select(DB::raw('*'))
                     ->where('event_id', '=', $event_id)
                     ->get();    
            // $mediaArr = array();
            // if(count($media) > 0){
            //     foreach ($media as $val) {
            //       $mediaArr[] = $val->file_url;
            //     }
            // }                     
            return view('event',["event"=>$event,"media"=>$media,"created_by"=>$created_by,"last_updated_by"=>$last_updated_by,"start_date_time"=>$start_date_time,"end_date_time"=>$end_date_time,"msg"=>'']);
        }else{
            return $this->event_error();
        }    
    }

    public function event_error(){
        return view('event',["event"=>'',
                             "msg"=>'This event is not shared with this account, please use the same account where event is shared.',
                             "event_error"=>'This event is not shared with this account, please use the same account where event is shared.']);
    }

    public function add_event_comment(Request $request){
      extract($_POST);
      $user_id = $request->session()->get('user_id');
      DB::table('tblcomment')
        ->insert([ 'user_id' => $user_id,
                   'event_id' => $event_id,
                   'comment' => addslashes(strip_tags($comment)),
                   'created_at' => date('Y-m-d H:i:s'),
                   'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function get_event_comment(Request $request){
      extract($_POST);
      $resVal =DB::table('tblcomment As c')
                 ->select(DB::raw('c.comment_id,c.comment,u.name,u.profile_pic,c.created_at AS comment_created_at,c.updated_at AS comment_updated_at'))
                 ->join('tblusers AS u', 'u.user_id', '=', 'c.user_id')
                 ->where('event_id','=',$event_id)
                 ->orderBy('comment_created_at', 'DESC')
                 ->get();
      return json_encode($resVal);
    }
}
