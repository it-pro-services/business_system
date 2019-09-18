<?php
use Twilio\Rest\Client;
try{
    require_once "libs/Twilio/autoload.php";
    // Your Account SID and Auth Token from twilio.com/console
    $sid = 'AC51e2de460509b093ad57b2a709459bae';
    $token = 'ed5e81188a7ed8133d2a8eb9232a2b68';
    $client = new Client($sid, $token);

    $msg = "Message Sent";
    $res = $client->messages->create(8019403155, array('from' => '+18015152926', 'body' => $msg));
}catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
