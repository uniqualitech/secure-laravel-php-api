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


class api extends Controller
{


	//-------------- API REQUESTS ----------------- //

	function api_request(request $request){
		
		//-------------- CREATE MODEL OBJECT ----------------- //

		$API = new api_model;

		//-------------- CREATE MODEL OBJECT END ----------------- //

		$req = $request->all();
		$file = $request->file();
		$api_request = $req['api_request'];
		if (!is_array($req['data'])) {
	        $data = json_decode($req['data'], true);
	    }else{
	    	$data = $req['data'];
	    }

	    //--------------- GET REQUEST HEADER ---------------//  
	    $headers = apache_request_headers();
	    
	   //print_r($request->header());

	    $authorization = $request->header('Authorization');
	    $device_type = $request->header('device_type');
	    $device_id = $request->header('device_id');

	    //--------------- EXCLUDE SERVICE FROM CHECKING API SESSION  ---------------//  

		if($api_request != "login" && $api_request != "sign_up" && $api_request != "forgot_password" && $api_request != "resend_email" && $api_request != "logout"){

			//--------------- CHECK AVALABILITY OF AUTHORIZATION & DEVICE TYPE & DEVICE ID ---------------//  

// 			if($authorization !="" && $device_type !="" &&  $device_id !=""){

// 				//--------------- GET EXPLODE AUTHORIZATION ---------------// 

// 				$access_header = explode(' ',$authorization);

// 				//--------------- GET ACCESS KEY ---------------//

// 		        if(!isset($access_header[0])){
// 		            return $API->error_invalid('access key');
// 		        }
// 		        $access_key =$access_header[0];

// 		        //--------------- GET ACCESS TOKEN ---------------//
		        
// 		        if(!isset($access_header[1])){
// 		         	return $API->error_invalid('access token');
// 		        }

// 		        //--------------- VALIDET ACCESS KEY ---------------//

// 		        $access_token =$access_header[1];
// 		        if($access_key !="cat"){
// 		           return $API->error_denied('Invalid access_key');
// 		        }

// 		        //--------------- CHECK API SESSION ---------------//

// 		        $authentication= $API->authenticat_api_call($data['user_id'],$access_token,$device_type,$device_id);

// 			    if(count($authentication) == 0){
// 			    	return $API->error_denied('User session expire.Please login again.');
// 			    }
			    
// 			}else{
// 				return $API->error_api();
// 			}
		}else{

			$authentication= $API->authenticat_api_id($req['api_id'],$req['api_secret']);
		    if(count($authentication) == 0){
		    	return $API->error_denied('App api id or api secret not valid.');
		    }
		}


		//--------------- API CALL ---------------//

		if($api_request == "sign_up"){

			return  $API->sign_up($data,$file); 

		}else if($api_request == "login"){

			return  $API->login($data,$file,$device_type,$device_id); 

		}else if($api_request == "forgot_password"){

			return  $API->forgot_password($data); 

		}else if($api_request == "resend_email"){

			return  $API->resend_email($data); 

		}else if($api_request == "update_profile"){

			return  $API->update_profile($data,$file); 

		}else if($api_request == "change_password"){

			return  $API->change_password($data); 

		}else if($api_request == "contact_us"){

			return  $API->contact_us($data); 

		}else if($api_request == "register_for_push"){

			return  $API->register_for_push($data,$device_id,$device_type); 

		}else if($api_request == "update_time_zone"){

			return  $API->update_time_zone($data); 

		}else if($api_request == "update_sync_time"){

			return  $API->update_sync_time($data); 

		}else if($api_request == "sync_event"){

			return  $API->sync_event($data); 

		}else if($api_request == "updated_google_event_id"){

			return  $API->updated_google_event_id($data); 

		}else if($api_request == "logout"){

			return  $API->logout($data,$device_type); 

		}else if($api_request == "get_category"){

			return  $API->get_category($data); 

		}else if($api_request == "add_update_event"){

			return  $API->add_update_event($data,$file); 

		}else if($api_request == "remove_event_media"){

			return  $API->remove_event_media($data,$file); 

		}else if($api_request == "remove_event"){

			return  $API->remove_event($data,$file); 

		}else if($api_request == "upload_media"){

			return  $API->upload_media($data,$file); 

		}else if($api_request == "remove_preloaded_media"){

			return  $API->remove_preloaded_media($data); 

		}else if($api_request == "get_preloaded_media"){

			return  $API->get_preloaded_media($data); 

		}else if($api_request == "add_duplicate_event"){

			return  $API->add_duplicate_event($data); 

		}else if($api_request == "search_user"){

			return  $API->search_user($data); 

		}else if($api_request == "share_event"){

			return  $API->share_event($data); 

		}else if($api_request == "list_event"){

			return  $API->list_event($data); 

		}else if($api_request == "get_event"){

			return  $API->get_event($data); 

		}else if($api_request == "search_event"){

			return  $API->search_event($data); 

		}else if($api_request == "get_event_by_id"){

			return  $API->get_event_by_id($data); 

		}else if($api_request == "get_event_member"){

			return  $API->get_event_member($data); 

		}else if($api_request == "add_to_event_group"){

			return  $API->add_to_event_group($data); 

		}else if($api_request == "remove_from_event_group"){

			return  $API->remove_from_event_group($data); 

		}else if($api_request == "leave_event_group"){

			return  $API->leave_event_group($data); 

		}else if($api_request == "complete_to_do"){

			return  $API->complete_to_do($data); 

		}else if($api_request == "add_comment"){

			return  $API->add_comment($data); 

		}else if($api_request == "get_comment"){

			return  $API->get_comment($data); 

		}else{

			return  $API->error_api(); 

		}		
	}
	//-------------- API REQUESTS END ----------------- //
}
