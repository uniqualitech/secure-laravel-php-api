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



if($host = request()->getHttpHost() != "localhost:8000"){

   // require_once ('../../../vendor/autoload.php');

}









class notification extends Controller

{





	//-------------- EVENT NOTIFICATION ----------------- //



	function notification(){

	    

// 		DB::table('tblcron_log')->insert(['log_data' => 'Notificationlog',

//                                   'created_at' => date('Y-m-d H:i:s'),

//                                   'updated_at' => date('Y-m-d H:i:s')]);



		//-------------- CREATE MODEL OBJECT ----------------- //



		$API = new api_model;

		$REPEAT_EVENT = new repeat_event_model;

		$MAIL_NOTIFICATION = new mail_notification;



		//-------------- GET START DATE AND END DATE ----------------- //



		$current_datetime = date('Y-m-d');

		$end_date = date('Y-m-d',strtotime(date('Y-m-d H:i:s')."+4 week")); 



		//-------------- DATA ARRAY TO CALL GET_EVENT API ----------------- //



		$data = array('notification'=>'1','event_view'=>'-1','start_date'=>$current_datetime,'end_date'=>$end_date,'user_current_datetime'=>$current_datetime);



		//-------------- CALL GET_EVENT API ----------------- //



		$res = $API->get_event($data);

		

		//-------------- API RESPONSE ----------------- //



		$resVal = json_decode($res,true);

		$resVal = $resVal['data'];

		

		//-------------- "Notification": "0:never,1:On the day,2:A day before,3:A week before,4:Custom" ----------------- //



		if(!empty($resVal) || count($resVal) > 0){



			//-------------- Mail TEMPLATE ----------------- //



			foreach ($resVal as $value) {



				//-------------- CHECK NOTIFICATION SENT OR NOT ----------------- //



				$notification_status = $this->check_notification_log($value['event_id'],$value['start_date'],$value['start_time'],$value['updated_at']);

				// print_r($notification_status);

				if(count($notification_status) > 0){

					continue;

				}



				//-------------- GET USER DATA ----------------- //

				

				$push_data = $this->get_user_push_detail($value['user_id']);

				

				$user = $API->getUserData($value['user_id']);

				if($user[0]->time_zone != ""){

					$var  = Carbon::now($user[0]->time_zone);

			      	$time = $var->toTimeString();

					$current_datetime = date('d-m-Y H:i',strtotime($time));

				}		



				//-------------- EVENT DATE TIME ----------------- //



				$event_start_date_time = $value['start_date'].' '.$value['start_time']; 

				$event_end_date_time = $value['end_date'].' '.$value['end_time']; 



				if($value['notification'] == '1'){



					//-------------- ON THE DAY NOTIFICATION ----------------- //

					$current_datetime.'/'.$value['start_date'].' '.$value['start_time']."/";

					$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],1);

					if($difference == '0'){



						//-------------- SET NOTIFICATION MESSAGE ----------------- //



						$message = stripslashes($value['title'])." event is today.";



						//-------------- SEND NOTIFICATION ----------------- //



						if($value['notification_type'] == '0'){



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								//$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	



						}elseif ($value['notification_type'] == '1') {



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								//$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	

							

						}elseif ($value['notification_type'] == '2') {



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);

							

						}



						//-------------- ADD TO NOTIFICATION HISTORY AND LOG ----------------- //



						$this->add_notification_history($value['user_id'],$message,1);

						$this->add_notification_log($value['event_id'],$value['start_date'],$value['start_time']);



					}



				}else if($value['notification'] == '2'){



					//-------------- A DAY BEFOR NOTIFICATION ----------------- //



					$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],1);

					if($difference == '1'){



						//-------------- SET NOTIFICATION MESSAGE ----------------- //



						$message = stripslashes($value['title'])." event is tomorrow.";



						//-------------- SEND NOTIFICATION ----------------- //



						if($value['notification_type'] == '0'){



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								// $MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	



						}elseif ($value['notification_type'] == '1') {



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								// $MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	

							

						}elseif ($value['notification_type'] == '2') {



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);

							

						}



						//-------------- ADD TO NOTIFICATION HISTORY AND LOG ----------------- //



						$this->add_notification_history($value['user_id'],$message,1);

						$this->add_notification_log($value['event_id'],$value['start_date'],$value['start_time']);



					}

					

				}else if($value['notification'] == '3'){



					//-------------- A WEEK BEFOR NOTIFICATION ----------------- //



					$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],2);

					if($difference == '1'){



						//-------------- SET NOTIFICATION MESSAGE ----------------- //



						$message = stripslashes($value['title'])." event is in next week.";



						//-------------- SEND NOTIFICATION ----------------- //



						if($value['notification_type'] == '0'){



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								// $MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	



						}elseif ($value['notification_type'] == '1') {



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								//$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	

							

						}elseif ($value['notification_type'] == '2') {



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);

							

						}



						//-------------- ADD TO NOTIFICATION HISTORY AND LOG ----------------- //



						$this->add_notification_history($value['user_id'],$message,1);

						$this->add_notification_log($value['event_id'],$value['start_date'],$value['start_time']);



					}



				}else if($value['notification'] == '4'){



					//-------------- CUSTOM NOTIFICATION ----------------- //



					$value['custom_notification'] = $value['custom_notification'][0];

					if(isset($value['custom_notification']['frequency']) && $value['custom_notification']['frequency'] == "0"){



						//-------------- CUSTOM MINUTE NOTIFICATION ----------------- //



						$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],-1);

						$before = $value['custom_notification']['before'];

						if($before == '1'){

							$message_text = " event is after ".$before." minute.";

						}else{

							$message_text = " event is after ".$before." minutes.";	

						}

						

					}elseif(isset($value['custom_notification']['frequency']) && $value['custom_notification']['frequency'] == "1"){



						//-------------- CUSTOM HOUR NOTIFICATION ----------------- //



						$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],0);

						$before = $value['custom_notification']['before'];

						if($before == '1'){

							$message_text = " event is after ".$before." hour.";

						}else{

							$message_text = " event is after ".$before." hours.";	

						}

						

					}elseif(isset($value['custom_notification']['frequency']) && $value['custom_notification']['frequency'] == "2"){



						//-------------- CUSTOM DAY NOTIFICATION ----------------- //



						$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],1);

						$before = $value['custom_notification']['before'];

						if($before == '1'){

							$message_text = " event is after ".$before." day.";

						}else{

							$message_text = " event is after ".$before." days.";	

						}

						

					}else{



						//-------------- CUSTOM WEEK NOTIFICATION ----------------- //

						

						$difference = $REPEAT_EVENT->date_difference($current_datetime,$value['start_date'].' '.$value['start_time'],2);

						$before = @$value['custom_notification']['before'];

						if($before == '1'){

							$message_text = " event is after ".$before." week.";

						}else{

							$message_text = " event is after ".$before." weeks.";

						}



					}

					

					if($difference == $before){



						//-------------- SET NOTIFICATION MESSAGE ----------------- //



						$message = stripslashes($value['title']).''.$message_text;



						//-------------- SEND NOTIFICATION ----------------- //



						if($value['notification_type'] == '0'){



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								// $MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	



						}elseif ($value['notification_type'] == '1') {



							//-------------- SEND PUSH NOTIFICATION  ----------------- //



							if(count($push_data) > 0){

								foreach ($push_data as  $pushArr) {

									$MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $pushArr->certificate_type, $pushArr->device_token,1);

								}

								// $MAIL_NOTIFICATION->send_push_notification($message, $value['event_id'], $push_data[0]->certificate_type, $push_data[0]->device_token,1);

							}	

							

						}elseif ($value['notification_type'] == '2') {



							//-------------- SEND E-MAIL  ----------------- //



							$MAIL_NOTIFICATION->send_mail($user[0]->email_id,$value,$message,$user[0]->name);

							

						}



						//-------------- ADD TO NOTIFICATION HISTORY AND LOG ----------------- //



						$this->add_notification_history($value['user_id'],$message,1);

						$this->add_notification_log($value['event_id'],$value['start_date'],$value['start_time']);

					}

				}

			}

		}

	}



	//-------------- EVENT REMAINDER ----------------- //



	function reminder(){

		 

		//-------------- CREATE MODEL OBJECT ----------------- //

		

// 		DB::table('tblcron_log')->insert(['log_data' => 'reminder',

//                                           'created_at' => date('Y-m-d H:i:s'),

//                                           'updated_at' => date('Y-m-d H:i:s')]);



		$API = new api_model;

		$MAIL_NOTIFICATION = new mail_notification;



		//-------------- GET START DATE AND END DATE ----------------- //



		$current_datetime = strtotime(date('Y-m-d H:i'));

        $start_date = @date('Y-m-d').' 00:00:00';

        $end_date = @date('Y-m-d').' 23:59:59';



		

		//-------------- DATA ARRAY TO CALL GET_EVENT API ----------------- //



		$data = array('notification'=>'1','event_view'=>'-2','user_current_datetime'=>$current_datetime);



		//-------------- CALL GET_EVENT API ----------------- //

		$res = $API->get_event($data);

		//-------------- API RESPONSE ----------------- //



		$resVal = json_decode($res,true);

		$resVal = $resVal['data'];

		

		if(!empty($resVal) || count($resVal) > 0){



			foreach ($resVal as $value) {

				//-------------- GET USER DATA ----------------- //
				

				$user = $API->getUserData($value['user_id']);


				//-------------- EVENT DATE TIME ----------------- //


				$reminder_date_time = strtotime(date('Y-m-d H:i',strtotime($value['reminder_date_time'])));


				if($user[0]->time_zone != ""){

					$var  = Carbon::now($user[0]->time_zone);

			      	$time = $var->toTimeString();

					$current_datetime = strtotime(date('Y-m-d H:i',strtotime($time)));

				}		

				//echo "current".date('Y-m-d H:i',strtotime($time)).'-reminder'.date('Y-m-d H:i',strtotime($value['reminder_date_time']));

				if($current_datetime == $reminder_date_time){

					//-------------- GET USER PUSH DATA ----------------- //

					$push_data = $this->get_user_push_detail($value['user_id']);

					//-------------- SET MESSAGE FOR REMINDER  ----------------- //

					if($value['related_event_id'] != "0"){
						$eventArr = $API->get_event_data($value['related_event_id']);
						$event_type = $eventArr[0]->event_type;
						$event_title = stripslashes($eventArr[0]->title);
					}else{
						$event_type = $value['event_type'];
						$event_title = stripslashes($value['title']);
					}	
					//	1:Diary,2:Planner,3:To-dos,4:Notes
					if($event_type == "1"){
						$event_txt = " diary entry ";
					}else if($event_type == "2"){
						$event_txt = " planner entry ";
					}else if($event_type == "3"){
						$event_txt = " To-dos entry ";
					}else if($event_type == "4"){
						$event_txt = " Notes entry ";
					}

					$message = "this is a reminder for your".$event_txt."-".$event_title;
					
					//-------------- SEND E-MAIL  ----------------- //



					$from = "admin@YOURSERVER.com";

					$to = $user[0]->email_id;

	  				$subject = "Event Reminder";

	  				$font_style="font-family: 'Source Sans Pro', sans-serif !important;letter-spacing: 1px;";

				   	$html = '<html>

			                  <body style="'.$font_style.'">

			                    <div style="text-align:left;text-align: left;background: #378FE5;width: 50%;margin:auto;padding: 30px;color: #fff;border-radius: 5px;line-height: 25px;overflow: auto;">

			                      <p style="font-size: 18px;font-weight:900;width: 100%;color: #ffffff;float: right;margin-bottom: 12px;text-align: center;">Event Reminder</p>

			                      <p style="font-size: 16px;font-weight:600;width: 100%;color: #ffffff;float: right;margin-bottom: 0px;">

			                      	Dear <span style="font-weight:100;font-size: 14px;">'.$user[0]->name.',</span>

			                      </p>

			                      <p style="font-size: 14px;width: 100%;color: #ffffff;float: right;margin-bottom: 0px;"> '.$message.'</p>

			                      <p style="font-size: 16px;font-weight:600;width: 100%;color: #ffffff;float: right;margin-bottom: 0px;">Event Details : </p>

			                      <p style="font-size: 12px;width: 100%;color: #1156ab;float: right;margin-bottom: 0px;font-weight:600;text-transform: capitalize;">

			                      	<span><b style="color: #ffffff;">Title : </b>'.$value['title'].'</span><br/>

			                      	<span><b style="color: #ffffff;">Start Date : </b>'.$value['start_date'].'</span><br/>

			                      	<span><b style="color: #ffffff;">Start Time : </b>'.date("H:i A", strtotime($value['start_time'])).'</span><br/>

			                      </p>

			                    </div>

			                  </body>

			                </html>'; 



			        //-------------- CHECK NOTIFICATION TYPE  ----------------- //        



			        if($value['notification_type'] == '0' || $value['notification_type'] == '2'){



			        	//-------------- SEND MAIL NOTIFICATION  ----------------- //



						$MAIL_NOTIFICATION->send_html_mail($from,$to,$subject,$html);



					}	



					if($value['notification_type'] == '0' || $value['notification_type'] == '1'){	



						//-------------- SEND PUSH NOTIFICATION  ----------------- //



						if(count($push_data) > 0){

							$body = array();

					        $body['aps'] = array(

					            'alert' => $message,

					            'sound' => 'default',

					            'type' => 2, 

					            'event_id' => $value['event_id'],                                

					            'badge' => 1 

					        );

					        $payload = json_encode($body);

					        foreach ($push_data as  $pushArr) {

								$MAIL_NOTIFICATION->SendPushiOS($pushArr->device_token, $payload, $pushArr->certificate_type);

							}

							//$MAIL_NOTIFICATION->SendPushiOS($push_data[0]->device_token, $payload, $push_data[0]->certificate_type);



						}

					}		



					//-------------- CREATE LOG & HISTORY ----------------- //



					$this->add_notification_history($value['user_id'],$message,2);

					$this->add_reminder_log($value['event_id'],$value['start_date'],$value['start_time']);

					$this->updated_reminder_flag($value['event_id']);		



				}

			}

		}			

	}



	//-------------- GET USER PUSH DETAIL  ----------------- //



	function get_user_push_detail($user_id){

		return  DB::table('tblpush_user AS pu')

                  ->select(DB::raw('*'))

                  ->join('tblusers AS u', 'u.user_id', '=', 'pu.user_id')

                  ->where('pu.user_id', '=', $user_id)

                  ->get();

	}



	//-------------- CHECK NOTIFICATION SENT OR NOT  ----------------- //



	function check_notification_log($event_id,$start_date,$start_time,$event_updated_at){



		return  DB::table('tblnotification_log')

                  ->select(DB::raw('*'))

                  ->where('event_id', '=', $event_id)

                  ->where('start_date', '=', date('Y-m-d',strtotime($start_date)))

                  ->where('start_time', '=', $start_time)

                  ->where('created_at', '=', date('Y-m-d'))

                //   ->where('updated_at', '>=', $event_updated_at)

                  ->get();

	}



	//-------------- ADD NOTIFICATION LOG  ----------------- //



	function add_notification_log($event_id,$start_date,$start_time){

		

        DB::table('tblnotification_log')->insert(['event_id' => $event_id,

			                                      'start_date' => date('Y-m-d',strtotime($start_date)),

			                                      'start_time' => $start_time,

			                                      'created_at' => date('Y-m-d H:i:s'),

			                                      'updated_at' => date('Y-m-d H:i:s')]);          

	}



	//-------------- ADD REMINDER LOG  ----------------- //



	function add_reminder_log($event_id,$start_date,$start_time){

		

        DB::table('tblreminder_log')->insert(['event_id' => $event_id,

		                                      'start_date' => date('Y-m-d',strtotime($start_date)),

		                                      'start_time' => $start_time,

		                                      'created_at' => date('Y-m-d H:i:s'),

		                                      'updated_at' => date('Y-m-d H:i:s')]);          

	}



	//-------------- ADD NOTIFICATION HISTORY  ----------------- //



	function add_notification_history($user_id,$message,$type){

		

        DB::table('tblpush_notification')->insert([  'user_id' => $user_id,

			                                         'message' => addslashes($message),

			                                         'type' => $type,

			                                         'created_at' => date('Y-m-d H:i:s'),

			                                         'updated_at' => date('Y-m-d H:i:s')]);          

	}



	//-------------- UPDATE NOTIFICATION HISTORY  ----------------- //



	function updated_reminder_flag($event_id){

		return  DB::table('tblevents')

		          ->where('event_id', $event_id)

		          ->update(['is_reminder_sent' => 1,

		                    'updated_at' => date('Y-m-d H:i:s')]);

	}

}

