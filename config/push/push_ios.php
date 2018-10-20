<?php

error_reporting(1);

error_reporting(E_ALL);

function SendPushiOS($deviceToken, $body, $is_production_mode) {

    if ($is_production_mode == '1' || $is_production_mode == 1) {

        //live

        $url = 'ssl://gateway.push.apple.com:2195';

        $cert_path = '../push/ck_prod.pem';

    } else {

        //demo

        $url = 'ssl://gateway.sandbox.push.apple.com:2195';

        $cert_path = '../push/ck_dev.pem';

    }

    

$logFile = "LIVE_PUSH_DEBUG.txt";

$logfh = fopen($logFile, 'a');

fwrite($logfh, "\n\n Log at " . date("Y-m-d H:i:s") . " ---------------- ");

    $passphrase = 'password';

    $ctx = stream_context_create();

    stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_path); // path to cetificate

    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

    // testing

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



?>

