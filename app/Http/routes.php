<?php

//--------------- DEFAULT ---------------//

Route::get('/',array('uses'=>'login@index'));

//--------------- LOGIN ---------------//

Route::post('/login', array('uses'=>'login@login'));

//--------------- LOGOUT ---------------//

Route::get('/logout', 'login@logout');

//--------------- EVENT ---------------//

Route::get('/event', array('uses'=>'event@event'));
Route::get('/event/{ref_key}', array('uses'=>'event@event_by_id'));
Route::post('/event/add_event_comment', array('uses'=>'event@add_event_comment'));
Route::post('/event/get_event_comment', array('uses'=>'event@get_event_comment'));

//--------------- API CALL ---------------//

Route::get('/test_api', function () { return view('test_api'); });
Route::post('/api', array('uses'=>'api@api_request'));

//--------------- CHANGE PASSWORD ---------------//

Route::get('/change_password/', array('uses'=>'authentication@change_password'));
Route::get('/change_password/{ref_key}', array('uses'=>'authentication@change_password'));
Route::post('/update_password', array('uses'=>'authentication@update_password'));
Route::get('/update_password', array('uses'=>'authentication@update_password'));

//--------------- ACTIVE USER ---------------//

Route::get('/active_user/{ref_key}', array('uses'=>'authentication@active_user'));
Route::get('/active_user/', array('uses'=>'authentication@active_user'));

//--------------- TERMS / PRIVACY ---------------//

Route::get('/terms', function () { return view('terms'); });
Route::get('/privacy', function () { return view('privacy'); });

//--------------- NOTIFICATION & REMINDER ---------------//

Route::get('/notification', 'notification@notification');
Route::get('/reminder', 'notification@reminder');
Route::get('/push', 'mail_notification@SendPushiOStest');

