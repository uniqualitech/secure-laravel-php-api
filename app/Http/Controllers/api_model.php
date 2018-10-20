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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Carbon\Carbon;

use File;

if($host = request()->getHttpHost() != "localhost:8000"){
  // require 'vendor/autoload.php';
}

class api_model extends Controller
{

	//-------------- API METHODS ----------------- //
   
    function sign_up($data,$file){

	    if (!isset($data['name']) || $data['name'] == '') {
        return $this->error_invalid('Name');
      }
      if (!isset($data['phone']) || $data['phone'] == '') {
          //return $this->error_invalid('Phone');
      }
      if (!isset($data['email_id']) || $data['email_id'] == '') {
        return $this->error_invalid('email id');
      }
      if (!isset($data['password']) || $data['password'] == '') {
        return $this->error_invalid('password');
      }
      if (!isset($data['secret']) || $data['secret'] == '') {
        return $this->error_invalid('Secret');
      }
      extract($data);	
      //--------------------- AUTHENTICAT SECRET --------------------//

      $generated_secret= $this->secret_generator($email_id,$password);
      if($generated_secret != $secret){
        return $this->error_denied("Invalid secret.");
      }
        
      //--------------------- CHECK EMAIL ALREADY AVAILABLE --------------------//

	   	$data = DB::table('tblusers')
                ->select(DB::raw('*'))
                ->where('email_id', '=', $email_id)
                ->get();

	    if(count($data) > 0 ){
	    	return  $this->error_denied("Email address already in use. Please try another email."); 
	    }   

      //--------------------- ADD PROFILE IMAGE --------------------//

	    $profile_pic ="";
	    if(isset($file) && !empty($file)){
	    	$file = (object) $file;
	    	$file = $file->profile_pic;
	        $destinationPath = 'uploads/user_profile/';
	        $file_name = $this->file_name().'.png';
	        $file->move($destinationPath,$file_name);
	        $profile_pic = 'uploads/user_profile/'.$file_name;
	    } 

      //--------------------- GENERAT ACTIVATION CODE --------------------//

	    $activtion_key = md5(rand(1,10000000000000));

      //--------------------- ADD USER DATA --------------------//

	    DB::table('tblusers')->insert(['name' => $name,
                									   'phone' => @$phone,
                									   'email_id' => $email_id,
                									   'password' => $password,
                									   'profile_pic' => $profile_pic,
                                     'activtion_key' => $activtion_key,
                									   'created_at' => date('Y-m-d H:i:s'),
                									   'updated_at' => date('Y-m-d H:i:s')]
		  );

      $user_id = DB::getPdo()->lastInsertId();

      //--------------------- SEND ACTIVATION MAIL --------------------//
      
      $from = "MY APP";
      $url=$user_id."&".$activtion_key."&".$email_id;
      $url = url('/') . "/active_user/" . urlencode(base64_encode($url));
     
      $html = ' <html>
                  <body>
                    <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                      <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                              <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Activation email</span><br/>
                        Hello <span style="font-weight: 400;color: #ffffff;text-transform: capitalize;"> '.$name.'</span>,<br/>
                      </span>
                        <span style="color:#ffffff;">Welcome to the MY APP, to activate your account, click on the following link <a href='.@$url.' style="font-weight: 900;color: #11509e;">(Activate account)</a></span>
                        <br/> Thank you!
                        <br/> <span style="color:#ffffff;font-weight: 900;">Eclat Team</span>
                    </p>
                  </body>
                </html>';            
      
      $mail = new PHPMailer(true);
      $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
      $mail->addAddress($email_id, '');
      $mail->Subject  = "Activate Your MY APP Account";
      $mail->MsgHTML($html);
      $mail->IsHTML(true);
      $mail->send(); 

	    $data = DB::table('tblusers')
                  ->select(DB::raw('*'))
                  ->where('user_id', '=', $user_id)
                  ->get();

      //--------------------- API RESPONSE --------------------//            

	    $resArr = array();  
	    if(count($data) > 0){               
        $resArr['user_id'] = (string) $data[0]->user_id;
        $resArr['email_id'] = (isset($data[0]->email_id) && $data[0]->email_id != "") ? $data[0]->email_id : "";
        $resArr['name'] = (isset($data[0]->name) && $data[0]->name != "") ? $data[0]->name : ""; 
        $resArr['phone'] = (isset($data[0]->phone) && $data[0]->phone != "") ? $data[0]->phone : ""; 
        $resArr['profile_pic'] = (isset($data[0]->profile_pic) && $data[0]->profile_pic != "") ? url('/').'/'.$data[0]->profile_pic : "";

	    	return  $this->api_success($resArr,"An activation link has been sent to your email. Please click the link within the email to activate your account."); 

	    }else {
	    	return  $this->error_denied("Oops somthing went wrong,please try again after some time."); 
	    }
    } 

    function login($data,$file,$device_type,$device_id){

	    if (!isset($data['email_id']) || $data['email_id'] == '') {
           return $this->error_invalid('email id');
      }
      if (!isset($data['password']) || $data['password'] == '') {
         return $this->error_invalid('password');
      }
      if (!isset($data['secret']) || $data['secret'] == '') {
         return $this->error_invalid('secret');
      }
      extract($data);

      //--------------------- AUTHENTICAT SECRET -------------------- //

      $generated_secret= $this->secret_generator($email_id,$password);
      if($generated_secret != $secret){
        return $this->error_denied("Invalid secret.");
      }

	   	$data = DB::table('tblusers')
	                     ->select(DB::raw('*'))
	                     ->where('email_id', '=', $email_id)
	                     ->where('password', '=', $password)
                       ->where('is_active', '=', '1')
	                     ->get();
	    $resArr = array();  
	    if(count($data) > 0){   

	    	//---------------- GENERAT API SESSION   ----------------- //

	    	$access_token = $this->access_token();
	    	$access_session = date("Y-m-d H:i:s", strtotime("+365 day"));
	    	$access_session = strtotime($access_session);

	    	if($device_type !="" &&  $device_id !=""){
		    	$get_api_session = DB::table('tblapi_session')
				                     ->select(DB::raw('*'))
				                     ->where('user_id', '=', $data[0]->user_id)
				                     ->where('device_type', '=', $device_type)
				                     ->where('device_id', '=', $device_id)
				                     ->get();

		      if(count($get_api_session) > 0){             
			    	DB::table('tblapi_session')
		          ->where('user_id', $data[0]->user_id)
		          ->where('device_type', $device_type)
		          ->where('device_id', $device_id)
		          ->update(['access_token' => $access_token,
		          			    'access_session' => $access_session,
							          'updated_at' => date('Y-m-d H:i:s')]);
			    }else{
			    	DB::table('tblapi_session')->insert([  'access_token' => $access_token,
                    														   'access_session' => $access_session,
                    														   'device_type' => $device_type,
                    														   'device_id' => $device_id,
                    														   'user_id' => $data[0]->user_id,
                    														   'created_at' => date('Y-m-d H:i:s'),
                    														   'updated_at' => date('Y-m-d H:i:s')]);									   
			    }
		    }else{
		    	$access_token = "";
		    }      

		    //--------------  API RESPONSE   ----------------- //   
	     	
        $resArr['user_id'] = (string) $data[0]->user_id;
        $resArr['email_id'] = (isset($data[0]->email_id) && $data[0]->email_id != "") ? $data[0]->email_id : ""; 
        $resArr['name'] = (isset($data[0]->name) && $data[0]->name != "") ? $data[0]->name : ""; 
        $resArr['phone'] = (isset($data[0]->phone) && $data[0]->phone != "") ? $data[0]->phone : ""; 
        $resArr['profile_pic'] = (isset($data[0]->profile_pic) && $data[0]->profile_pic != "") ? url('/').'/'.$data[0]->profile_pic : ""; 
        $resArr['access_token'] = $access_token; 
	    	return  $this->api_success($resArr,"User loging successfully."); 
	    }else {
	    	return  $this->error_denied("Sorry we couldn't log you in. Please check your email address and password were correctly entered. If you have not yet activated your account, please do so by clicking the link in your email."); 
	    }
    } 

    function forgot_password($data){

	    if (!isset($data['email_id']) || $data['email_id'] == '') {
            return $this->error_invalid('email_id');
        }
        extract($data);

      //--------------- CHECK EMAIL --------------------//
      
    	$userData = DB::table('tblusers')
                     ->select(DB::raw('*'))
                     ->where('email_id', '=', $email_id)
                     ->first();            
      $url=""; 
      if (count($userData) == 0) {
      	return $this->error_denied('User not registered.');
      }else if (count($userData) > 0) {

        //--------------- SENT EMAIL FOR CHANGE PASSWORD --------------------//
      
     
        $ref_key = md5(rand(1000, 123456789456789));
        DB::table('tblusers')
          ->where('email_id', $email_id)
          ->update(['ref_key' => $ref_key,
  				'updated_at' => date('Y-m-d H:i:s')]);
       
        $key = $email_id."&".$ref_key;
        $from = "MY APP";
        $url = url('/') . "/change_password/" . urlencode(base64_encode($key));
        $html = '<html>
                  <body>
                    <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                      <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                              <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Forgot Password</span><br/>
                        Mail Form: <span style="font-weight: 400;color: #ffffff;">'.@$from.'</span>,<br/>
                        Hello <span style="font-weight: 400;color: #ffffff;text-transform: capitalize;"> '.$userData->name.'</span>,<br/>
                      </span>
                        <span>We received a request to reset your password for your MY APP account '.@$email_id.'<br/>
                        Click the “Reset Password” Link to set a new password. This link will be active for one time use only. <a href='.@$url.' style="font-weight: 900;color: #11509e;">(Reset Password)</a></span>
                        <br/> Thank you
                        <br/> <span style="color:#ffffff;font-weight: 900;">MY APP</span>
                    </p>
                  </body>
                </html>';            
      
        $mail = new PHPMailer(true);
        $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
        $mail->addAddress($email_id, '');
        $mail->Subject  = "Forgot Password request";
        $mail->MsgHTML($html);
        $mail->IsHTML(true);
        $mail->send(); 
     
      }

      $resArr = array();
      return $this->api_success($resArr, 'A password reset link has been sent to your email.');

    } 

    function resend_email($data){
      
      if (!isset($data['email_id']) || $data['email_id'] == '') {
          return $this->error_invalid('email id');
      }
      extract($data); 
      
      //--------------------- CHECK EMAIL ALREADY AVAILABLE --------------------//

      $user = DB::table('tblusers')
                ->select(DB::raw('*'))
                ->where('email_id', '=', $email_id)
                ->first();

      if(count($user) > 0){
        if($user->is_active == "1"){
          return $this->error_denied('Your account already Activated.');
        }
      }else{
        return $this->error_denied('User not registered.');
      }  

      
      //--------------------- SEND ACTIVATION MAIL --------------------//

      $from = "MY APP";
      $url=$user->user_id."&".$user->activtion_key."&".$user->email_id;
      $url = url('/') . "/active_user/" . urlencode(base64_encode($url));
     
      $html = '<html>
                  <body>
                    <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                      <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                              <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Activation email</span><br/>
                        Mail Form: <span style="font-weight: 400;color: #ffffff;">'.@$from.'</span>,<br/>
                        Hello <span style="font-weight: 400;color: #ffffff;text-transform: capitalize;"> '.$user->name.'</span>,<br/>
                      </span>
                        <span>Welcome to the MY APP, to activate your account, click on the following link <a href='.@$url.' style="font-weight: 900;color: #11509e;">(Active account)</a></span>
                        <br/> Thank you
                        <br/> <span style="color:#ffffff;font-weight: 900;">MY APP</span>
                    </p>
                  </body>
                </html>';            
      
      $mail = new PHPMailer(true);
      $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
      $mail->addAddress($user->email_id, '');
      $mail->Subject  = "Activate Your MY APP Account";
      $mail->MsgHTML($html);
      $mail->IsHTML(true);
      $mail->send(); 
      $resArr = array();
      return  $this->api_success($resArr,"Activation Mail set at your email. To activate your account, follow the instructions sent to the email you provided."); 

    } 

    function update_profile($data,$file){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
            return $this->error_invalid('user_id');
      }
      if (!isset($data['name']) || $data['name'] == '') {
            //return $this->error_invalid('Name');
      }
      if (!isset($data['phone']) || $data['phone'] == '') {
          //return $this->error_invalid('Phone');
      }
      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  
        
      //--------------------- ADD PROFILE IMAGE --------------------//

      $profile_pic ="";
      $set_profile = "";
      if(isset($file) && !empty($file)){
        $file = (object) $file;
        $file = $file->profile_pic;
          $destinationPath = 'uploads/user_profile/';
          $file_name = $this->file_name().'.png';
          $file->move($destinationPath,$file_name);
          $profile_pic = 'uploads/user_profile/'.$file_name;
          //$set_profile = array('profile_pic'=>$profile_pic);
          DB::table('tblusers')
            ->where('user_id', $user_id)
            ->update(['profile_pic'=>$profile_pic,
                      'updated_at' => date('Y-m-d H:i:s')]);
      } 

      $set_name = "";
      if(isset($name) && $name !=""){
        DB::table('tblusers')
        ->where('user_id', $user_id)
        ->update(['name' => $name,
                  'updated_at' => date('Y-m-d H:i:s')]);
      }
      $set_phone = "";
      if(isset($phone) && $phone !=""){
        DB::table('tblusers')
        ->where('user_id', $user_id)
        ->update(['phone'=>$phone,
                  'updated_at' => date('Y-m-d H:i:s')]);
      }

      
      $data = DB::table('tblusers')
                  ->select(DB::raw('*'))
                  ->where('user_id', '=', $user_id)
                  ->get();

      //--------------------- API RESPONSE --------------------//            

      $resArr = array();  
      if(count($data) > 0){               
        $resArr['user_id'] = (string) $data[0]->user_id;
        $resArr['email_id'] = (isset($data[0]->email_id) && $data[0]->email_id != "") ? $data[0]->email_id : "";
        $resArr['name'] = (isset($data[0]->name) && $data[0]->name != "") ? $data[0]->name : ""; 
        $resArr['phone'] = (isset($data[0]->phone) && $data[0]->phone != "") ? $data[0]->phone : ""; 
        $resArr['profile_pic'] = (isset($data[0]->profile_pic) && $data[0]->profile_pic != "") ? url('/').'/'.$data[0]->profile_pic : "";

        return  $this->api_success($resArr,"Profile updated successfully."); 

      }else {
        return  $this->error_denied("Oops somthing went wrong,please try again after some time."); 
      }
    } 

    function change_password($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
            return $this->error_invalid('user_id');
      }
      if (!isset($data['old_password']) || $data['old_password'] == '') {
        return $this->error_invalid('Old password');
      }
      if (!isset($data['new_password']) || $data['new_password'] == '') {
        return $this->error_invalid('New password');
      }
      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  
        
      //--------------------- CHECK OLD PASSWORD --------------------//

      $data = DB::table('tblusers')
                ->select(DB::raw('*'))
                ->where('user_id', '=', $user_id)
                ->where('Password', '=', $old_password)
                ->get();

      if(count($data) == 0 ){
        return  $this->error_denied("Old Password is not matching with existing password."); 
      }   

      //--------------------- UPDATE PASSWORD --------------------//

      DB::table('tblusers')
        ->where('user_id', $user_id)
        ->update(['password' => $new_password,
                  'updated_at' => date('Y-m-d H:i:s')]);

      //--------------------- API RESPONSE --------------------//            

      $resArr = array();  
      return  $this->api_success($resArr,"Success! Your Password has been changed!"); 
    } 

    function contact_us($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['subject']) || $data['subject'] == '') {
        return $this->error_invalid('subject');
      }
      if (!isset($data['body']) || $data['body'] == '') {
          return $this->error_invalid('body id');
      }
      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  
  
      //--------------------- SEND CONTACT US MAIL --------------------//
      
      $from = "MY APP";
      $html = '<html>
                  <body>
                    <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                      <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                              <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Contact Us</span><br/>
                        User Name <span style="font-weight: 400;color: #ffffff;text-transform: capitalize;"> '.$user[0]->name.'</span>,<br/>
                      </span>
                      <span style="color:#ffffff;"><strong>Subject : </strong>'. $subject.'</span><br/>
                      <span style="color:#ffffff;"><strong>Details : </strong>'. $body.'</span>
                      <br/> Thank you!
                      <br/> <span style="color:#ffffff;font-weight: 900;">'.$user[0]->name.'</span>
                    </p>
                  </body>
                </html>';            
      
      $mail = new PHPMailer(true);
      $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
      $mail->addAddress('test@gmail.com');
      $mail->Subject  = $subject;
      $mail->MsgHTML($html);
      $mail->IsHTML(true);
      $mail->send(); 

      $data = DB::table('tblusers')
                  ->select(DB::raw('*'))
                  ->where('user_id', '=', $user_id)
                  ->get();

      //--------------------- API RESPONSE --------------------//            

      $resArr = array();  
      return  $this->api_success($resArr,"Your message has been sent. Thank you for contacting Eclat support. Our team will contact you regarding your issue."); 

    } 

    function register_for_push($data,$device_id,$device_type){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['device_id']) || $data['device_id'] == '') {
        //return $this->error_invalid('device_id');
      }
      if (!isset($data['device_token']) || $data['device_token'] == '') {
        return $this->error_invalid('device_token');
      }
      if (!isset($data['certificate_type']) || $data['certificate_type'] == '') {
        return $this->error_invalid('certificate_type');
      }
      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  

      $resVal = DB::table('tblpush_user')
                ->select(DB::raw('*'))
                ->where('user_id', '=', $user_id)
                ->where('device_type', '=', 'ios')
                ->where('device_token', '=', $device_token)
                ->first();

      if(count($resVal) > 0){
        DB::table('tblpush_user')
          ->where('push_user_id', '=', $resVal->push_user_id)
          ->delete();
      }       

      //--------------------- ADD USER DATA --------------------//

      DB::table('tblpush_user')->insert(['user_id' => $user_id,
                                         'device_id' => $device_id,
                                         'device_type' => 'ios',
                                         'device_token' => $device_token,
                                         'certificate_type' => $certificate_type,
                                         'created_at' => date('Y-m-d H:i:s'),
                                         'updated_at' => date('Y-m-d H:i:s')]);

      
      $resArr = array();
      return  $this->api_success($resArr,"You Register for push successfully. "); 

    }

    function update_time_zone($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['time_zone']) || $data['time_zone'] == '') {
        return $this->error_invalid('time_zone');
      }

      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  

      DB::table('tblusers')
        ->where('user_id', $user_id)
        ->update(['time_zone' => $time_zone,
                  'updated_at' => date('Y-m-d H:i:s')]);

      $resArr = array();
      return  $this->api_success($resArr,"Timezone updated."); 

    }

     function update_sync_time($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }

      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  

      DB::table('tblusers')
        ->where('user_id', $user_id)
        ->update(['sync_time' => date('Y-m-d H:i:s'),
                  'updated_at' => date('Y-m-d H:i:s')]);
      DB::table('tblevents')
        ->where('user_id', $user_id)
        ->update(['is_sync' => '1']);  

      $resArr = array();
      return  $this->api_success($resArr,"Sync time updated."); 

    }

    function logout($data,$device_type){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      extract($data); 

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  

      $resVal = DB::table('tblpush_user')
                ->select(DB::raw('*'))
                ->where('user_id', '=', $user_id)
                ->where('device_type', '=', 'ios')
                ->first();

      if(count($resVal) > 0){
        DB::table('tblpush_user')
          ->where('push_user_id', '=', $resVal->push_user_id)
          ->delete();
      }       
      
      $resArr = array();
      return  $this->api_success($resArr,"Logout Successfully."); 

    } 

    function get_category($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
           return $this->error_invalid('user_id id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }   

      $data =DB::table('tblcategory')
               ->select(DB::raw('*'))
               ->orderBy('category_name', 'asc')
               ->get();

      //---------------------  API RESPONSE  --------------------//
               
      $resArr = array(); 
      if(count($data) > 0){
        $i=0;
        foreach ($data as $value) {
          $resArr[$i]['category_id'] = (string) $value->category_id;
          $resArr[$i]['category_name'] = $value->category_name;
          $i++;
        }  
      }
      return  $this->api_success($resArr,"Category retrive successfully."); 

    } 

    function add_update_event($data,$file){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        //return $this->error_invalid('event_id');
      }
      if (!isset($data['google_event_id']) || $data['google_event_id'] == '') {
        //return $this->error_invalid('event_id');
      }
      if (!isset($data['event_type']) || $data['event_type'] == '') {
        return $this->error_invalid('event_type');
      }
      if (!isset($data['title']) || $data['title'] == '') {
        //return $this->error_invalid('title');
      }
      if (!isset($data['description']) || $data['description'] == '') {
        //return $this->error_invalid('description');
      }
      if (!isset($data['category_id']) || $data['category_id'] == '') {
        //return $this->error_invalid('category_id');
      }

      if (!isset($data['other_category']) || $data['other_category'] == '') {
        if (isset($data['category_id']) && $data['category_id'] == '-1') {
          return $this->error_invalid('other_category');
        }
      }
      
      if (!isset($data['tag']) || $data['tag'] == '') {
        //return $this->error_invalid('tag');
      }

      // if($data['event_type'] !="3" && $data['event_type'] !="4" )  {
      //   if (!isset($data['start_date']) || $data['start_date'] == '') {
      //         return $this->error_invalid('start_date');
      //     }
      //   if (!isset($data['start_time']) || $data['start_time'] == '') {
      //     if(isset($data['all_day']) && $data['all_day'] != '1'){
      //       return $this->error_invalid('start_time');
      //     }
      //   }
      // }  

      if( $data['event_type'] !="4" && $data['event_type'] !="3" )  {
        if (!isset($data['priority']) || $data['priority'] == '') {
          if(isset($data['event_type']) && $data['event_type'] == "3"){
            return $this->error_invalid('priority');
          }  
        }
        if (!isset($data['start_date']) || $data['start_date'] == '') {
            return $this->error_invalid('start_date');
        }
        if (!isset($data['start_time']) || $data['start_time'] == '') {
          if(isset($data['all_day']) && $data['all_day'] != '1'){
            return $this->error_invalid('start_time');
          }
        }
        if (!isset($data['end_date']) || $data['end_date'] == '') {
          if ($data['event_type'] != "3" && $data['event_type'] != "4") {
            //return $this->error_invalid('end_date');
          }
        }
        if (!isset($data['end_time']) || $data['end_time'] == '') {
          if ($data['event_type'] != "3" && $data['event_type'] != "4") {
            if(isset($data['all_day']) && $data['all_day'] != '1'){
              //return $this->error_invalid('end_time');
            }
          }  
        }
        if (!isset($data['all_day']) || $data['all_day'] == '') {
          return $this->error_invalid('all_day');
        }
        if (!isset($data['location']) || $data['location'] == '') {
          //return $this->error_invalid('location');
        }
        if (!isset($data['latitude']) || $data['latitude'] == '') {
          //return $this->error_invalid('latitude');
        }
        if (!isset($data['longitude']) || $data['longitude'] == '') {
          //return $this->error_invalid('longitude');
        }
        if (!isset($data['repeat_mode']) || $data['repeat_mode'] == '') {
          return $this->error_invalid('repeat_mode');
        }
        if (!isset($data['custom_repeat']) || @$data['custom_repeat'] == '') {
          if (!isset($data['custom_repeat']) && @$data['custom_repeat'] == "6") {
            return $this->error_invalid('custom_repeat');
          }
        }
        if (!isset($data['notification_type']) || $data['notification_type'] == '') {
          return $this->error_invalid('notification_type');
        }
        if (!isset($data['notification']) || $data['notification'] == '') {
          return $this->error_invalid('notification');
        }
        if (!isset($data['notification_type']) || $data['notification_type'] == '') {
          if(isset($data['notification']) || $data['notification'] != "0"){
            return $this->error_invalid('notification_type');
          }  
        }
      }  
      if (!isset($data['related_event_id']) || $data['related_event_id'] == '') {
        if(isset($event_type) && $event_type == '3'){
          return $this->error_invalid('related event id');
        }  
      }

      // if (!isset($data['set_reminder']) || $data['set_reminder'] == '') {
      //   //return $this->error_invalid('set_reminder');
      // }
      // if (!isset($data['with_preloded_media']) || $data['with_preloded_media'] == '') {
      //   //return $this->error_invalid('set_reminder');
      // }
      // if (!isset($data['reminder_ids']) || $data['reminder_ids'] == '') {
       
      // }
      extract($data); 
        
      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }   

      $values= "";
      $set= "";
      $mediaArr = array();
      if(isset($media_count) && $media_count !="0" && $media_count !=""){
        if(isset($file) && !empty($file)){
          $i = 1;
          foreach ($file as $value) {
            $filetype = explode('/',$value->getMimeType());
            $extension = @$filetype[1];
            $destinationPath = 'uploads/media/';
            $file_name = $this->file_name().'_event_media.'.$extension;
            $value->move($destinationPath,$file_name);
            $file_url = url('/').'/'.'uploads/media/'.$file_name;
            $media_title = 'media'.$i."_title";
            $media_icon_link = 'media'.$i."_icon_link";
            //$values.=$media.',';
            $mediaArr[$i]['file_url'] = $file_url;
            $mediaArr[$i]['title'] = $data[$media_title];
            $mediaArr[$i]['icon_link'] = $data[$media_icon_link];
            $i++;
          }
          $values=trim($values,',');

        }      
      }

      $event_state = "0";
      if(isset($google_event_id) && $google_event_id != ""){
        $resVal1 = DB::table('tblevents')
                     ->select(DB::raw('*'))
                     ->where('user_id', '=', $user_id)
                     ->where('google_event_id', '=', $google_event_id)
                     ->get();
        if(count($resVal1) > 0){
          $event_state = "1";
        }               
      }
      if(isset($with_preloded_media) && $with_preloded_media !=""){
        DB::table('tblpreloaded_media')
          ->where('user_id', '=', $user_id)
          ->delete();
      }
      $resArr = array();  
      if(($event_id == "" && $google_event_id =="") || (isset($google_event_id) && $google_event_id !="" && $event_state == '0')){
        //--------------------- ADD EVENT --------------------//
          
        DB::table('tblevents')
          ->insert([ 'user_id' => @$user_id,
                     'related_event_id' => (isset($related_event_id) && $related_event_id != "")? $related_event_id : "",
                     'google_event_id' => (isset($google_event_id) && $google_event_id != "")? $google_event_id : "",
                     'event_type' => @$event_type,
                     'title' => (isset($title) && $title != "")? addslashes($title) : "",
                     'description' => (isset($description) && $description != "")? addslashes($description) : "",
                     'category_id' => (isset($category_id) && $category_id != "")? $category_id : "0",
                     'other_category' => (isset($other_category) && $other_category != "")? addslashes($other_category) : "",
                     'tag' =>  (isset($tag) && $tag != "")? $tag : "",
                     'priority' => (isset($priority) && $priority != "")? $priority : "0",
                     'start_date' => (isset($start_date) && $start_date != "")? date('Y-m-d',strtotime($start_date)) : "",
                     'start_time' => (isset($start_time) && $start_time != "")? $start_time : "",
                     'end_date' => (isset($end_date) && $end_date != "")? date('Y-m-d',strtotime($end_date)) : "",
                     'end_time' => (isset($end_time) && $end_time != "")? $end_time : "",
                     'all_day' => (isset($all_day) && $all_day != "")? $all_day : "",
                     'location' =>  (isset($location) && $location != "") ?$location : "",
                     'latitude' =>  (isset($latitude) && $latitude != "")? $latitude : "",
                     'longitude' =>  (isset($longitude) && $longitude != "")? $longitude : "",
                     'repeat_mode' =>  (isset($repeat_mode) && $repeat_mode != "")? $repeat_mode : "",
                     'custom_repeat' =>  (isset($custom_repeat) && $custom_repeat != "")? json_encode($custom_repeat) : "",
                     'notification_type' => (isset($notification_type) && $notification_type != "")? $notification_type : "",
                     'notification' => (isset($notification) && $notification != "")? $notification : "",
                     'custom_notification' => (isset($custom_notification) && $custom_notification != "")? json_encode($custom_notification) : "",
                     'set_reminder' => (isset($set_reminder) && $set_reminder != "")? $set_reminder : "",
                     'reminder_date_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d',strtotime($reminder_date_time)) : "",
                     'last_updated_by' => @$user_id,
                     'created_at' => date('Y-m-d H:i:s'),
                     'updated_at' => date('Y-m-d H:i:s')]);

          $event_id = DB::getPdo()->lastInsertId();

          if(isset($reminder_ids) && $reminder_ids !=""){
            $reminder_ids = explode(',',$reminder_ids);
            foreach ($reminder_ids as  $reminder_id) {
              DB::table('tblevents')
                ->where('event_id', $reminder_id)
                ->update(['related_event_id' => $event_id,
                          'updated_at' => date('Y-m-d H:i:s')]);  

            }
          }
          if(count($mediaArr) > 0){
            //$values = explode(',',$values);
            foreach ($mediaArr as $value) {
              DB::table('tblevent_media')
                ->insert(['event_id' => $event_id,
                          'file_url' => $value['file_url'],
                          'title' => $value['title'],
                          'icon_link' => $value['icon_link'],
                          'created_at' => date('Y-m-d H:i:s'),
                          'updated_at' => date('Y-m-d H:i:s')]);
            }    
          } 

          if(!empty(@$event_media) && count(@$event_media) > 0){
            foreach ($event_media as $value) {
              DB::table('tblevent_media')
                ->insert(['event_id' => $event_id,
                          'file_url' => $value['file_url'],
                          'title' => $value['title'],
                          'icon_link' => $value['icon_link'],
                          'created_at' => date('Y-m-d H:i:s'),
                          'updated_at' => date('Y-m-d H:i:s')]);
            }  
          }

        $resArr['event_id'] = (string) $event_id;

        if(isset($event_id)){
          if(isset($set_reminder) && $set_reminder == 1){
            DB::table('tblevents')
              ->insert([ 'user_id' => @$user_id,
                         'related_event_id' => @$event_id,
                         'event_type' => "3",
                         'title' => 'Reminder for incomplete event entry',
                         'start_date' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d',strtotime(@$reminder_date_time)) : "",
                         'start_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('H:i',strtotime(@$reminder_date_time)) : "",
                         'repeat_mode' =>  "0",
                         'notification_type' => "0",
                         'notification' => "1",
                         'reminder_type' => "1",
                         'set_reminder' => "1",
                         'reminder_date_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d H:i',strtotime($reminder_date_time)) : "",
                         'last_updated_by' => @$user_id,
                         'created_at' => date('Y-m-d H:i:s'),
                         'updated_at' => date('Y-m-d H:i:s')]);
          }
        }
        $resArr['title'] = (string) $title;  

        if(isset($event_type) && $event_type == "4"){   
          return  $this->api_success($resArr,"Entry completed."); 
        }else{
          return  $this->api_success($resArr,"Event added successfully."); 
        } 

      }else{
      
        //--------------------- UPDATE EVENT --------------------//
        
        $event = $this->get_event_data($event_id);
        $update = [  'event_type' => @$event_type,
                     'related_event_id' => @$related_event_id,
                     'title' => (isset($title) && $title != "")? addslashes($title) : "",
                     'description' =>  (isset($description) && $description != "")? addslashes($description) : "",
                     'category_id' => (isset($category_id) && $category_id != "")? $category_id : "0",
                     'other_category' => (isset($other_category) && $other_category != "")? addslashes($other_category) : "",
                     'tag' =>  (isset($tag) && $tag != "")? $tag : "",
                     'priority' => (isset($priority) && $priority != "")? $priority : "0",
                     'start_date' => (isset($start_date) && $start_date != "")? date('Y-m-d',strtotime($start_date)) : "",
                     'start_time' => (isset($start_time) && $start_time != "")? $start_time : "",
                     'custom_notification' => (isset($event->custom_notification) && $event->custom_notification != "")? json_encode($event->custom_notification) : "",
                     'end_date' => (isset($end_date) && $end_date != "")? date('Y-m-d',strtotime($end_date)) : "",
                     'end_time' => (isset($end_time) && $end_time != "")? $end_time : "",
                     'all_day' => (isset($all_day) && $all_day != "")? $all_day : "",
                     'location' =>  (isset($location) && $location != "") ?$location : "",
                     'latitude' =>  (isset($latitude) && $latitude != "")? $latitude : "",
                     'longitude' =>  (isset($longitude) && $longitude != "")? $longitude : "",
                     'repeat_mode' =>  (isset($repeat_mode) && $repeat_mode != "")? $repeat_mode : "",
                     'custom_repeat' =>  (isset($custom_repeat) && $custom_repeat != "")? json_encode($custom_repeat) : "",
                     'notification_type' => (isset($notification_type) && $notification_type != "")? $notification_type : "",
                     'notification' => (isset($notification) && $notification != "")? $notification : "",
                     'custom_notification' => (isset($custom_notification) && $custom_notification != "")? json_encode($custom_notification) : "",
                     'set_reminder' => (isset($set_reminder) && $set_reminder != "")? $set_reminder : "",
                     'reminder_date_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d',strtotime($reminder_date_time)) : "",
                     'last_updated_by' => @$user_id,
                     'updated_at' => date('Y-m-d H:i:s')];
        
        if(isset($google_event_id) && $google_event_id !="" && $google_event_id !="0"){
          DB::table('tblevents')
            ->where('google_event_id', $google_event_id)
            ->where('user_id', $user_id)
            ->update($update);
          $eventData = $this->getEventDataByGoogleId($google_event_id);  
          $event_id = $eventData[0]->event_id;
        }else{
           DB::table('tblevents')
             ->where('event_id', $event_id)
             ->update($update);
        }
       

        if(count($mediaArr) > 0){
          //$values = explode(',',$values);
          foreach ($mediaArr as $value) {
            DB::table('tblevent_media')
              ->insert(['event_id' => $event_id,
                        'file_url' => $value['file_url'],
                        'title' => $value['title'],
                        'icon_link' => $value['icon_link'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')]);
          }    
        } 
        
        if(!empty(@$event_media) && count(@$event_media) > 0){
          foreach ($event_media as $value) {
            DB::table('tblevent_media')
              ->insert(['event_id' => $event_id,
                        'file_url' => $value['file_url'],
                        'title' => $value['title'],
                        'icon_link' => $value['icon_link'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')]);
          }  
        }

        $resArr['event_id'] = (string) $event_id;
        $resArr['title'] = (string) $title;

        if(isset($event_id)){
          if(isset($set_reminder) && $set_reminder == "1"){
            if($event[0]->set_reminder == '1'){

              DB::table('tblevents')
                ->where('related_event_id', $event_id)
                ->where('reminder_type', '1')
                ->where('user_id', @$user_id)
                ->update([ 'start_date' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d',strtotime(@$reminder_date_time)) : "",
                           'start_time' => (isset($reminder_date_time) && $reminder_date_time != "")? strval(date('H:i',strtotime(@$reminder_date_time))) : "",
                           'last_updated_by' => @$user_id,
                           'reminder_date_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d H:i',strtotime($reminder_date_time)) : "",
                           'updated_at' => strval(date('Y-m-d H:i:s'))]);  
            }else{

              DB::table('tblevents')
                ->insert([ 'user_id' => @$user_id,
                           'related_event_id' => @$event_id,
                           'event_type' => "3",
                           'title' => 'Reminder for incomplete event entry',
                           'start_date' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d',strtotime(@$reminder_date_time)) : "",
                           'start_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('H:i',strtotime(@$reminder_date_time)) : "",
                           'repeat_mode' =>  "0",
                           'notification_type' => "0",
                           'notification' => "1",
                           'reminder_type' => "1",
                           'set_reminder' => "1",
                           'reminder_date_time' => (isset($reminder_date_time) && $reminder_date_time != "")? date('Y-m-d H:i',strtotime($reminder_date_time)) : "",
                           'last_updated_by' => @$user_id,
                           'created_at' => date('Y-m-d H:i:s'),
                           'updated_at' => date('Y-m-d H:i:s')]);
              
            }    
            
          }
        }

        if(isset($event_type) && $event_type == "4"){   
          return  $this->api_success($resArr,"Entry completed."); 
        }else{
          return  $this->api_success($resArr,"Event updated successfully."); 
        } 
      }  
       
     
      //--------------------- API EROOR --------------------//   
      
      return  $this->error_denied("Oops somthing went wrong,please try again after some time."); 

    } 

    function remove_event_media($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['media_id']) || $data['media_id'] == '') {
        return $this->error_invalid('media_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      $resVal =DB::table('tblevent_media')
               ->select(DB::raw('*'))
               ->where('media_id', '=', $media_id)
               ->first();        
     
      if (stripos($resVal->file_url, url('/')) !== false) {
        $path = str_replace(url('/'),"", $resVal->file_url);
        // unlink(public_path()."".$path);
      } 

      DB::table('tblevent_media')
        ->where('media_id', '=', $media_id)
        ->delete();

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      return $this->api_success($resArr, 'Event deleted successfully.');
    }

    function remove_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      // DB::table('tblevents')
      //   ->where('event_id', '=', $event_id)
      //   ->delete();
      $event_ids = explode(',',$event_id);
      if(count($event_ids) > 0){
        foreach ($event_ids as $value) {
          DB::table('tblevents')
            ->where('event_id', $value)
            ->orwhere('google_event_id', $value)
            ->orwhere('related_event_id', $value)
            ->update(['event_status' => '0',
                      'updated_at' => date('Y-m-d H:i:s')]);  

          //--------------------- DELETE EVENT MEDIA --------------------//
            
          DB::table('tblevent_media')
            ->where('event_id', $value)
            ->update(['status' => '0',
                      'updated_at' => date('Y-m-d H:i:s')]);    

          if(count($event_ids) == 1){  
            $eventArr =DB::table('tblevents')
                         ->select(DB::raw('event_type'))
                         ->where('event_id', '=', $value)
                         ->first();
            if(count($eventArr) > 0){                
              $event_type =  $eventArr->event_type;
            }  
          }     

        }    
      }    

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      if(isset($event_type) && $event_type == "4"){
        return $this->api_success($resArr, 'Entry deleted.');
      }else{
        return $this->api_success($resArr, 'Event deleted successfully.');
      }
      
    }

    function upload_media($data,$file){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      extract($data); 
      
      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }   

      //--------------------- ADD MULTIPLE MEDIA --------------------//

      $values= "";
      $set= "";
      $mediaArr = array();
      if(isset($media_count) && $media_count !="0"){
        $i = 1;
        foreach ($file as $value) {
          $filetype = explode('/',$value->getMimeType());
          $extension = @$filetype[1];
          $destinationPath = 'uploads/media/';
          $file_name = $this->file_name().'_pre_uploaded_media.'.$extension;
          $value->move($destinationPath,$file_name);
          // $media = 'uploads/media/'.$file_name;
          // $values.=$media.',';
          $file_url = url('/').'/'.'uploads/media/'.$file_name;
          $media_title = 'media'.$i."_title";
          $media_icon_link = 'media'.$i."_icon_link";
          //$values.=$media.',';
          $mediaArr[$i]['file_url'] = $file_url;
          $mediaArr[$i]['title'] = $data[$media_title];
          $mediaArr[$i]['icon_link'] = $data[$media_icon_link];
          $i++;
        }
        $values=trim($values,',');
      }

      $data =DB::table('tblpreloaded_media')
               ->select(DB::raw('*'))
               ->where('user_id', '=', $user_id)
               ->get();

      if(count($mediaArr) > 0){
          //$values = explode(',',$values);
        foreach ($mediaArr as $value) {
          DB::table('tblpreloaded_media')
            ->insert(['user_id' => $user_id,
                      'media' => $value['file_url'],
                      'title' => $value['title'],
                      'icon_link' => $value['icon_link'],
                      'created_at' => date('Y-m-d H:i:s'),
                      'updated_at' => date('Y-m-d H:i:s')]);
        }    
      }             

      //--------------------- API RESPONSE --------------------//

      $resArr = array();  
      return  $this->api_success($resArr,"Media uploded successfully."); 

    } 

    function remove_preloaded_media($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['preloaded_media_id']) || $data['preloaded_media_id'] == '') {
        return $this->error_invalid('preloaded_media_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      $resVal =DB::table('tblpreloaded_media')
               ->select(DB::raw('*'))
               ->where('preloaded_media_id', '=', $preloaded_media_id)
               ->first();        
     
      if (stripos($resVal->media, url('/')) !== false) {
        $path = str_replace(url('/'),"", $resVal->media);
        // unlink(public_path()."".$path);
      } 

      DB::table('tblpreloaded_media')
        ->where('preloaded_media_id', '=', $preloaded_media_id)
        ->delete();

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      return $this->api_success($resArr, 'Event deleted successfully.');
    }

    function get_preloaded_media($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
           return $this->error_invalid('user_id id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }   

      $resVal =DB::table('tblpreloaded_media')
               ->select(DB::raw('*'))
               ->where('user_id', '=', $user_id)
               ->get();

      //--------------  API RESPONSE   ----------------- //   
               
      // $resArr = array(); 
      // if(count($data) > 0){
      //   $mediaArr = array();
      //   foreach ($data as  $value) {
      //     $media = explode(',',$value->media);
      //     $media =array_filter($media);
      //     if(count($media) > 0){
      //       foreach ($media as $value) {
      //         $mediaArr[] = url('/').'/'.$value;
      //       }
      //     } 
      //   }  
      // }
    
      // $resArr['media'] = (!empty($mediaArr)) ? $mediaArr : array();

      $resArr = array(); 
      if(count($resVal) > 0){
        $i = 0;
        foreach ($resVal as $value) {
          $resArr[$i]['preloaded_media_id'] = (string) $value->preloaded_media_id;
          $resArr[$i]['file_url'] = $value->media;
          $resArr[$i]['title'] = $value->title;
          $resArr[$i]['icon_link'] = $value->icon_link;
          $i++;
        }
      }      


      return  $this->api_success($resArr,"Media retrived."); 

    } 

    function add_duplicate_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      extract($data); 
      
      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }   

      $event =DB::table('tblevents')
                 ->select(DB::raw('*'))
                 ->where('event_id', '=', $event_id)
                 ->first();
         
      $resArr = array();  
      if(count($event) > 0){

        //--------------------- ADD EVENT --------------------//
          
        DB::table('tblevents')
          ->insert([ 'user_id' => @$user_id,
                     'event_type' => @$event->event_type,
                     'title' => (isset($event->title) && $event->title != "")? addslashes($event->title) : "",
                     'description' => (isset($event->description) && $event->description != "")? addslashes($event->description) : "",
                     'media' => (isset($event->media) && $event->media != "")? addslashes($event->media) : "",
                     'category_id' => (isset($event->category_id) && $event->category_id != "")? $event->category_id : "0",
                     'other_category' => (isset($event->other_category) && $event->other_category != "")? addslashes($event->other_category) : "",
                     'tag' =>  (isset($event->tag) && $event->tag != "")? $event->tag : "",
                     'priority' => (isset($event->priority) && $event->priority != "")? $event->priority : "0",
                     'start_date' => (isset($event->start_date) && $event->start_date != "")? date('Y-m-d',strtotime($event->start_date)) : "",
                     'start_time' => (isset($event->start_time) && $event->start_time != "")? $event->start_time : "",
                     'end_date' => (isset($event->end_date) && $event->end_date != "")? date('Y-m-d',strtotime($event->end_date)) : "",
                     'end_time' => (isset($event->end_time) && $event->end_time != "")? $event->end_time : "",
                     'all_day' => (isset($event->all_day) && $event->all_day != "")? $event->all_day : "",
                     'location' =>  (isset($event->location) && $event->location != "") ? $event->location : "",
                     'latitude' =>  (isset($event->latitude) && $event->latitude != "")? $event->latitude : "",
                     'longitude' =>  (isset($event->longitude) && $event->longitude != "")? $event->longitude : "",
                     'repeat_mode' =>  (isset($event->repeat_mode) && $event->repeat_mode != "")? $event->repeat_mode : "",
                     'custom_repeat' =>  (isset($event->custom_repeat) && $event->custom_repeat != "")? json_encode($event->custom_repeat) : "",
                     'notification_type' => (isset($event->notification_type) && $event->notification_type != "")? $event->notification_type : "",
                     'notification' => (isset($event->notification) && $event->notification != "")? $event->notification : "",
                     'custom_notification' => (isset($event->custom_notification) && $event->custom_notification != "")? json_encode($event->custom_notification) : "",
                     'created_at' => date('Y-m-d H:i:s'),
                     'updated_at' => date('Y-m-d H:i:s')]);
        return  $this->api_success($resArr,"Event added successfully.");   
      }else{
        return  $this->error_denied($resArr,"Event not available.");   
      }
    }

    function search_user($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        // return $this->error_invalid('user_id');
      }
      extract($data);
      
      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  

      //--------------- SEARCH USER --------------------//
      
      $resVal =DB::table('tblusers')
                 ->select(DB::raw('*'))
                 ->where('user_id', '<>', $user_id)
                 ->where(function($query) use($search_text)
                  {
                    $query ->orwhere('name', 'LIKE', '%'.$search_text.'%')
                           ->orwhere('email_id', 'LIKE', '%'.$search_text.'%');
                  })
                ->groupBy('user_id')
                ->get(); 
      $resArr = array();
      if(count($resVal) > 0){
        $i = 0;
        foreach ($resVal as $value) {
          $resArr[$i]['user_id'] = $value->user_id;
          $resArr[$i]['email_id'] = $value->email_id;
          $resArr[$i]['name'] = (isset($value->name) && $value->name != "") ? $value->name : ""; 
          $resArr[$i]['profile_pic'] = (isset($value->profile_pic) && $value->profile_pic != "") ? url('/').'/'.$value->profile_pic : "";
          $res = DB::table('tblevent_group')
                   ->select(DB::raw('*'))
                   ->where('invited_to', '=', $value->user_id)
                   ->where('event_id', '=', $event_id)
                   ->get();
          if(isset($event_id) && $event_id != "" && count($res) > 0){
            $resArr[$i]['is_member'] = "1";
          }else{
            $resArr[$i]['is_member'] = "0";
          }
          $i++;
        }  
      }  
      return $this->api_success($resArr, 'User retrived.');
    } 

    function share_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      // if (!isset($data['share_to_id']) || $data['share_to_id'] == '') {
      //   return $this->error_invalid('share_to_id');
      // }
      if (!isset($data['share_to_email']) || $data['share_to_email'] == '') {
        return $this->error_invalid('share_to_email');
      }
      if (!isset($data['start_date_time']) || $data['start_date_time'] == '') {
        return $this->error_invalid('start_date_time');
      }
      if (!isset($data['end_date_time']) || $data['end_date_time'] == '') {
        return $this->error_invalid('end_date_time');
      }
      if (!isset($data['type']) || $data['type'] == '') {
        return $this->error_invalid('type');
      }
      extract($data);
      
      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }  


      $share_to_email = explode(',', $share_to_email);

      foreach ($share_to_email as $value) {
  
        $to_user = DB::table('tblusers')
                    ->select(DB::raw('*'))
                    ->where('email_id', '=', $value)
                    ->orwhere('user_id', '=', $value)
                    ->get();     
        if(count($to_user) > 0 ){

          //--------------- SENT EMAIL --------------------//
          
          $key = $to_user[0]->user_id."&".$event_id."&".$start_date_time."&".$end_date_time;
          $from = $user[0]->name;
          $url = url('/') . "/event/" . urlencode(base64_encode($key));
          $html = '<html>
                    <body>
                      <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                        <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                          <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Shared Event</span><br/>
                          Hello <span style="font-weight: 900;color: #00509e;text-transform: capitalize;"> '.$to_user[0]->name.'</span>, '.$from.' has shared an event with you. Please click the link below to view the event.</br><a href='.@$url.' style="font-weight: 900;color: #11509e;"> (View)</a></span>
                          <br/> Thank you,
                          <br/> <span style="color:#ffffff;font-weight: 900;">MY APP</span>
                      </p>
                    </body>
                  </html>'; 

          if($host = request()->getHttpHost() != "localhost:8000"){  
            $mail = new PHPMailer(true);
            $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
            $mail->addAddress($to_user[0]->email_id, '');
            $mail->Subject  = "MY APP Share Event";
            $mail->MsgHTML($html);
            $mail->IsHTML(true);
            $mail->send(); 
          }  

          $resArr = array();
          $resArr['url'] = $url;
          if(isset($type) && $type == "0" ){
            return $this->api_success($resArr, 'The event is shared with '.$to_user[0]->name.' successfully.');
          }  
        }else{

          //--------------- SENT INVITATION EMAIL --------------------//
         
          $from = $user[0]->name;
          $html = '<html>
                    <body>
                      <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                        <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                              <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Invitation to Collaborate</span><br/>
                          Hello <span style="font-weight: 900;color: #00509e;text-transform: capitalize;"> '.$share_to_email.'</span>,'.$from.' invited you to join MY APP. by the joining MY APP you can create events and share event with your friends and also get shared event by your friends.</span>
                          <br/> Thank you
                          <br/> <span style="color:#ffffff;font-weight: 900;">MY APP</span>
                      </p>
                    </body>
                  </html>'; 

          if($host = request()->getHttpHost() != "localhost:8000"){  
            $mail = new PHPMailer(true);
            $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
            $mail->addAddress($share_to_email, '');
            $mail->Subject  = "MY APP : Invitation to join";
            $mail->MsgHTML($html);
            $mail->IsHTML(true);
            $mail->send(); 
          }  

          $resArr = array();
          if(isset($type) && $type == "0" ){
            return $this->api_success($resArr, $share_to_email.' is invited by you.');
          }
        }
      } 

      if(isset($type) && $type == "1" ){
        $resArr = array();
        return $this->api_success((object) $resArr,'Event shared successfully.');
      } 
    } 

    function list_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
          return $this->error_invalid('user_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//
     
      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      }
      
      //--------------------- GET EVENT DATA --------------------//

      $event_type = '4';
      $resValOne = DB::table('tblevents As e')
                     ->select(DB::raw('e.*,c.category_name'))
                     ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                     ->where('e.user_id', '=', $user_id)
                     ->where(function($query)
                      {

                        $query ->orwhere('e.event_type', '=', 2)
                               ->orwhere('e.event_type', '=', 4);
                      })
                     ->where('e.event_status', '=','1')
                     ->orderBy('e.start_date', 'desc')
                     ->get();

      $resValtwo = DB::table('tblevent_group As eg')
                     ->select(DB::raw('e.*,c.category_name'))
                     ->Join('tblevents AS e', 'e.event_id', '=', 'eg.event_id')
                     ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                     ->where('eg.invited_to', '=', $user_id)
                     ->where(function($query)
                      {

                        $query ->orwhere('e.event_type', '=', 2)
                               ->orwhere('e.event_type', '=', 4);
                      })
                     ->where('e.event_status', '=','1')
                     ->orderBy('e.start_date','desc')
                     ->get();           
      $resVal = array_merge($resValOne, $resValtwo);

      $finelArr = array();
      if(count($resVal) > 0){
        $i=0;
        foreach ($resVal as  $value) {
          $finelArr[$i]['event_id'] = (string) $value->event_id;
          $finelArr[$i]['title'] =  stripslashes($value->title);
          $finelArr[$i]['event_type'] = (string) $value->event_type;
          $i++;
        }
      }  
   
      return $this->api_success($finelArr, 'Event retrive successfully.');
    } 

    function get_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        if(!isset($data['notification']) && $data['notification'] !="1"){
          return $this->error_invalid('user_id');
        }  
      }
      if (!isset($data['event_type']) || $data['event_type'] == '') {
        if(!isset($data['notification']) && $data['notification'] !="1"){
          return $this->error_invalid('event_type');
        }  
      }
      if (!isset($data['start_date']) || $data['start_date'] == '') {
        if (isset($data['event_view']) && $data['event_view'] == '1') {
          return $this->error_invalid('start_date');
        }  
      }
      if (!isset($data['end_date']) || $data['end_date'] == '') {
        if (isset($data['event_view']) && $data['event_view'] == '1') {
          return $this->error_invalid('end_date');
        } 
      }
      if (!isset($data['event_view']) || $data['event_view'] == '') {
        if(!isset($data['notification']) && $data['notification'] !="1"){
          return $this->error_invalid('event_view');
        }  
      }
      if (!isset($data['user_current_datetime']) || $data['user_current_datetime'] == '') {
        return $this->error_invalid('user_current_datetime');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//
      if(!isset($notification) || $notification !="1"){
        $user = $this->getUserData($user_id);
        if(count($user) == 0 ){
          return  $this->error_denied("User not available."); 
        }
      }   
      
      //--------------------- GET EVENT DATA --------------------//

      if($event_view == "1"){

        //--------------------- GET (REPEAT) EVENT CALENDER --------------------//

        $resValOne = DB::table('tblevents As e')
                       ->select(DB::raw('e.*,c.category_name'))
                       ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                       ->where('e.user_id', '=', $user_id)
                       ->where('e.event_status', '=','1')
                       // ->where('e.start_date', '>=', date("Y-m-d",strtotime($start_date)))
                       // ->where('e.end_date', '<=', date("Y-m-d",strtotime($end_date)))
                       ->orderBy('e.start_date', 'asc')
                       ->get();

        $resValtwo =  DB::table('tblevent_group As eg')
                         ->select(DB::raw('e.*,c.category_name'))
                         ->Join('tblevents AS e', 'e.event_id', '=', 'eg.event_id')
                         ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                         ->where('eg.invited_to', '=', $user_id)
                         ->where('e.event_status', '=','1')
                         // ->where('e.start_date', '>=', date("Y-m-d",strtotime($start_date)))
                         // ->where('e.start_date', '<=', date("Y-m-d",strtotime($end_date)))
                         ->orderBy('e.start_date', 'asc')
                         ->get();           
        $resVal = array_merge($resValOne, $resValtwo);           

      }else if($event_view == "0"){

        //--------------------- GET EVENT LIST BY TYPE --------------------//
        $order = "asc";
        if($event_type == "1"){
          $order = "desc";
        }

        $resValOne = DB::table('tblevents As e')
                       ->select(DB::raw('e.*,c.category_name'))
                       ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                       ->where('e.user_id', '=', $user_id)
                       ->where('e.event_type', '=', $event_type)
                       ->where('e.is_completed', '=', '0')
                       ->where('e.event_status', '=','1')
                       ->orderBy('e.start_date', $order)
                       ->get();

        $resValtwo = DB::table('tblevent_group As eg')
                       ->select(DB::raw('e.*,c.category_name'))
                       ->Join('tblevents AS e', 'e.event_id', '=', 'eg.event_id')
                       ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                       ->where('eg.invited_to', '=', $user_id)
                       ->where('e.is_completed', '=', '0')
                       ->where('e.event_type', '=', $event_type)
                       ->where('e.event_status', '=','1')
                       ->orderBy('e.start_date', $order)
                       ->get();           
        $resVal = array_merge($resValOne, $resValtwo);
                     
      }else if($event_view == "-1"){

        //--------------------- GET EVENT FOR NOTIFICATION --------------------//

        $resValOne = DB::table('tblevents As e')
                       ->select(DB::raw('e.*,c.category_name'))
                       ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                       ->where('e.event_type', '<>', 4)
                       ->where('e.notification', '<>', 0)
                       ->where('e.notification', '<>', '-1')
                       ->where('e.event_status', '=','1')
                      ->where('e.end_date', '<>', '0000-00-00')
                       ->orderBy('e.start_date', 'asc')
                       //->where('e.user_id', '=', 1)
                       ->get();

        $resValtwo = DB::table('tblevent_group As eg')
                       ->select(DB::raw('e.*,c.category_name'))
                       ->Join('tblevents AS e', 'e.event_id', '=', 'eg.event_id')
                       ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                       ->where('e.event_type', '<>', 4)
                       ->where('e.notification', '<>', 0)
                       ->where('e.notification', '<>', '-1')
                       ->where('e.event_status', '=','1')
                       ->orderBy('e.start_date', 'asc')
                       ->get();           
        $resVal = array_merge($resValOne, $resValtwo);
                  
      }else if($event_view == "-2"){

        //--------------------- GET EVENT FOR REMIANDER --------------------//

        $resVal =DB::table('tblevents')
                   ->select(DB::raw('*'))
                   ->where('set_reminder', '=', 1)
                   // ->where('is_reminder_sent', '<>', 1)
                   ->where('event_status', '=','1')
                   ->orderBy('start_date', 'asc')
                   ->get();        
      }
     
      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      $normal_event = array();
      $repeat_every_day = array();
      $repeat_every_week = array();

      if(count($resVal) > 0){
        $start_date = @$start_date.' 00:00:00';
        $end_date = @$end_date.' 23:59:59';
        $repeat_event = new repeat_event_model;
        foreach ($resVal as $value) {
          $date_diff = "";
          //--------------------- REPEATE EVENT  --------------------//
          
          if($event_view != "0" && $event_view != "-2" && $value->repeat_mode == "1"){

            //--------------------- ALL DAY / EVERY DAY REPEATE EVENT  --------------------//

            $difference = $repeat_event->date_difference($value->start_date,$end_date,1);
            $resArr[] = $repeat_event->repeat_every_day($value,$difference,$start_date,$end_date,$user_current_datetime);

          }else if($event_view != "0" && $event_view != "-2" && ($value->repeat_mode == "2")){
            
            //--------------------- EVERY WEEK REPEATE EVENT  --------------------//
            
            $difference = $repeat_event->date_difference($value->start_date,$end_date,2);
            $resArr[] = $repeat_event->repeat_every_week($value,$difference,$start_date,$end_date,$user_current_datetime);

          }else if($event_view != "0" && $event_view != "-2" && ($value->repeat_mode == "3")){

            //--------------------- EVERY MONTH REPEATE EVENT  --------------------//
            
            $difference = $repeat_event->date_difference($value->start_date,$end_date,3);
            $resArr[] = $repeat_event->repeat_every_month($value,$difference,$start_date,$end_date,$user_current_datetime);

          }else if($event_view != "0" && $event_view != "-2" && ($value->repeat_mode == "4")){

            //--------------------- EVERY YEAR REPEATE EVENT  --------------------//
            
            $difference = $repeat_event->date_difference($value->start_date,$end_date,4);
            $resArr[] = $repeat_event->repeat_every_year($value,$difference,$start_date,$end_date,$user_current_datetime);

          }else if($event_view != "0" && $event_view != "-2" && ($value->repeat_mode == "5")){

            //--------------------- CUSTOM REPEATE EVENT  --------------------//
            
            $resArr[] = $repeat_event->custom_repeat($value,$start_date,$end_date,$user_current_datetime);

          }else if($event_view == "-2" || $event_view == "-1"){

            //--------------------- REMINDER EVENT  --------------------//
            
            $resArr[] = $repeat_event->reminder_event($value);

          }else {
            //--------------------- NORMAL EVENT  --------------------//
            
            $resArr[] = $repeat_event->normal_event($value,$event_view,$start_date,$end_date,$user_current_datetime);

          }
        }
      }  

      $i=0;
      $finelArr = array();
      foreach ($resArr as  $val) {
        if(is_array($val)){
          foreach ($val as  $value) {
            $finelArr[$i]['event_id'] = (string) $value['event_id'];
            $event = $this->get_event_data($value['event_id']);
            $relatedEvent = $this->get_event_data($event[0]->related_event_id);
            $finelArr[$i]['related_event_id'] = (!empty($relatedEvent) && $event[0]->related_event_id != "" && $event[0]->related_event_id != "0")? $event[0]->related_event_id : "";
            $finelArr[$i]['related_event_title'] = (!empty($relatedEvent) && $relatedEvent[0]->title != "" && $event[0]->related_event_id != "" && $event[0]->related_event_id != "0")? $relatedEvent[0]->title : "";
            $finelArr[$i]['related_event_type'] = (!empty($relatedEvent) && $relatedEvent[0]->event_type != "" && $event[0]->related_event_id != "" && $event[0]->related_event_id != "0")? $relatedEvent[0]->event_type : "";
            $finelArr[$i]['event_type'] = (string) $value['event_type'];
            $finelArr[$i]['is_completed'] = (string) $value['is_completed'];
            $finelArr[$i]['user_id'] = (string) $value['user_id'];
            $finelArr[$i]['title'] =  stripslashes($value['title']);
            $finelArr[$i]['all_day'] =  stripslashes($value['all_day']);
            $finelArr[$i]['start_date'] =  (isset($value['start_date']) && $value['start_date'] != "0000-00-00")? $value['start_date'] : "";
            $finelArr[$i]['start_time'] =  date("H:i", strtotime($value['start_time']));
            $finelArr[$i]['start_date_time'] =  (isset($value['start_date']) && $value['start_date'] != "0000-00-00")? strtotime($value['start_date'].' '.date("H:i", strtotime($value['start_time']))) : "";
            $finelArr[$i]['end_date'] =  (isset($value['end_date']) && $value['end_date'] != "0000-00-00")? $value['end_date'] : "";
            $finelArr[$i]['end_time'] =  date("H:i", strtotime($value['end_time']));
            $finelArr[$i]['repeat_mode'] =  (string) $value['repeat_mode'];
            $finelArr[$i]['created_at'] =  $value['created_at'];
            $finelArr[$i]['updated_at'] =  $value['updated_at'];
            $finelArr[$i]['set_reminder'] =  $value['set_reminder'];
            $finelArr[$i]['reminder_date_time'] =  $value['reminder_date_time'];
            $finelArr[$i]['priority'] =  $event[0]->priority;
            $finelArr[$i]['category_id'] =  $event[0]->category_id;
            if(isset($event[0]->other_category) && $event[0]->other_category !=""){
              $finelArr[$i]['category_name'] =  (isset($event[0]->other_category) && $event[0]->other_category != "")? stripslashes($event[0]->other_category) : ""; 
            }else{
              $finelArr[$i]['category_name'] =  (isset($event[0]->category_name) && $event[0]->category_name != "")? $event[0]->category_name : ""; 
            }
            if($event_view == "-1" || $event_view == "-2"){
              $finelArr[$i]['notification_type'] =  $value['notification_type'];
              $finelArr[$i]['notification'] =  $value['notification'];
              $finelArr[$i]['custom_notification'] =  $value['custom_notification'];
              $finelArr[$i]['location'] =  (isset($value['location']) && $value['location'] != "")? $value['location'] : "";
            }

            $reminderVal =DB::table('tblevents As e')
                             ->select(DB::raw('e.*,c.category_name'))
                             ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                             ->where('e.related_event_id', '=', $value['event_id'])
                             ->where('e.event_status', '=', '1')
                             ->get();
            $finelArr[$i]['reminder_count'] =  (!empty($reminderVal))? count($reminderVal) : "0";

            $i++;
          }  
        }
      }

      if(isset($event_view) && $event_view  == 0 && isset($event_type) && ($event_type  == 1 || $event_type  == 3 || $event_type  == 4)){
        $finelArr =$this->msort($finelArr,"start_date_time", 1); //DSENDING ORDER
      }else {
        $finelArr =$this->msort($finelArr,"start_date_time", 2); //ASCNDING ORDER
      }
      return $this->api_success($finelArr, 'Event retrive successfully.');
    } 

    function search_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['search_text']) || $data['search_text'] == '') {
        //return $this->error_invalid('search_text');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 
        
      //--------------------- GET EVENT DATA --------------------//
     
      $resVal =DB::table('tblevents As e')
                 ->select(DB::raw('e.*,c.category_name'))
                 ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                 ->where('e.user_id', '=', $user_id)
                 ->where('e.event_status', '=', '1')
                 ->where('e.is_completed', '=', '0')
                 ->where(function($query) use($search_text)
                  {
                    $query ->orwhere('e.title', 'LIKE', '%'.$search_text.'%')
                           // ->orwhere('e.description', 'LIKE', '%'.$search_text.'%')
                           ->orwhere('e.tag', 'LIKE', '%'.$search_text.'%')
                           ->orwhere('c.category_name', 'LIKE', '%'.$search_text.'%')
                           // ->orwhere('e.start_date', 'LIKE', '%'.$search_text.'%')
                           // ->orwhere('e.start_time', 'LIKE', '%'.$search_text.'%')
                           // ->orwhere('e.end_date', 'LIKE', '%'.$search_text.'%')
                           // ->orwhere('e.end_time', 'LIKE', '%'.$search_text.'%')
                           ->orwhere('e.location', 'LIKE', '%'.$search_text.'%');
                  })
                ->get();

      //--------------------- API RESPONSE --------------------//

      $i=0;
      $resArr = array();
      foreach ($resVal as  $value) {
        $resArr[$i]['event_id'] = (string) $value->event_id;
        $resArr[$i]['event_type'] = (string) $value->event_type;
        $resArr[$i]['is_completed'] = (string) $value->is_completed;
        $resArr[$i]['user_id'] = (string) $value->user_id;
        $resArr[$i]['title'] =  stripslashes($value->title);
        $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($value->start_date));
        $resArr[$i]['start_time'] =  date("H:i", strtotime($value->start_time));
        $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($value->end_date));
        $resArr[$i]['end_time'] =  date("H:i", strtotime($value->end_time));
        $resArr[$i]['repeat_mode'] =  $value->repeat_mode;
        $resArr[$i]['created_at'] =  $value->created_at;
        $resArr[$i]['updated_at'] =  $value->updated_at;
        $resArr[$i]['set_reminder'] =  $value->set_reminder;
        $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;
        $i++;
      }
      return $this->api_success($resArr, 'Event retrive successfully.');

    } 

    function get_event_by_id($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      $resVal =DB::table('tblevents As e')
                 ->select(DB::raw('e.*,c.category_name'))
                 ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                 ->where('e.event_id', '=', $event_id)
                 ->get();

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      if(count($resVal) > 0){
        $i = 0;

        $member =DB::table('tblevent_group')
                   ->select(DB::raw('count(*) AS count'))
                   ->where('event_id', '=', $event_id)
                   ->first();    
                           
        foreach ($resVal as $value) {

          //--------------------- GET MEDIA --------------------//

          $media =DB::table('tblevent_media')
                     ->select(DB::raw('*'))
                     ->where('event_id', '=', $event_id)
                     ->get();    

          $mediaArr = array();
          if(count($media) > 0){
            $z = 0;
            foreach ($media as $val) {
              $mediaArr[$z]['media_id'] = $val->media_id;
              $mediaArr[$z]['file_url'] = $val->file_url;
              $mediaArr[$z]['title'] = $val->title;
              $mediaArr[$z]['icon_link'] = $val->icon_link;
              $z++;
            }
          }      
         
          $resArr[$i]['event_id'] = (string) $value->event_id;
          $relatedEvent = $this->get_event_data($value->related_event_id);
          $resArr[$i]['related_event_id'] = (!empty($relatedEvent) && $value->related_event_id != "" && $value->related_event_id != "0")? $value->related_event_id : "";
          $resArr[$i]['related_event_title'] = (!empty($relatedEvent) && $relatedEvent[0]->title != "" && $value->related_event_id != "" && $value->related_event_id != "0")? $relatedEvent[0]->title : "";
          $resArr[$i]['related_event_type'] = (!empty($relatedEvent) && $relatedEvent[0]->event_type != "" && $value->related_event_id != "" && $value->related_event_id != "0")? $relatedEvent[0]->event_type : "";
          $resArr[$i]['google_event_id'] = (string) $value->google_event_id;
          $resArr[$i]['user_id'] =  (string) $value->user_id;
          $resArr[$i]['event_type'] = (string) $value->event_type;
          $resArr[$i]['title'] =  stripslashes($value->title);
          $resArr[$i]['description'] =  $value->description;
          $resArr[$i]['media'] =  (!empty($mediaArr)) ? $mediaArr : array();;
          $resArr[$i]['category_id'] =  (string) $value->category_id;

          if(isset($value->other_category) && $value->other_category !=""){
            $resArr[$i]['category_name'] =  (isset($value->other_category) && $value->other_category != "")? stripslashes($value->other_category) : ""; 
          }else{
            $resArr[$i]['category_name'] =  (isset($value->category_name) && $value->category_name != "")? $value->category_name : ""; 
          }

          $resArr[$i]['tag'] =  $value->tag;
          $resArr[$i]['priority'] =  (string) $value->priority;
          $resArr[$i]['start_date'] =  (isset($value->start_date) && $value->start_date != "0000-00-00")? date('d-m-Y',strtotime($value->start_date)) : "";
          $resArr[$i]['start_time'] =  date("H:i", strtotime($value->start_time));
          $resArr[$i]['end_date'] =  (isset($value->end_date) && $value->end_date != "0000-00-00")? date('d-m-Y',strtotime($value->end_date)) : "";
          $resArr[$i]['end_time'] =  date("H:i", strtotime($value->end_time));
          $resArr[$i]['all_day'] =  (string) $value->all_day;
          $resArr[$i]['location'] =  $value->location;
          $resArr[$i]['latitude'] =  $value->latitude;
          $resArr[$i]['longitude'] =  $value->longitude;
          $resArr[$i]['repeat_mode'] =  (string) $value->repeat_mode;
          $resArr[$i]['custom_repeat'] =  (isset($value->custom_repeat) && $value->custom_repeat != "")? json_decode($value->custom_repeat,true) : array();
          $resArr[$i]['notification_type'] = (string) $value->notification_type;
          $resArr[$i]['notification'] =  (string) $value->notification;
          $resArr[$i]['custom_notification'] = (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();
          $resArr[$i]['is_completed'] = (string) $value->is_completed;
          $resArr[$i]['completion_date'] =  $value->completion_date;
          $resArr[$i]['created_at'] =  $value->created_at;
          $resArr[$i]['updated_at'] =  $value->updated_at;
          $resArr[$i]['set_reminder'] =  (string) $value->set_reminder;
          $resArr[$i]['member_count'] = (string) $member->count;
          $resArr[$i]['created_by_id'] = (string) $value->user_id;
          $user = $this->getUserData($value->user_id);
          $created_by = (isset($user[0]->name) && $user[0]->name !="")? $user[0]->name : "";
          $resArr[$i]['created_by_name'] = (string) $created_by;
          $resArr[$i]['last_updated_by_id'] = (isset($value->last_updated_by) && $value->last_updated_by !="0" )? (string) $value->last_updated_by : "";
          $user = $this ->getUserData($value->last_updated_by);
          $last_updated_by = (isset($user[0]->name) && $user[0]->name !="")? $user[0]->name : "";
          $resArr[$i]['last_updated_by_name'] = (string) $last_updated_by;

          $reminderVal =DB::table('tblevents As e')
                         ->select(DB::raw('e.*,c.category_name'))
                         ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                         ->where('e.related_event_id', '=', $event_id)
                         ->where('e.event_status', '=', '1')
                         ->get();

          $reminderArr = array();
          if(count($reminderVal) > 0){
            $j = 0;
            foreach ($reminderVal as $val) {
              $reminderArr[$j]['event_id'] = (string) $val->event_id;
              $reminderArr[$j]['title'] = (string) $val->title;
              $j++;
            }  
          }               
          $resArr[$i]['reminders'] = $reminderArr;
          $i++;
        }
      }  
      return $this->api_success($resArr, 'Event retrive successfully.');
    }

    function get_event_member($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      $resVal =DB::table('tblevent_group As eg')
                 ->select(DB::raw('*'))
                 ->join('tblusers AS u', 'u.user_id', '=', 'eg.invited_to')
                 ->where('eg.event_id', '=', $event_id)
                 ->groupBy('eg.invited_to')
                 ->get();

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      $i = 0;
      if(count($resVal) > 0){
        foreach ($resVal as  $value) {
          $resArr[$i]['user_id'] = (string) $value->user_id;
          $resArr[$i]['name'] = (isset($value->name) && $value->name != "") ? $value->name : ""; 
          $resArr[$i]['profile_pic'] = (isset($value->profile_pic) && $value->profile_pic != "") ? url('/').'/'.$value->profile_pic : "";
          $i++;
        } 
      } 
      $resArr[$i]['user_id'] = (string) $user[0]->user_id;
      $resArr[$i]['name'] = (isset($user[0]->name) && $user[0]->name != "") ? $user[0]->name : ""; 
      $resArr[$i]['profile_pic'] = (isset($user[0]->profile_pic) && $user[0]->profile_pic != "") ? url('/').'/'.$user[0]->profile_pic : "";
      $resArr = array_map("unserialize", array_unique(array_map("serialize", $resArr)));
      return $this->api_success($resArr, 'Event retrive successfully.');
    }

    function add_to_event_group($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      if (!isset($data['invited_to']) || $data['invited_to'] == '') {
        return $this->error_invalid('invited_to');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      $to_user = $this->getUserData($invited_to);

      $resVal =DB::table('tblevent_group')
                 ->select(DB::raw('*'))
                 ->where('created_by', '=', $user_id)
                 ->where('invited_to', '=', $invited_to)
                 ->where('event_id', '=', $event_id)
                 ->get();

      if(count($resVal) > 0 ){
        return  $this->error_denied("User already in the event group."); 
      }  

      $event = $this->get_event_data($event_id); 

      if(count($event) == 0){
        return  $this->error_denied("Event not available, please refresh the event list."); 
      }

      $start_date_time = date('d-m-Y H:i',strtotime($event[0]->start_date.' '.$event[0]->start_time));
      $end_date_time = date('d-m-Y H:i',strtotime($event[0]->end_date.' '.$event[0]->end_time));
      $key = $invited_to."&".$event_id."&".$start_date_time."&".$end_date_time;
      $from = $user[0]->name;
      $url = url('/') . "/event/" . urlencode(base64_encode($key));
      $html = '<html>
                <body>
                  <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">
                    <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">
                      <span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Shared Event</span><br/>
                      Hello <span style="font-weight: 900;color: #00509e;text-transform: capitalize;"> '.$to_user[0]->name.'</span>, '.$from.' has shared an event with you. Please click the link below to view the event.</br><a href='.@$url.' style="font-weight: 900;color: #11509e;"> (View)</a></span>
                      <br/> Thank you,
                      <br/> <span style="color:#ffffff;font-weight: 900;">MY APP</span>
                  </p>
                </body>
              </html>'; 

      if($host = request()->getHttpHost() != "localhost:8000"){  
        $mail = new PHPMailer(true);
        $mail->setFrom('admin@YOURSERVER.com', 'MY APP');
        $mail->addAddress($to_user[0]->email_id, '');
        $mail->Subject  = "MY APP Share Event";
        $mail->MsgHTML($html);
        $mail->IsHTML(true);
        $mail->send(); 
      }  

        

      DB::table('tblevent_group')
        ->insert(['event_id' => $event_id,
                  'created_by' => $user_id,
                  'invited_to' => $invited_to,
                  'created_at' => date('Y-m-d H:i:s'),
                  'updated_at' => date('Y-m-d H:i:s')]);

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      $resArr['url'] = $url;
      return $this->api_success($resArr, 'User added in the group.');
    }

    function remove_from_event_group($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      if (!isset($data['remove_to']) || $data['remove_to'] == '') {
        return $this->error_invalid('remove_to');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      DB::table('tblevent_group')
        ->where('event_id', '=', $event_id)
        ->where('invited_to', '=', $remove_to)
        ->delete();

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      return $this->api_success($resArr, 'User removed from group.');
    }

    function leave_event_group($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 

      DB::table('tblevent_group')
        ->where('event_id', '=', $event_id)
        ->where('invited_to', '=', $user_id)
        ->delete();

      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      return $this->api_success($resArr, 'You left the group.');
    }


    function complete_to_do($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      if (!isset($data['completion_date']) || $data['completion_date'] == '') {
        return $this->error_invalid('completion_date');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 
      
      DB::table('tblevents')
        ->where('event_id', $event_id)
        ->update(['completion_date' => date('Y-m-d H:i:s',strtotime($completion_date)),
                  'is_completed' => 1,
                  'updated_at' => date('Y-m-d H:i:s')]);

      $resArr = array();
      return $this->api_success($resArr, 'To-do completed successfully.');

    } 

    function add_comment($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      if (!isset($data['comment']) || $data['comment'] == '') {
        return $this->error_invalid('comment');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 
      
      DB::table('tblcomment')
        ->insert(['event_id' => $event_id,
                 'user_id' => $user_id,
                 'comment' => addslashes($comment),
                 'created_at' => date('Y-m-d H:i:s'),
                 'updated_at' => date('Y-m-d H:i:s')]);
      $comment_id = DB::getPdo()->lastInsertId();
      $resVal =DB::table('tblcomment As c')
                 ->select(DB::raw('c.*,u.*,c.created_at AS comment_created_at,c.updated_at AS comment_updated_at'))
                 ->join('tblusers AS u', 'u.user_id', '=', 'c.user_id')
                 ->where('comment_id','=',$comment_id)
                 ->get();
     
      //--------------------- API RESPONSE --------------------//

     
      $i=0;
      $resArr = array();
      foreach ($resVal as  $value) {
        $resArr['event_id'] = (string) $value->event_id;
        $resArr['user_id'] = (string) $value->user_id;
        $resArr['name'] =  $value->name;
        $resArr['profile_pic'] =  url('/').'/'.$value->profile_pic;
        $resArr['comment_id'] = (string) $value->comment_id;
        $resArr['comment'] = (isset($value->comment) && $value->comment != "")? stripslashes($value->comment) : "";
        $resArr['created_at'] =  (isset($value->comment_created_at) && $value->comment_created_at != "0000-00-00")? $value->comment_created_at : "";
        $resArr['updated_at'] =  (isset($value->comment_updated_at) && $value->comment_updated_at != "0000-00-00")? $value->comment_updated_at : "";
        $i++;
      }
      return $this->api_success((object)$resArr, 'Comment added successfully.');

    } 

     function get_comment($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event_id']) || $data['event_id'] == '') {
        return $this->error_invalid('event_id');
      }
      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 
      
      //--------------------- GET COMMENT DATA --------------------//
   
      $resVal =DB::table('tblcomment As c')
                 ->select(DB::raw('c.*,u.*,c.created_at AS comment_created_at,c.updated_at AS comment_updated_at'))
                 ->join('tblusers AS u', 'u.user_id', '=', 'c.user_id')
                 ->where('event_id','=',$event_id)
                 ->get();
     
      //--------------------- API RESPONSE --------------------//
     
      $i=0;
      $resArr = array();
      foreach ($resVal as  $value) {
        $resArr[$i]['event_id'] = (string) $value->event_id;
        $resArr[$i]['user_id'] = (string) $value->user_id;
        $resArr[$i]['name'] =  $value->name;
        $resArr[$i]['profile_pic'] =  url('/').'/'.$value->profile_pic;
        $resArr[$i]['comment_id'] = (string) $value->comment_id;
        $resArr[$i]['comment'] = (isset($value->comment) && $value->comment != "")? stripslashes($value->comment) : "";
        $resArr[$i]['created_at'] =  (isset($value->comment_created_at) && $value->comment_created_at != "0000-00-00")? $value->comment_created_at : "";
        $resArr[$i]['updated_at'] =  (isset($value->comment_updated_at) && $value->comment_updated_at != "0000-00-00")? $value->comment_updated_at : "";
        $i++;
      }
      return $this->api_success($resArr, 'Comment retrive successfully.');

    } 

    function sync_event($data){

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }

      extract($data);

      //--------------------- CHECK USER AVAILABLE --------------------//

      $user = $this->getUserData($user_id);
      if(count($user) == 0 ){
        return  $this->error_denied("User not available."); 
      } 
      $sync_time = $user[0]->sync_time;
      $resVal = DB::table('tblevents As e')
                     ->select(DB::raw('e.*,c.category_name'))
                     ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                     ->where('e.user_id', '=', $user_id)
                     ->where(function($query)use($sync_time)
                      {
                        $query->where('e.created_at', '>', $sync_time)
                              ->orWhere('e.updated_at', '>', $sync_time);
                      })
                     ->groupBy('event_id')
                     ->get();     


      //--------------------- API RESPONSE --------------------//

      $resArr = array();
      $finelArr = array();
      $newEventArr = array();
      $updatedEventArr = array();
      if(count($resVal) > 0){
        $i = 0;

        
                           
        foreach ($resVal as $value) {

          $member =DB::table('tblevent_group')
                   ->select(DB::raw('count(*) AS count'))
                   ->where('event_id', '=',$value->event_id)
                   ->first();    

          //--------------------- GET MEDIA --------------------//

          $media =DB::table('tblevent_media')
                     ->select(DB::raw('*'))
                     ->where('event_id', '=', $value->event_id)
                     ->get();    

          $mediaArr = array();
          if(count($media) > 0){
            $z = 0;
            foreach ($media as $val) {
              $mediaArr[$z]['media_id'] = $val->media_id;
              $mediaArr[$z]['file_url'] = $val->file_url;
              $mediaArr[$z]['title'] = $val->title;
              $mediaArr[$z]['icon_link'] = $val->icon_link;
              $z++;
            }
          }      
         
      
          $resArr[$i]['event_id'] = (string) $value->event_id;
          $resArr[$i]['google_event_id'] = (string) $value->google_event_id;
          $resArr[$i]['user_id'] =  (string) $value->user_id;
          $resArr[$i]['event_type'] = (string) $value->event_type;
          $resArr[$i]['title'] =  stripslashes($value->title);
          $resArr[$i]['description'] =  $value->description;
          $resArr[$i]['media'] =  (!empty($mediaArr)) ? $mediaArr : array();;
          $resArr[$i]['category_id'] =  (string) $value->category_id;

          if(isset($value->other_category) && $value->other_category !=""){
            $resArr[$i]['category_name'] =  (isset($value->other_category) && $value->other_category != "")? stripslashes($value->other_category) : ""; 
          }else{
            $resArr[$i]['category_name'] =  (isset($value->category_name) && $value->category_name != "")? $value->category_name : ""; 
          }

          $resArr[$i]['tag'] =  $value->tag;
          $resArr[$i]['priority'] =  (string) $value->priority;
          $resArr[$i]['start_date'] =  (isset($value->start_date) && $value->start_date != "0000-00-00")? date('d-m-Y',strtotime($value->start_date)) : "";
          $resArr[$i]['start_time'] =  date("H:i", strtotime($value->start_time));
          $resArr[$i]['end_date'] =  (isset($value->end_date) && $value->end_date != "0000-00-00")? date('d-m-Y',strtotime($value->end_date)) : "";
          $resArr[$i]['end_time'] =  date("H:i", strtotime($value->end_time));
          $resArr[$i]['all_day'] =  (string) $value->all_day;
          $resArr[$i]['location'] =  $value->location;
          $resArr[$i]['latitude'] =  $value->latitude;
          $resArr[$i]['longitude'] =  $value->longitude;
          $resArr[$i]['repeat_mode'] =  (string) $value->repeat_mode;
          $resArr[$i]['custom_repeat'] =  (isset($value->custom_repeat) && $value->custom_repeat != "")? json_decode($value->custom_repeat,true) : array();
          $resArr[$i]['notification_type'] = (string) $value->notification_type;
          $resArr[$i]['notification'] =  (string) $value->notification;
          $resArr[$i]['custom_notification'] = (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();
          $resArr[$i]['is_completed'] = (string) $value->is_completed;
          $resArr[$i]['completion_date'] =  $value->completion_date;
          $resArr[$i]['created_at'] =  $value->created_at;
          $resArr[$i]['updated_at'] =  $value->updated_at;
          $resArr[$i]['set_reminder'] =  (string) $value->set_reminder;
          $resArr[$i]['member_count'] = (string) $member->count;
          $resArr[$i]['created_by_id'] = (string) $value->user_id;
          $user = $this->getUserData($value->user_id);
          $created_by = (isset($user[0]->name) && $user[0]->name !="")? $user[0]->name : "";
          $resArr[$i]['created_by_name'] = (string) $created_by;
          $resArr[$i]['last_updated_by_id'] = (isset($value->last_updated_by) && $value->last_updated_by !="0" )? (string) $value->last_updated_by : "";
          $user = $this ->getUserData($value->last_updated_by);
          $last_updated_by = (isset($user[0]->name) && $user[0]->name !="")? $user[0]->name : "";
          $resArr[$i]['last_updated_by_name'] = (string) $last_updated_by;
          $resArr[$i]['event_status'] = (string) $value->event_status;
          if((strtotime($value->created_at)  == strtotime($value->updated_at) && $value->event_status != '0') || ( $value->is_sync != "1")){
            $finelArr['new_event'][] =  $resArr[$i];
          }
          if(strtotime($value->updated_at)  > strtotime($value->created_at) && $value->event_status != '0' && $value->is_sync != "0" ){
            $finelArr['updated_event'][] =  $resArr[$i];
          }
          if(strtotime($value->updated_at)  > strtotime($value->created_at) && $value->event_status == '0' ){
            $finelArr['deleted_event'][] =  $resArr[$i];
          }
          $i++;
        }
      }  

      return $this->api_success((object) $finelArr, 'Event retrive successfully.');

    }

    function updated_google_event_id($data) {

      if (!isset($data['user_id']) || $data['user_id'] == '') {
        return $this->error_invalid('user_id');
      }
      if (!isset($data['event']) || $data['event'] == '') {
        return $this->error_invalid('event');
      }

      extract($data);
      $resArr = array();
      if(count($event) > 0){
        foreach ($event as $value) {
          DB::table('tblevents')
            ->where('event_id', $value['event_id'])
            ->update(['google_event_id' => $value['google_event_id'],
                      'updated_at' => date('Y-m-d H:i:s')]);
        }
      }      
      
      return $this->api_success($resArr, 'Google event id updated.');        
    }

    //-------------- API METHODS END ----------------- //

  //-------------- API AUTHENTICATION METHODS ----------------- //

  function authenticat_api_call($user_id,$access_token,$device_type,$device_id) {
    $access_session = strtotime(date('Y-m-d H:i:s'));
    $data = DB::table('tblapi_session')
              ->select(DB::raw('*'))
              ->where('user_id', '=', $user_id)
              ->where('device_type', '=', $device_type)
              ->where('device_id', '=', $device_id)
              ->where('access_token', '=', $access_token)
              ->where('access_session', '>=', $access_session)
              ->get();
    return $data;
  }

	//--------------- CHECK API ID & SECRET ---------------//

	function authenticat_api_id($api_id,$api_secret) {
	    return $data = DB::table('tblapi_mst')
		                 ->select(DB::raw('*'))
		                 ->where('api_id', '=', $api_id)
		                 ->where('api_secret', '=', $api_secret)
		                 ->get();
	}

	//--------------- GENERAT SECRET ---------------//

	function secret_generator($email_id,$password){
	  $email_id = strrev($email_id);
	  $password = strrev($password);
	  return md5($email_id.$password);
	}

  //-------------- API AUTHENTICATION METHODS END ----------------- //

  //-------------- API RESPONSE METHODS ----------------- //

  function getUserData($user_id) {
    $data = DB::table('tblusers')
                ->select(DB::raw('*'))
                ->where('user_id', '=', $user_id)
                ->where('is_active', '=','1')
                ->get();
    return $data;
  }
  	
 	function api_success($data = NULL,$msg) {
     $output = array();
     $output['flag'] = 'true';
     $output['result'] = 'success';
     $output['msg'] = $msg;
     if (isset($data))
         $output['data'] = $data;
     return json_encode($output);
  }

  function error_denied($msg) {
      return $this->api_error('DENIED', $msg);
  }

  function error_api() {
      return $this->api_error('API_ERROR', 'Bad API user or secret.');
  }

  function error_invalid($fieldname) {
      return $this->api_error('INVALID_INPUT', 'Invalid or missing ' . $fieldname . '.');
  }

  function api_error($declaration, $msg) {
      $output =array(
          'flag' => 'false',
          'result' => 'error',
          'declaration' => $declaration,
          'msg' => $msg
      );
      return json_encode($output);
  }

  function current_datetime() {
      return date("Y-m-d H:i:s");
  }

  //--------------- GENERAT FILE NAME ---------------//

  function file_name() {
    $str = strtoupper(md5(uniqid(rand(),true)));
    $file_name = substr($str,0,8) . '-' .
                 substr($str,8,4) . '-' .
                 substr($str,12,4). '-' .
                 substr($str,16,4). '-' .
                 substr($str,20);
    return $file_name;
  }

  //--------------- GENERAT ACCESS TOKEN ---------------//

  function access_token($length = 50) {
    $characters = '@0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  //-------------- API RESPONSE METHODS END ----------------- //

  //-------------- GET EVENT DATA ----------------- //

   function get_event_data($event_id){
     return $resValOne = DB::table('tblevents As e')
                          ->select(DB::raw('e.*,c.category_name'))
                          ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                          ->where('e.event_id', '=', $event_id)
                          ->get();          
   }
   function getEventDataByGoogleId($google_event_id){
     return $resValOne = DB::table('tblevents As e')
                          ->select(DB::raw('e.*,c.category_name'))
                          ->leftJoin('tblcategory AS c', 'c.category_id', '=', 'e.category_id')
                          ->where('e.google_event_id', '=', $google_event_id)
                          ->get();          
   }

  //-------------- GET EVENT DATA END ----------------- //

  //-------------- SORT EVENTS ----------------- //

  function msort($array, $key,$type, $sort_flags = SORT_REGULAR) {
    if (is_array($array) && count($array) > 0) {
      if (!empty($key)) {
        $mapping = array();
        foreach ($array as $k => $v) {
          $sort_key = '';
          if (!is_array($key)) {
               $sort_key = $v[$key];
          }else{
            foreach ($key as $key_key) {
              $sort_key .= $v[$key_key];
            }
            $sort_flags = SORT_STRING;
          }
          $mapping[$k] = $sort_key;
        }
            
        if($type == 1){
          arsort($mapping, $sort_flags);
        }
        if($type == 2){
          asort($mapping, $sort_flags);
        }
            
        $sorted = array();
        foreach ($mapping as $k => $v) {
          $sorted[] = $array[$k];
        }
        return $sorted;
      }
    }
    return $array;
  } 

  //-------------- SORT EVENTS END ----------------- //


}	

