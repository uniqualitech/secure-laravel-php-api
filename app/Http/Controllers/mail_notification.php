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
   // require_once ('../../../vendor/autoload.php');
}


class mail_notification extends Controller
{

	//-------------- SENT EMAIL NOTIFICATION ----------------- //

	function send_mail($to,$event,$message,$name){

		//-------------- ADD TO CRON LOG -----------------//

		DB::table('tblcron_log')->insert(['log_data' => 'Email NOTIFICATION',
										  'event_id' => $event['event_id'],
                                          'created_at' => date('Y-m-d H:i:s'),
                                          'updated_at' => date('Y-m-d H:i:s')]);

	    $from = "admin@YOURSERVER.com";
	  	$subject = "Event Notification";
	  	$event_on;
	  	$font_style="font-family: 'Source Sans Pro', sans-serif !important;letter-spacing: 1px;";
	    $html = '<html>
                  <body style="'.$font_style.'">
                    <div style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;overflow: auto;">
                      <p style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;text-align: center;">Event Notification</p>
                      <p style="font-size: 16px;font-weight:600;width: 100%;color: #ffffff;float: right;margin-bottom: 0px;">
                      	Hello, <span style="font-weight:100;font-size: 14px;">'.$name.'</span>
                      </p>
                      <p style="font-size: 14px;width: 100%;color: #ffffff;float: right;margin-bottom: 0px;"> Your '.$message.'</p>
                      <p style="font-size: 16px;font-weight:600;width: 100%;color: #ffffff;float: right;margin-bottom: 0px;">Event Details : </p>
                      <p style="font-size: 12px;width: 100%;color: #1156ab;float: right;margin-bottom: 0px;font-weight:600;text-transform: capitalize;">
                      	<span><b style="color: #ffffff;">Title : </b>'.$event['title'].'</span><br/>
                      	<span><b style="color: #ffffff;">Start Date : </b>'.$event['start_date'].'</span><br/>
                      	<span><b style="color: #ffffff;">Start Time : </b>'.date("H:i A", strtotime($event['start_time'])).'</span><br/>
                      	<span><b style="color: #ffffff;">End Date : </b>'.$event['end_date'].'</span><br/>
                      	<span><b style="color: #ffffff;">End Time : </b>'.date("H:i A", strtotime($event['end_time'])).'</span><br/>
                      	<span><b style="color: #ffffff;">Location : </b>'.$event['location'].'</span><br/>
                      </p>
                    </div>
                  </body>
                </html>'; 

        $this->send_html_mail($from,$to,$subject,$html);        
        
	}

	function send_html_mail($from,$to,$subject,$html){

		if($host = request()->getHttpHost() != "localhost:8000"){        
		  	$mail = new PHPMailer(true);
	      	$mail->setFrom($from, 'Eclat App');
	      	$mail->addAddress($to, '');
	      	$mail->Subject  = $subject;
	      	$mail->MsgHTML($html);
	      	$mail->IsHTML(true);
	      	$mail->send();
	    }  

	}

	//-------------- SENT PUSH NOTIFICATION ----------------- //

	function send_push_notification($message, $event_id, $is_production_mode, $deviceToken,$type){

		//-------------- ADD TO CRON LOG -----------------//

		DB::table('tblcron_log')->insert(['log_data' => 'PUSH NOTIFICATION',
										  'event_id' => $event_id,
                                          'created_at' => date('Y-m-d H:i:s'),
                                          'updated_at' => date('Y-m-d H:i:s')]);

		$body = array();
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default',
            'type' => 1, 
            'event_id' => $event_id,                                
            'badge' => 1 
        );
        $payload = json_encode($body);
        $this->SendPushiOS($deviceToken, $payload, $is_production_mode);
	}

	//-------------- SENT IOS PUSH NOTIFICATION ----------------- //

	function SendPushiOS($deviceToken, $body, $is_production_mode) {
        //|| $is_production_mode == '0' || $is_production_mode == 0
	    if ($is_production_mode == '1' || $is_production_mode == 1 ) {
	        //live
	        $url = 'ssl://gateway.push.apple.com:2195';
	        $cert_path = config_path('push/ck_prod.pem');
	    } else {
	        //demo
	        $url = 'ssl://gateway.sandbox.push.apple.com:2195';
	        $cert_path = config_path('push/ck_dev.pem');
	    }

	    $logFile = "LIVE_PUSH_DEBUG.txt";
	    $logfh = fopen($logFile, 'a');
	    fwrite($logfh, "\n\n Log at " . date("Y-m-d H:i:s") . " ---------------- ");
	    $passphrase = 'password';
	    $ctx = stream_context_create();
	    stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_path); // path to cetificate
	    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
	   
	    $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

	    if (!$fp){
	      exit("Failed to connect: $err $errstr" . PHP_EOL);
	      fwrite($logfh, "\n Failed to connect: ". $err);
	    }

	    $payload = $body;
	    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
	    fwrite($logfh, "\n err = ".$err );
	    fwrite($logfh, "\n deviceToken = ".$deviceToken );
	    fwrite($logfh, "\n msg = ".$msg );
	    $result = fwrite($fp, $msg, strlen($msg));
	    fwrite($logfh, "\n result = ".$result );

	    fclose($fp);
    }
    
    function SendPushiOStest($is_production_mode = 0) {
        $deviceToken ='35e53bc5c7ae4a1fc4e2e7787df1c31377bdc9c67fa8b68021e2a0e68bb19350';
        $body = array();
        $body['aps'] = array(
            'alert' => "Test",
            'sound' => 'default',
            'type' => 1, 
            'event_id' => -1,                                
            'badge' => 1 
        );
        $payload = json_encode($body);

	    if ($is_production_mode == '1' || $is_production_mode == 1 || $is_production_mode == '0' || $is_production_mode == 0) {
	        //live
	        $url = 'ssl://gateway.push.apple.com:2195';
	        $cert_path = config_path('push/ck_prod.pem');
	    } else {
	        //demo
	        $url = 'ssl://gateway.sandbox.push.apple.com:2195';
	        $cert_path = config_path('push/ck_dev.pem');
	    }

	    $logFile = "LIVE_PUSH_DEBUG.txt";
	    $logfh = fopen($logFile, 'a');
	    fwrite($logfh, "\n\n Log at " . date("Y-m-d H:i:s") . " ---------------- ");
	    $passphrase = 'password';
	    $ctx = stream_context_create();
	    stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_path); // path to cetificate
	    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
	   
	    $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

	    if (!$fp){
	      exit("Failed to connect: $err $errstr" . PHP_EOL);
	      fwrite($logfh, "\n Failed to connect: ". $err);
	    }

	   // $payload = $body;
	    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
	    fwrite($logfh, "\n err = ".$err );
	    fwrite($logfh, "\n deviceToken = ".$deviceToken );
	    fwrite($logfh, "\n msg = ".$msg );
	    $result = fwrite($fp, $msg, strlen($msg));
	    fwrite($logfh, "\n result = ".$result );

	    fclose($fp);
    }
	
}
