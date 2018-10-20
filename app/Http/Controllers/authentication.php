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



if($host = request()->getHttpHost() != "localhost:8000"){

  require 'vendor/autoload.php';

}



class authentication extends Controller

{



	//-------------- ACTIVAT ACCOUNT ----------------- //



	function active_user($data = 0){

	    if($data != "0"){

		   $data = base64_decode(urldecode($data));

		   $key = explode('&',$data);

		   $user_id = $key[0];

		   $activtion_key = $key[1];

		   $email_id = $key[2];

		   $resVal =DB::table('tblusers')

			         ->where('email_id', $email_id)

			         ->where('user_id', $user_id)

			         ->where('activtion_key', $activtion_key)

			         ->first();	         

		   if(count($resVal) > 0){

			   	DB::table('tblusers')

		          ->where('user_id', $user_id)

		          ->where('email_id', $email_id)

		          ->update(['activtion_key' => '',

		          			'is_active' => '1',

							'updated_at' => date('Y-m-d H:i:s')]);



		        //--------------------- SEND ACTIVATION SUCCESSFUL MAIL --------------------// 

		        $from = "Eclat App"; 

		        $html = ' <html>

			                  <body>

			                    <p style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;">

			                      <span style="color:#ffffff;width: 100%;text-align: center;font-weight: 900;">

                        			<span style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;">Account activated</span><br/>

			                        Mail Form: <span style="font-weight: 400;color: #ffffff;">'.@$from.' </span>,<br/>

			                        Hello <span style="font-weight: 400;color: #ffffff;text-transform: capitalize;"> '.@$resVal->name.'</span>,<br/>

			                      </span>

			                      <span>Welcome to Eclat App. Your account has been successfully activated. You may now login to Eclat App using your email: '.@$email_id.'</span>

			                        <br/> Thank you

			                        <br/> <span style="color:#ffffff;font-weight: 900;">Eclat App</span>

			                    </p>

			                  </body>

			                </html>'; 



                $mail = new PHPMailer(true);

			    $mail->setFrom('admin@YOURSERVER.COM', 'Eclat App');

			    $mail->addAddress($email_id, '');

			    $mail->Subject  = "Your Eclat App account is now active!";

			    $mail->MsgHTML($html);

			    $mail->IsHTML(true);

			    $mail->send();  



		   	 	return view('active_user', ['validated_link' => 'success']);

		   }else{

		   	 return view('active_user', ['validated_link' => 'false']);

		   }	

	    }else{

	       return view('active_user', ['validated_link' => 'false']);

	    }   

	}



	//-------------- CHANGE PASSWORD ----------------- //



	function change_password($ref_key = 0){



		   if($ref_key != "0"){

			   $ref_key = base64_decode(urldecode($ref_key));

			   $key = explode('&',$ref_key);

			   $email_id = $key[0];

			   $ref_key = $key[1];

			   $data = DB::table('tblusers')

				         ->where('email_id', $email_id)

				         ->where('ref_key', $ref_key)

				         ->first();

		       return view('change_password', ['data' => $data,'validated_link' => 'success']);

		    }else{

		       return view('change_password', ['msg' => 'false']);

		    }   

	}



	function update_password(){

		if(!empty($_POST)){

			extract($_POST);

			$data = DB::table('tblusers')

			         ->where('email_id', $email_id)

			         ->where('user_id', $user_id)

			         ->first();

			if($data->ref_key !=""){ 

		    	DB::table('tblusers')

		          ->where('user_id', $user_id)

		          ->where('email_id', $email_id)

		          ->update(['password' => md5($password),

		          			'ref_key' => '',

							'updated_at' => date('Y-m-d H:i:s')]);

		    	return view('change_password', ['msg' => 'success']);

		    }else{

		    	return view('change_password', ['msg' => 'false']);

		    }

		}else{

			return view('change_password', ['msg' => 'false']);	

		}    

	}



}

