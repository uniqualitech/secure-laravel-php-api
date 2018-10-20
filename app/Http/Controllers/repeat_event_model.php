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



use Carbon\Carbon;



class repeat_event_model extends Controller

{



   //-------------- NORMAL LIST EVENT ----------------- //



   function normal_event($value,$event_view,$start_date,$end_date,$user_current_datetime){

      $current_datetime = strtotime($user_current_datetime);

      $event_end_datetime = strtotime($value->end_date.' '.$value->end_time);

      $resArr = array();



      if($event_view !="0"){

         if( ( strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->start_date))) 

           && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->end_date))) )

         || strtotime(date('d-m-Y',strtotime($end_date))) < strtotime(date('d-m-Y',strtotime($value->start_date)))){

            return $resArr; 

         }

      }

     

      

      if($value->event_type !="3" && $value->event_type !="4" && $current_datetime > $event_end_datetime && $value->event_type != '1' && $value->end_date != '0000-00-00' && $value->event_type !="0"){

        

         DB::table('tblevents')

           ->where('event_id', $value->event_id)

           ->update(['event_type' => 1,

                     'updated_at' => date('Y-m-d H:i:s')]);

      }else{

         $i=0;

         $resArr[$i]['event_id'] = (string) $value->event_id;

         $resArr[$i]['event_type'] = (string) $value->event_type;

         $resArr[$i]['is_completed'] = (string) $value->is_completed;

         $resArr[$i]['user_id'] = (string) $value->user_id;

         $resArr[$i]['title'] =  $value->title;

         $resArr[$i]['all_day'] = (string) $value->all_day;

         $resArr[$i]['start_date'] =  (isset($value->start_date) && $value->start_date != "0000-00-00")? date('d-m-Y',strtotime($value->start_date)) : "";

         $resArr[$i]['start_time'] =  $value->start_time;

         $resArr[$i]['end_date'] =  (isset($value->end_date) && $value->end_date != "0000-00-00")? date('d-m-Y',strtotime($value->end_date)) : "";

         $resArr[$i]['end_time'] =  $value->end_time;

         $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

         $resArr[$i]['created_at'] =  $value->created_at;

         $resArr[$i]['updated_at'] =  $value->updated_at;

         $resArr[$i]['set_reminder'] =  $value->set_reminder;

         $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

         $resArr[$i]['notification_type'] =  $value->notification_type;

         $resArr[$i]['notification'] =  $value->notification;

         $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

         $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

      }  

      return $resArr; 

   }



   //-------------- REMINDER EVENT ----------------- //



   function reminder_event($value){

      $i=0;

      $resArr[$i]['event_id'] = (string) $value->event_id;

      $resArr[$i]['event_type'] = (string) $value->event_type;

      $resArr[$i]['is_completed'] = (string) $value->is_completed;

      $resArr[$i]['user_id'] = (string) $value->user_id;

      $resArr[$i]['title'] =  $value->title;

      $resArr[$i]['all_day'] = (string) $value->all_day;

      $resArr[$i]['start_date'] =  (isset($value->start_date) && $value->start_date != "0000-00-00")? date('d-m-Y',strtotime($value->start_date)) : "";

      $resArr[$i]['start_time'] =  $value->start_time;

      $resArr[$i]['end_date'] =  (isset($value->end_date) && $value->end_date != "0000-00-00")? date('d-m-Y',strtotime($value->end_date)) : "";

      $resArr[$i]['end_time'] =  $value->end_time;

      $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

      $resArr[$i]['created_at'] =  $value->created_at;

      $resArr[$i]['updated_at'] =  $value->updated_at;

      $resArr[$i]['set_reminder'] =  $value->set_reminder;

      $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

      $resArr[$i]['notification_type'] =  $value->notification_type;

      $resArr[$i]['notification'] =  $value->notification;

      $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

      $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

      return $resArr; 

   }



	//-------------- REPEAT EVERY DAY EVENT ----------------- //



	function repeat_every_day($value,$difference,$start_date,$end_date,$user_current_datetime){

      $resArr = array();

      for ($i=0; $i <= $difference; $i++) {

      	if( ( strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->start_date." +".$i." day")))

               && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->end_date." +".$i." day"))) )

            || strtotime(date('d-m-Y',strtotime($end_date))) < strtotime(date('d-m-Y',strtotime($value->start_date." +".$i." day")))){

      	   continue;

      	}

         $resArr[$i]['event_id'] = (string) $value->event_id;

         $resArr[$i]['event_type'] = (string) $value->event_type;

         $resArr[$i]['is_completed'] = (string) $value->is_completed;

         $resArr[$i]['user_id'] = (string) $value->user_id;

         $resArr[$i]['title'] =  $value->title;

         $resArr[$i]['all_day'] = (string) $value->all_day;

         $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($value->start_date." +".$i." day"));

         $resArr[$i]['start_time'] =  $value->start_time;

         $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($value->end_date." +".$i." day"));

         $resArr[$i]['end_time'] =  $value->end_time;

         $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

         $resArr[$i]['created_at'] =  $value->created_at;

         $resArr[$i]['updated_at'] =  $value->updated_at;

         $resArr[$i]['set_reminder'] =  $value->set_reminder;

         $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

         $resArr[$i]['notification_type'] =  $value->notification_type;

         $resArr[$i]['notification'] =  $value->notification;

         $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

         $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

      } 

      return $resArr;

   }



   //-------------- REPEAT EVERY WEEK EVENT ----------------- //



	function repeat_every_week($value,$difference,$start_date,$end_date,$user_current_datetime){

      $resArr = array();

      for ($i=0; $i <= $difference; $i++) {

      	if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($value->start_date."".$i." week")))

               && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->end_date." +".$i." week"))) )

            || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($value->start_date."".$i." week")))){

      		continue;

      	}

         $resArr[$i]['event_id'] = (string) $value->event_id;

         $resArr[$i]['event_type'] = (string) $value->event_type;

         $resArr[$i]['is_completed'] = (string) $value->is_completed;

         $resArr[$i]['user_id'] = (string) $value->user_id;

         $resArr[$i]['title'] =  $value->title;

         $resArr[$i]['all_day'] = (string) $value->all_day;

         $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($value->start_date."".$i." week" ));

         $resArr[$i]['start_time'] =  $value->start_time;

         $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($value->end_date."".$i." week"));

         $resArr[$i]['end_time'] =  $value->end_time;

         $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

         $resArr[$i]['created_at'] =  $value->created_at;

         $resArr[$i]['updated_at'] =  $value->updated_at;

         $resArr[$i]['set_reminder'] =  $value->set_reminder;

         $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

         $resArr[$i]['notification_type'] =  $value->notification_type;

         $resArr[$i]['notification'] =  $value->notification;

         $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

         $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

      } 

      return $resArr;

   }



   //-------------- REPEAT EVERY MONTH EVENT ----------------- //



   function repeat_every_month($value,$difference,$start_date,$end_date,$user_current_datetime){

      $resArr = array();

      for ($i=0; $i <= $difference; $i++) {

         if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($value->start_date."".$i." month"))) 

               && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->end_date." +".$i." month"))) )

            || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($value->start_date."".$i." month")))){

            continue;

         }

         $resArr[$i]['event_id'] = (string) $value->event_id;

         $resArr[$i]['event_type'] = (string) $value->event_type;

         $resArr[$i]['is_completed'] = (string) $value->is_completed;

         $resArr[$i]['user_id'] = (string) $value->user_id;

         $resArr[$i]['title'] =  $value->title;

         $resArr[$i]['all_day'] = (string) $value->all_day;

         $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($value->start_date."".$i." month" ));

         $resArr[$i]['start_time'] =  $value->start_time;

         $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($value->end_date."".$i." month"));

         $resArr[$i]['end_time'] =  $value->end_time;

         $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

         $resArr[$i]['created_at'] =  $value->created_at;

         $resArr[$i]['updated_at'] =  $value->updated_at;

         $resArr[$i]['set_reminder'] =  $value->set_reminder;

         $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

         $resArr[$i]['notification_type'] =  $value->notification_type;

         $resArr[$i]['notification'] =  $value->notification;

         $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

         $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

      } 

      return $resArr;

   }



   //-------------- REPEAT EVERY YEAR EVENT ----------------- //



   function repeat_every_year($value,$difference,$start_date,$end_date,$user_current_datetime){

      $resArr = array();

      for ($i=0; $i <= $difference; $i++) {

            

            if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($value->start_date."".$i." year"))) 

                  && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->end_date." +".$i." year"))) )

               || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($value->start_date."".$i." year")))){

               continue;

            }



            $resArr[$i]['event_id'] = (string) $value->event_id;

            $resArr[$i]['event_type'] = (string) $value->event_type;

            $resArr[$i]['is_completed'] = (string) $value->is_completed;

            $resArr[$i]['user_id'] = (string) $value->user_id;

            $resArr[$i]['title'] =  $value->title;

            $resArr[$i]['all_day'] = (string) $value->all_day;

            $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($value->start_date."".$i." year" ));

            $resArr[$i]['start_time'] =  $value->start_time;

            $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($value->end_date."".$i." year"));

            $resArr[$i]['end_time'] =  $value->end_time;

            $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

            $resArr[$i]['created_at'] =  $value->created_at;

            $resArr[$i]['updated_at'] =  $value->updated_at;

            $resArr[$i]['set_reminder'] =  $value->set_reminder;

            $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

            $resArr[$i]['notification_type'] =  $value->notification_type;

            $resArr[$i]['notification'] =  $value->notification;

            $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

            $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

      } 

      return $resArr;

   }



   //-------------- CUSTOM REPEAT EVENT ----------------- //



   function custom_repeat($value,$start_date,$end_date,$user_current_datetime){



      $custom_repeat = json_decode($value->custom_repeat,true);



      //-------------- "frequency": "0:Daily,1:Weekly,2:Monthly,3:Yearly" ----------------- //



      $frequency = $custom_repeat[0]['frequency'];



      //-------------- "every": "2:Day,Week,Month,Yearly" ----------------- //



      $every = $custom_repeat[0]['every'];



      //-------------- "on": "0:Sunday,1:Monday" ----------------- //



      $on = $custom_repeat[0]['on'];

      if($on !=""){

            $onArr = explode(',',$on); 

      }



      //-------------- "end_never": "1:never end" ----------------- //



      $end_never = $custom_repeat[0]['end_never'];



      //-------------- "end_on": "01-03-2018" ----------------- //



      $end_on = $custom_repeat[0]['end_on'];



      //-------------- "end_after_occurrence": "01-03-2018" ----------------- //



      $end_after_occurrence = $custom_repeat[0]['end_after_occurrence'];



      $resArr = array();



      if($frequency == "0"){





         //-------------- REPET DAILY ----------------- //



         $difference = $this->date_difference($value->start_date,$end_date,1);

         



         for ($i=0; $i <= $difference; $i=$i+$every) {



            //-------------- CHECK EVENT BETWENT START & END DATE ----------------- //



            if( (strtotime($start_date) > strtotime(date('d-m-Y',strtotime($value->start_date." +".$i." day")))

                 && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($value->end_date." +".$i." day"))) )

               || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($value->start_date." +".$i." day")))){

               continue;

            }



            //-------------- CHECK END ON EVENT ----------------- //



            if($end_never !="1" && $end_on !="" && (strtotime($end_on) == strtotime(date('d-m-Y',strtotime($value->start_date." +".$i." day"))) 

                                                    || strtotime($end_on) < strtotime(date('d-m-Y',strtotime($value->start_date." +".$i." day")))) ){

               continue;

            }



            //-------------- CHECK EVENT OCUURRENCE ----------------- //



            $eventDate = date('d-m-Y',strtotime($value->start_date." +".$i." day"));

            $occurrenc_difference = $this->date_difference($value->start_date,$eventDate,1) / $every;

            

            if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && $occurrenc_difference > $end_after_occurrence - 1){

               continue;

            }

         

            $resArr[$i]['event_id'] = (string) $value->event_id;

            $resArr[$i]['event_type'] = (string) $value->event_type;

            $resArr[$i]['is_completed'] = (string) $value->is_completed;

            $resArr[$i]['user_id'] = (string) $value->user_id;

            $resArr[$i]['title'] =  $value->title;

            $resArr[$i]['all_day'] = (string) $value->all_day;

            $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($value->start_date." +".$i." day"));

            $resArr[$i]['start_time'] =  $value->start_time;

            $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($value->end_date." +".$i." day"));

            $resArr[$i]['end_time'] =  $value->end_time;

            $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

            $resArr[$i]['created_at'] =  $value->created_at;

            $resArr[$i]['updated_at'] =  $value->updated_at;

            $resArr[$i]['set_reminder'] =  $value->set_reminder;

            $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

            $resArr[$i]['notification_type'] =  $value->notification_type;

            $resArr[$i]['notification'] =  $value->notification;

            $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

            $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

         } 

         return $resArr; 

     

      }else if($frequency == "1"){

            

         //-------------- REPET WEEKLY ----------------- //



         $difference = $this->date_difference($value->start_date,$end_date,2);

         // if($every != ""){

         //   $difference = round($difference /  $every);

         // }else{

         //    return array();

         // } 

         $resAllArr = array();

         

         for ($i=0; $i <= $difference; $i=$i+$every) {

            

            $week_date_actual =  date('d-m-Y',strtotime($value->start_date." +".$i." week"))."\n"; 

            $week_end_date =  date('d-m-Y',strtotime($value->end_date." +".$i." week"));

            $event_days = $this->date_difference($week_date_actual,$week_end_date,1); 



            //-------------- CHECK DAY OF THE WEEK ----------------- //



            if(isset($on) && $on !="" ){

               for ($j=0; $j <= 6 ; $j++) { 

                  

                  //-------------- GET START & END DATE OF WEEK ----------------- //



                  $start_week  = date("Y-m-d", strtotime('sunday this week', strtotime($week_date_actual)));

                  

                  // if($i == 0 &&  $j == 0){

                  //    $week_date =  date('d-m-Y',strtotime($week_date_actual));

                  // }else{

                     //$k = $j - 1;

                     $week_date =  date('d-m-Y',strtotime($start_week." +".$j." day"));

                     $week_end_date =  date('d-m-Y',strtotime($week_date." +".$event_days." day"));

                  //}



                  //-------------- CHECK DATE IS NOT LESS THAN ACTUAL DATE ----------------- //



                  if(strtotime($week_date) < strtotime($value->start_date)){

                     continue;

                  }

                 

                  //-------------- CHECK DAY OF THE WEEK ----------------- //



                  $day_of_week = date('w',strtotime($week_date));

                  if(!in_array($day_of_week,$onArr)){

                     if($i == 0 && $j ==0){

                     }else{

                        continue; 

                     }

                  }

                 

                  //-------------- CHECK EVENT BETWENT START & END DATE ----------------- //



                  if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($week_date)))

                        && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($week_end_date." +".$j." day"))) )

                     || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($week_date)))){

                     continue;

                  }



                  //-------------- CHECK END ON EVENT ----------------- //



                  if($end_never !="1" && $end_on !="" && (strtotime($end_on) == strtotime(date('d-m-Y',strtotime($week_date))) 

                                                          || strtotime($end_on) < strtotime(date('d-m-Y',strtotime($week_date))) )){

                     continue;

                  }



                  //-------------- CHECK EVENT OCUURRENCE ----------------- //



                  $occurrenc_difference = $this->date_difference($value->start_date,$week_date_actual,2) / $every;

               

                  if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && $occurrenc_difference > $end_after_occurrence - 1){

                     continue;

                  }

              

                  // if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && count($resArr) > $end_after_occurrence - 1){

                  //    continue;

                  // }



                  $resArr[$i][$j]['event_id'] = (string) $value->event_id;

                  $resArr[$i][$j]['event_type'] = (string) $value->event_type;

                  $resArr[$i][$j]['is_completed'] = (string) $value->is_completed;

                  $resArr[$i][$j]['user_id'] = (string) $value->user_id;

                  $resArr[$i][$j]['title'] =  $value->title;

                  $resArr[$i][$j]['all_day'] = (string) $value->all_day;

                  $resArr[$i][$j]['start_date'] =  date('d-m-Y',strtotime($week_date));

                  $resArr[$i][$j]['start_time'] =  $value->start_time;

                  $resArr[$i][$j]['end_date'] =  date('d-m-Y',strtotime($week_end_date));

                  $resArr[$i][$j]['end_time'] =  $value->end_time;

                  $resArr[$i][$j]['repeat_mode'] =  $value->repeat_mode;

                  $resArr[$i][$j]['created_at'] =  $value->created_at;

                  $resArr[$i][$j]['updated_at'] =  $value->updated_at;

                  $resArr[$i][$j]['set_reminder'] =  $value->set_reminder;

                  $resArr[$i][$j]['reminder_date_time'] =  $value->reminder_date_time;

                  $resArr[$i][$j]['notification_type'] =  $value->notification_type;

                  $resArr[$i][$j]['notification'] =  $value->notification;

                  $resArr[$i][$j]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

                  $resArr[$i][$j]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";



               }



               $resAllArr = $this->convert_array($resArr);

            }else{



               //-------------- CHECK EVENT BETWENT START & END DATE ----------------- //



               if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($week_date_actual)))

                     && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($week_end_date))) )

                  || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($week_date_actual)))){

                     continue;

               }



               //-------------- CHECK END ON EVENT ----------------- //



               if($end_never !="1" && $end_on !="" && (strtotime($end_on) == strtotime(date('d-m-Y',strtotime($week_date_actual))) 

                                                       || strtotime($end_on) < strtotime(date('d-m-Y',strtotime($week_date_actual))))){

                  continue;

               }



               //-------------- CHECK EVENT OCUURRENCE ----------------- //



               $occurrenc_difference = $this->date_difference($value->start_date,$week_date_actual,2) / $every;

               

               if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && $occurrenc_difference > $end_after_occurrence - 1){

                  continue;

               }

            

               // if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && count($resArr) > $end_after_occurrence - 1){

               //    continue;

               // }



               $resArr[$i]['event_id'] = (string) $value->event_id;

               $resArr[$i]['event_type'] = (string) $value->event_type;

               $resArr[$i]['is_completed'] = (string) $value->is_completed;

               $resArr[$i]['user_id'] = (string) $value->user_id;

               $resArr[$i]['title'] =  $value->title;

               $resArr[$i]['all_day'] = (string) $value->all_day;

               $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($week_date_actual));

               $resArr[$i]['start_time'] =  $value->start_time;

               $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($week_end_date));

               $resArr[$i]['end_time'] =  $value->end_time;

               $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

               $resArr[$i]['created_at'] =  $value->created_at;

               $resArr[$i]['updated_at'] =  $value->updated_at;

               $resArr[$i]['set_reminder'] =  $value->set_reminder;

               $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

               $resArr[$i]['notification_type'] =  $value->notification_type;

               $resArr[$i]['notification'] =  $value->notification;

               $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

               $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

               $resAllArr = $resArr;

            } 

            

         } 



         return $resAllArr;   

            

      }else if($frequency == "2"){



         //-------------- REPET MONTHLY ----------------- //

         

         $difference = $this->date_difference($value->start_date,$end_date,3);

         // if($every != ""){

         //   $difference = round($difference /  $every);

         // }else{

         //    return array();

         // } 



         // $time=strtotime($value->start_date);

         // $month=date("F",$time);

         // $year=date("Y",$time);



         

         for ($i=0; $i <= $difference; $i=$i+$every) {

           

            $month_date =  date('d-m-Y',strtotime($value->start_date." +".$i." month")); 

            $month_end_date =  date('d-m-Y',strtotime($value->end_date." +".$i." month")); 

            $event_days = $this->date_difference($month_date,$month_end_date,1); 



            //-------------- CHECK DAY OF THE WEEK ----------------- //



            if(isset($on) && $on !="" && $i != 0){

               $dayArr = $this->format_week_day($on);

               if($dayArr['week_letter'] !="" && $dayArr['day_letter'] !="" ){

                  $month_date = date("d-m-Y", strtotime($dayArr['week_letter']." ".$dayArr['day_letter']." of ".date('M',strtotime($month_date))." ".date('Y',strtotime($month_date)).""));

                  $month_end_date =  date('d-m-Y',strtotime($month_date." +".$event_days." day")); 

               }   

            }

            

            //-------------- CHECK EVENT BETWENT START & END DATE ----------------- //



            if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($month_date)))

                  && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($month_end_date))) )

               || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($month_date)))){

               continue;

            }



            //-------------- CHECK END ON EVENT ----------------- //



            if($end_never !="1" && $end_on !="" && (strtotime($end_on) == strtotime(date('d-m-Y',strtotime($month_date))) 

                                                    || strtotime($end_on) < strtotime(date('d-m-Y',strtotime($month_date))))){

               continue;

            }



            //-------------- CHECK EVENT OCUURRENCE ----------------- //



            $occurrenc_difference = $this->date_difference($value->start_date,$month_date,3) / $every;

            if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && $occurrenc_difference > $end_after_occurrence - 1){

               continue;

            }



            $resArr[$i]['event_id'] = (string) $value->event_id;

            $resArr[$i]['event_type'] = (string) $value->event_type;

            $resArr[$i]['is_completed'] = (string) $value->is_completed;

            $resArr[$i]['user_id'] = (string) $value->user_id;

            $resArr[$i]['title'] =  $value->title;

            $resArr[$i]['all_day'] = (string) $value->all_day;

            $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($month_date));

            $resArr[$i]['start_time'] =  $value->start_time;

            $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($month_end_date));

            $resArr[$i]['end_time'] =  $value->end_time;

            $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

            $resArr[$i]['created_at'] =  $value->created_at;

            $resArr[$i]['updated_at'] =  $value->updated_at;

            $resArr[$i]['set_reminder'] =  $value->set_reminder;

            $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

            $resArr[$i]['notification_type'] =  $value->notification_type;

            $resArr[$i]['notification'] =  $value->notification;

            $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

            $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

           

         } 

         return $resArr;

            

      }else if($frequency == "3"){



         //-------------- REPET YEARLY ----------------- //



         $difference = $this->date_difference($value->start_date,$end_date,4);

         // if($every != ""){

         //   $difference = round($difference /  $every );

         // }else{

         //    return array();

         // } 

       

         for ($i=0; $i <= $difference; $i=$i+$every) {

           

            $year_date =  date('d-m-Y',strtotime($value->start_date." +".$i." year")); 

            $year_end_date =  date('d-m-Y',strtotime($value->end_date." +".$i." year")); 



            

            //-------------- CHECK EVENT BETWENT START & END DATE ----------------- //



            if( ( strtotime($start_date) > strtotime(date('d-m-Y',strtotime($year_date)))

                  && strtotime(date('d-m-Y',strtotime($start_date))) > strtotime(date('d-m-Y',strtotime($year_end_date))) )

               || strtotime($end_date) < strtotime(date('d-m-Y',strtotime($year_date)))){

               continue;

            }



            //-------------- CHECK END ON EVENT ----------------- //



            if($end_never !="1" && $end_on !="" && (strtotime($end_on) == strtotime(date('d-m-Y',strtotime($year_date))) 

                                                    || strtotime($end_on) < strtotime(date('d-m-Y',strtotime($year_date))))){

               continue;

            }



            //-------------- CHECK EVENT OCUURRENCE ----------------- //

            

            $occurrenc_difference = $this->date_difference($value->start_date,$year_date,4) / $every;

            if($end_never !="1" && isset($end_after_occurrence) && $end_after_occurrence !="" && $occurrenc_difference > $end_after_occurrence - 1){

               continue;

            }



            $resArr[$i]['event_id'] = (string) $value->event_id;

            $resArr[$i]['event_type'] = (string) $value->event_type;

            $resArr[$i]['is_completed'] = (string) $value->is_completed;

            $resArr[$i]['user_id'] = (string) $value->user_id;

            $resArr[$i]['title'] =  $value->title;

            $resArr[$i]['all_day'] = (string) $value->all_day;

            $resArr[$i]['start_date'] =  date('d-m-Y',strtotime($year_date));

            $resArr[$i]['start_time'] =  $value->start_time;

            $resArr[$i]['end_date'] =  date('d-m-Y',strtotime($year_end_date));

            $resArr[$i]['end_time'] =  $value->end_time;

            $resArr[$i]['repeat_mode'] =  $value->repeat_mode;

            $resArr[$i]['created_at'] =  $value->created_at;

            $resArr[$i]['updated_at'] =  $value->updated_at;

            $resArr[$i]['set_reminder'] =  $value->set_reminder;

            $resArr[$i]['reminder_date_time'] =  $value->reminder_date_time;

            $resArr[$i]['notification_type'] =  $value->notification_type;

            $resArr[$i]['notification'] =  $value->notification;

            $resArr[$i]['custom_notification'] =  (isset($value->custom_notification) && $value->custom_notification != "")? json_decode($value->custom_notification,true) : array();

            $resArr[$i]['location'] =  (isset($value->location) && $value->location != "")? $value->location : "";

         } 

         return $resArr;

            

      }

   }



   //-------------- CONVERT MULTIDEMINTIONAL TO SINGLE  ----------------- //



   function convert_array($res){

      $all = array();

      if (count($res) > 0) {

         $i = 0;

         foreach ($res as  $value) {

            foreach ($value as $row) {

               $all[$i] = $row;

               $i++;

            }   

         }

      }

       return $all;

   }



   function is_multi_array( $arr ) {

       rsort( $arr );

       return isset( $arr[0] ) && is_array( $arr[0] );

   }



   //-------------- GET DATE DIFFERENCE ----------------- //



   function date_difference($start_date,$end_date,$type){

      //Type -1:Minute,0:Hour,1:Day,2:Week,3:Month,4:Year

      $start = strtotime($start_date); 

      $end = strtotime($end_date);

      if($type == -1){

        $start_date = date_create($start_date);

        $end_date= date_create($end_date);

        $interval= date_diff($start_date, $end_date);

        return ($interval->days * 24) + $interval->i;

      }else if($type == 0){

        $start_date = date_create($start_date);

        $end_date= date_create($end_date);

        $interval= date_diff($start_date, $end_date);

        return ($interval->days * 24) + $interval->h;

      }else if($type == 1){

        $datediff = $end - $start;

        return $day = round($datediff / (60 * 60 * 24));

      }elseif ($type == 2) {

        $datediff = $end - $start;

        $difference = round($datediff / (60 * 60 * 24));

        return round($difference / 7);

      }elseif ($type == 3) {

         $year1 = date('Y', $start);

         $year2 = date('Y', $end);

         $month1 = date('m', $start);

         $month2 = date('m', $end);

         return $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

      }elseif ($type == 4) {

        $start_date = date_create($start_date);

        $end_date= date_create($end_date);

        $interval= date_diff($start_date, $end_date);

        return $year = $interval->format('%y');

      }   

   }  



   //-------------- GET EXPLODE ON STRING ----------------- //



   function format_week_day($on) {



      $number = preg_replace('/[^0-9]/', '', $on);

      $letters = preg_replace('/[^a-zA-Z]/', '', $on);



      $resArr = array();

      $week_letter = "";

      $day_letter = "";



      //-------------- GET WEEK LETTER -----------------//



      if($number == '1'){

         $week_letter = "first";

      }else if($number == '2'){

         $week_letter = "second";

      }else if($number == '3'){

         $week_letter = "third";

      }else if($number == '4'){

         $week_letter = "fourth";

      }else if($number == '5'){

         $week_letter = "fifth";

      }



      //-------------- GET DAY LETTER -----------------//



      if($letters == 'SU'){

         $day_letter = "Sunday";

      }else if($letters == 'MO'){

         $day_letter = "Monday";

      }else if($letters == 'TU'){

         $day_letter = "Tuesday";

      }else if($letters == 'WE'){

         $day_letter = "Wednesday";

      }else if($letters == 'TH'){

         $day_letter = "Thursday";

      }else if($letters == 'FR'){

         $day_letter = "Friday";

      }else if($letters == 'SA'){

         $day_letter = "Saturday";

      }



      $resArr['week_letter'] = $week_letter;

      $resArr['day_letter'] = $day_letter;

      return $resArr;

      

   }



}

