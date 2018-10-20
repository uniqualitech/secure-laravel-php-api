<?php
$tokken = csrf_token();
?>

<html>
    <head>
        <title>Eclat App Test Api</title>
        <style>
            div{
                float: left;
            }
        </style>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css" rel="stylesheet" type="text/css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
        <script>
            window.api_id = 'f63ab8e4dc88fcda0826a2f695bfd7ba';
            window.api_secret = '1c4a417ce28bb18256ca150e4e8d3c6f';
            var testcases = [];

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: '--- GENERAL SERVICES ---',
                data: {
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'sign_up',
                data: {

                    'name':'name',
                    'phone':'+919876543210',
                    'email_id': 'test@gmail.com',
                    'password': '098f6bcd4621d373cade4e832627b4f6',
                    'profile_pic':'user_pic.jpg',
                    'secret': '7629660670a3ad7613e56f048e217c10'    
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'login',
                data: {
                    'email_id': 'test@gmail.com',
                    'password': '098f6bcd4621d373cade4e832627b4f6',
                    'secret': '7629660670a3ad7613e56f048e217c10'     
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'forgot_password',
                data: {        
                    'email_id': 'test@gmail.com'                    
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'resend_email',
                data: {        
                    'email_id': 'test@gmail.com'                  
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'update_profile',
                data: {
                    'user_id':'1',
                    'name':'name',
                    'phone':'+919876543210',
                    'profile_pic':'user_pic.jpg'  
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'change_password',
                data: {
                    'user_id':'1',
                    'old_password':'098f6bcd4621d373cade4e832627b4f6',
                    'new_password':'5a105e8b9d40e1329780d62ea2265d8a'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'contact_us',
                data: {
                    'user_id':'1',
                    'subject':'Isuue regarding Q',
                    'body':'My issue with the q.'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'register_for_push',
                data: {
                    'user_id': '1',
                    'device_id': '123456789',
                    'device_token': '212345364',
                    'certificate_type' : '0 where 0:dev, 1:live '
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'update_time_zone',
                data: {
                    'user_id': '1',
                    'time_zone': 'Asia/Kolkata'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'update_sync_time',
                data: {
                    'user_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'logout',
                data: {
                    'user_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: '--- EVENT SERVICES ---',
                data: {
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'get_category',
                data: {
                    'user_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'add_update_event',
                data: {
                    'user_id': '1',
                    'event_id': '',
                    'related_event_id': '',
                    'google_event_id': '',
                    'event_type':'1:Diary,2:Planner,3:To-dos,4:Notes',
                    'title':'Event',
                    'description':'This is my event',
                    'category_id': '1:Music,-1:Other categry', 
                    'other_category': 'Other Category', 
                    'tag':'firstevent',
                    'priority':'0:Urgent,1:Critical',
                    'start_date':'01-03-2018',
                    'start_time':'16:00',
                    'end_date':'31-03-2018',
                    'end_time':'18:00',
                    'all_day': '0:Disable,1:Enable', 
                    'location':'mumbai',
                    'latitude':'21.189480',
                    'longitude':'72.815733',
                    'repeat_mode':'0:Never,1:Day,2:Week,3:Month,4:Year,5:Custom',
                    'custom_repeat':[
                                        {
                                            "frequency":"0:Daily,1:Weekly,2:Monthly,3:Yearly",
                                            "every":"2 Day,Week,Month,Year",
                                            "on":"0:Sunday,1:Monday",
                                            "end_never":"1:Never",
                                            "end_on":"01-03-2018",
                                            "end_after_occurrence":"1"
                                        }
                                    ],       
                    'notification_type':'0:Both,1:phone,2:email',
                    'notification': '0:never,1:On the day,2:A day before,3:A week before,4:Custom',
                    'set_reminder': '0:Not,1:Yes',
                    'reminder_date_time': '31-03-2018 18:00',
                    'custom_notification':[
                                            {
                                                "frequency":"0:Minues,1:Hours,2:days,3:Weeks",
                                                "before":"2 Minue,Hour,Day,Week"
                                            }
                                        ],
                    "media_count" : "2",
                    "media1" : "media1.png",
                    "media1_title" : "media1.png",
                    "media1_icon_link" : "media1.png",
                    "media2" : "media2.png",
                    "media2_title" : "media2.png",
                    "media2_icon_link" : "media2.png",
                    "event_media":[
                                    {
                                        "file_url":"https://drive.google.com/file/d/0Bxn4aMYNhzGFOXU4d3pwQXZpSkU/view?usp=drive_web",
                                        "title":"banner-5.jpg",
                                        "icon_link":"https://drive-thirdparty.googleusercontent.com/16/type/image/jpeg"
                                    },
                                    {
                                        "file_url":"https://drive.google.com/file/d/0Bxn4aMYNhzGFOXU4d3pwQXZpSkU/view?usp=drive_web",
                                        "title":"banner-5.jpg",
                                        "icon_link":"https://drive-thirdparty.googleusercontent.com/16/type/image/jpeg"
                                    }
                                  ],
                    "with_preloded_media" : "1",
                    "reminder_ids" : "2,3" 
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'remove_event_media',
                data: {
                    'user_id': '1',
                    'media_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'remove_event',
                data: {
                    'user_id': '1',
                    "event_id" : "1,2"
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'upload_media',
                data: {
                    'user_id': '1',
                    "media_count" : "2",
                    "media1" : "media1.png",
                    "media1_title" : "media1.png",
                    "media1_icon_link" : "media1.png",
                    "media2" : "media2.png",
                    "media2_title" : "media2.png",
                    "media2_icon_link" : "media2.png"
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'remove_preloaded_media',
                data: {
                    'user_id': '1',
                    'preloaded_media_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'get_preloaded_media',
                data: {
                    'user_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'add_duplicate_event',
                data: {
                    'user_id': '1',
                    'event_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'search_user',
                data: {
                    'user_id': '1',
                    'search_text': 'Google',
                    'event_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'share_event',
                data: {
                    'user_id': '1',
                    'event_id': '1',
                    'share_to_email': 'test@gmail.com',
                    'start_date_time': '01-04-2018 18:00',
                    'end_date_time': '30-04-2018 18:00',
                    'type': '0:Share via Email, 1:Share via Id'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'list_event',
                data: {
                    'user_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'get_event',
                data: {
                    'user_id': '1',
                    'event_type': '1:Diary,2:Planner,3:To-dos,4:Notes',
                    'start_date': '01-05-2018',
                    'end_date': '31-05-2018',
                    'event_view': '0:List,1:Calendar',
                    'user_current_datetime': '01-04-2018 18:00:00'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'search_event',
                data: {
                    'user_id': '1',
                    'search_text': 'event'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'get_event_by_id',
                data: {
                    'user_id': '1',
                    'event_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'get_event_member',
                data: {
                    'user_id': '1',
                    'event_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'add_to_event_group',
                data: {
                    'user_id': '1',
                    'event_id': '1',
                    'invited_to': '2'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'remove_from_event_group',
                data: {
                    'user_id': '1',
                    'event_id': '1',
                    'remove_to': '2'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'leave_event_group',
                data: {
                    'user_id': '1',
                    'event_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'complete_to_do',
                data: {
                    'user_id': '1',
                    'event_id': '1',
                    'completion_date': '01-04-2018 18:00'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'add_comment',
                data: {
                    'user_id': '1',
                    'event_id': '1',
                    'comment': 'event comment.'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'get_comment',
                data: {
                    'user_id': '1',
                    'event_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'sync_event',
                data: {
                    'user_id': '1'
                }
            });

            testcases.push({
                api_id: window.api_id,
                api_secret: window.api_secret,
                api_request: 'updated_google_event_id',
                data: {
                    'user_id': '1',
                    'event':[
                                {
                                    "event_id":"1",
                                    "google_event_id":"1"
                                },
                                {
                                    "event_id":"5",
                                    "google_event_id":"2"
                                }
                            ]
                }
            });

            function run_testcase() {
                try {
                    var data = JSON.parse($('.testcase').val());
                } catch (e) {
                    $('.output').val('Invalid JSON.');
                    return;
                }
                if (data) {
                    var authorization = $('.authorization').val();
                    var device_type = $('.device_type').val();
                    var device_id = $('.device_id').val();
                    var req= JSON.parse($('.testcase').val());
                    if(req.api_request != 'login' && req.api_request != 'sign_up' && req.api_request != 'forgot_password'){
                        if(authorization == ""){
                            //alert('Authorization can not be empty.');
                            swal('Api','Authorization can not be empty','warning');
                            return false;
                        }
                        if(device_type == ""){
                            //alert('Device type can not be empty.');
                            swal('Api','Device type can not be empty.','warning');
                            return false;
                        }
                        if(device_id == ""){
                            //alert('Device id can not be empty.');
                            swal('Api','Device id can not be empty.','warning');
                            return false;
                        }
                    }
                    $.ajax({
                        method: 'POST',
                        url: '/api',
                        headers: { 'Authorization': authorization, 'device_type': device_type, 'device_id': device_id},
                        data: JSON.parse($('.testcase').val()),
                        success: function (responsejson) {
                            if (typeof responsejson == 'string' || responsejson instanceof String) {
                                try {
                                    var output = JSON.parse(responsejson);
                                    $('.output').val(JSON.stringify(output, null, 4));
                                    if(req.api_request == 'login'){
                                        var access_token = 'cat'+' '+output['data']['access_token'];
                                        //$('#Authorization').val(access_token);
                                        localStorage.setItem("access_token", access_token);
                                    }    
                                } catch (e) {
                                    $('.output').val(responsejson);
                                }
                            } else {
                                $('.output').val(JSON.stringify(responsejson, null, 4));
                            }
                            if ($('.output').val() == '') {
                                $('.output').val('No output.');
                            }
                        },
                        error: function (data, status, error_thrown) {
                            $('.output').val('Error: ' + error_thrown);
                        }
                    });
                }
            }
            function encrypt() {
                window.location = '?input=' + $('.testcase').val() + '&encrypt=1';
            }
            function decrypt() {
                window.location = '?input=' + $('.testcase').val() + '&decrypt=1';
            }
            function toJSON(responsejson) {
                try {
                    var response = JSON.parse(responsejson);
                    return response;
                } catch (e) {
                    return responsejson;
                }
            }
            $(document).ready(function () {
                for (var i = 0; i < testcases.length; i++) {
                    $('.select').append('<option value="' + i + '">' + testcases[i].api_request + '</option>');
                }
                $('.select').change(function () {
                    var selected_service = $('.select option:selected').text();
                    if(selected_service == "login" || selected_service == "sign_up" || selected_service == "forgot_password"){
                      $('#Authorization').val('');  
                    }else{
                        var access_token =  localStorage.getItem("access_token");
                        if(access_token !=""){
                            $('#Authorization').val(access_token);
                        }
                    }

                    // if($('.select option:selected').text() == "add_update_event"){
                    //     $('.notes').show(); 
                    //     $('.note_text').text('')
                    // }else{
                    //     $('.notes').hide();
                    //     $('.note_text').text('');
                    // }

                    if ($('.select').val() != -1) {
                        $('.testcase').val(JSON.stringify(testcases[$('.select').val()], null, 4));
                        $('.output').val('');
                    }
                });
            });

            $(function() {
              $(".select").select2();
            }); 

        </script>
        <style>
            input{
                height: 34px;
                padding: 6px 12px;
                font-size: 14px;
                line-height: 1.42857143;
                color: #555;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            .headers{
                padding: 10px;
                font-weight: 900;
            }
            ::-webkit-scrollbar {
                width: 5px;
                
            }
             
            ::-webkit-scrollbar-track {
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
                border-radius: 10px;
            }
             
            ::-webkit-scrollbar-thumb {
                border-radius: 10px;
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
                    background: #cdcdcd;
            }
            textarea {
                font-family: monospace;
                width: 100%;
                min-height: 600px;
                border-radius: 5px;
            }
            .select2-container--default .select2-results>.select2-results__options{
                max-height: 595 !important;
            }
        </style>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    </head>
    <body>
        <div class="col-md-12" style="margin-top:10px;">
            <div class="col-md-12" style="margin-bottom:10px;">
                <select size="1" class="select" style="width: 600px; height: 40px; font-size: 16px;border-radius: 5px;">
                    <option value="-1">-SELECT TESTCASE-</option>
                </select><button class="btn btn-primary" onclick="run_testcase();" style="margin-left:10px; ">Run</button>
            </div>
            <div class="col-md-6" style="margin-bottom:10px;margin-left:10px;padding: 0px;">
                 <div class="col-md-2 headers">Authorization</div><input id="Authorization" class="Authorization col-md-7" type="text" name="authorization" placeholder="[ AccessKey ]  [ AccessToken ]" value="" style="margin: 5px;">
            </div>   
            <div class="col-md-6" style="margin-bottom:10px;margin-left:10px;padding: 0px;">
                
                 <div class="col-md-2 headers">Device type</div><input class="device_type col-md-7" type="text" name="device_type" placeholder="Device type" value="ios" style="margin: 5px;">
            </div>   
            <div class="col-md-6" style="margin-bottom:10px;margin-left:10px;padding: 0px;">
                 <div class="col-md-2 headers">Device id</div><input class="device_id col-md-7" type="text" name="device_id" placeholder="Device id" value="123456" style="margin: 5px;">
            </div>
            <div class="col-md-6 notes" style="margin-bottom:10px;margin-left:10px;padding: 0px;display: none;">
                 <div class="col-md-12" style="padding: 10px;">   
                    <span><b>Note : </b> <span class="note_text"></span></span>
                 </div>   
            </div>
            <div class="col-md-6">
                <span style="font-weight: 900;">API Request:</span>
                <br>   
                <textarea class="testcase"></textarea>
            </div>
            <div class="col-md-6">
                <span style="font-weight: 900;">Response:</span>
                <br> 
                <textarea class="output"></textarea>
            </div>
        </div>
    </body>
</html>