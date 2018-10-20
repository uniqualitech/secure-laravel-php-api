<?php

error_reporting(0);
function SendAndroidPush($id, $msg, $type, $q_id=0) {

// $GOOGLE_SERVER_KEY = 'AAAAVISuBgI:APA91bGU6FzyLuLpz8EMv8EOEddqvHuDwpYpNeztIfyL3hmLYsF_ALLwLus8y20oo086295veKukWEpa-Xh6cmDW0-GYMgm7yxt0BuNwy53JA56b7MrRyxEuMHJnqaW3vpp55gElYU26';

$GOOGLE_SERVER_KEY = 'AAAA6Ls0hZE:APA91bGCNm8hrdBMSJfqixzZmbop07ACJpbLaiszGZkEx62lbw8lIRkVLDTTnIlDRcYPFdIqKy1aw-FaKm_eUS210MWMmyoxVD9F6UOuVhxHveAIWED7sjcb-ba_atOhJEg7fUFqBGlg';

$logFile = "LIVE_PUSH_DEBUG.txt";

$logfh = fopen($logFile, 'a');

fwrite($logfh, "\n\n Log at " . date("Y-m-d H:i:s") . " ---------------- ");

    $data = array('message' => $msg ,
                  'type' =>  isset($type)? $type : 0,
                  'q_id' =>  isset($q_id)? $q_id : 0);

//    $data = array_map('utf8_encode', $data);

    $ids =array($id);

fwrite($logfh, "\n ids : ". $ids);


    $url = 'https://fcm.googleapis.com/fcm/send';

    $post = array(

        'registration_ids' => $ids,

        'data' => $data

    );

    $headers = array(

        'Authorization: key=' . $GOOGLE_SERVER_KEY,

        'Content-Type: application/json'

    );

fwrite($logfh, "\n post : ".  json_encode($post));

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

    $result = curl_exec($ch);

    if (curl_errno($ch)) {

        echo 'GCM error: ' . curl_error($ch);

    }

    curl_close($ch);

fwrite($logfh, "\n\n result : ".  $result);



    //echo $result;

}



// For Testing

 // SendAndroidPush("APA91bEMkzd7RWQ2XBJPj3aK28eGeKX6RXimd-713MOtgNxC0hXBXTNZM__TOQvbzPhGupQF1FcEwNgvobE2LYbi33fKj73FI9DvdTL_vzoh-G-O41dmBxtxpovfQDCc70b8EI7-EJae","Testing");

?>