<?php 
require_once dirname(dirname(__DIR__))."/registration.php";
use LondonFencing\registration as Reg;

$reg = new Reg\registration($db);

$sessionNfo = $reg->getRegistrationSession("beginner");

if (isset($sessionNfo['isOpen'])){
    
if ($sessionNfo['isOpen'] === true){
    global $message;
    $sent = 0;
    $body = '';

    if (!empty($_POST) && isset($_POST["nonce"]) && validate_form($_POST)) {
            $valid = 1;
            $regAge = ($sessionNfo["eventStart"] - strtotime($_POST['RQvalDATEbirthDate']))/(60*60*24*365);
            
            if ($regAge < (int)$sessionNfo['ageMin']){
                $message .= "<li>You must be a minimum age of ".trim($sessionNfo['ageMin']." by the time the session starts to register</li>");
                $valid = 0;
            }
            if ($regAge < 18 && trim($_POST['OPvalALPHparentName']) == ""){
                $message .= "<li>A Parent or Legal Guardian name must be provided. <em>Text</em></li>";
                $valid = 0;
            }
            if ($valid == 1){
                list($sent, $message) = $reg->saveRegistration($_POST, "beginner");
            }
    }
    if ($sent == 1){
        include_once __DIR__ ."/registrationConfirm.php";
    }
    else{
        if (isset($sessionNfo['regMax']) && isset($sessionNfo['count']) && (int)$sessionNfo['count'] >= (int)$sessionNfo['regMax']){
            print alert_box("This session is currently full<br />Please complete the form to be put on the waitlist. You will be notified by email if a space becomes available", 3);
        }
        
        $post = array(
        "RQvalALPHfirstName"                 => "",
        "RQvalALPHlastName"                  => "",
        "RQvalDATEbirthDate"                  => "",
        "RQvalALPHgender"                     => "",
        "RQvalALPHaddress"                     => "",
        "OPvalALPHaddress2"                   => "",
        "RQvalALPHcity"                           => "",
        "RQvalALPHprovince"                    => "ON",
        "RQvalPOSTpostalCode"                => "",
        "RQvalPHONphoneNumber"          => "",
        "RQvalMAILemail"                         => "",
        "OPvalALPHparentName"              => "",
        "RQvalALPHemergencyContact"    => "",
        "RQvalPHONemergencyPhone"      => "",
            "OPvalALPHnotes"                        => ""
        );        
        
        $sessionDate = date("F j, Y", $sessionNfo['eventStart']);
        include_once __DIR__ ."/registrationFormFields.php";
        global $quipp;
        $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/registration.js";
    }
  
    }
    else{
        print alert_box("The next beginner session starts on ".date('F j, Y',$sessionNfo['eventStart'])."<br />Registration will be open on  ".date('F j, Y',$sessionNfo['regOpen']), 3);
    }
}