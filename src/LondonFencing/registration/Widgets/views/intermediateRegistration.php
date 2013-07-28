<?php 
require_once dirname(dirname(__DIR__))."/registration.php";
use LondonFencing\registration as Reg;

$reg = new Reg\registration($db);

if (isset($_GET['token']) && preg_match('%^[A-Z]{2}\-000I\-\d+$%', $_GET['token'], $matches)){

    $regNfo = $reg->getIntRegRecord($_GET['token']);
    if (!empty($regNfo)){
        global $message;
        $sent = 0;
        if (!empty($_POST) && !empty($_POST["RQvalNUMBsessionID"])){
            $_POST['RQvalALPHsessionID'] = $_POST["RQvalNUMBsessionID"];
            $_POST["RQvalNUMBsessionID"] = 1;
        }
        if (!empty($_POST) && isset($_POST["nonce"]) && validate_form($_POST)) {
                
                $valid = 1;
                $regAge = (date("U") - strtotime($_POST['RQvalDATEbirthDate']))/(60*60*24*365);

                if ($regAge < 9){
                    $message .= "<li>You must be a minimum age of nine (9) to participate in the fencing class</li>";
                    $valid = 0;
                }
                if ($regAge < 18 && trim($_POST['OPvalALPHparentName']) == ""){
                    $message .= "<li>A Parent or Legal Guardian name must be provided. <em>Text</em></li>";
                    $valid = 0;
                }
                if ($valid == 1){
                    list($sent, $message) = $reg->saveIntermediateRegistration($_POST);
                    $regKey = $_GET['token'];
                }
        }
        if ($sent == 1){
                //change this to intRegistrationConfirm (create new file) need a new link to the printable form also 
                //or! modify the reg confirm to look for intermediate
                include_once __DIR__ ."/intermediateConfirm.php";
            }
        else{
                $sessionNfo['itemID'] = trim($_GET['token']);
                $post = array(
                "RQvalALPHfirstName"            => $regNfo['firstName'],
                "RQvalALPHlastName"             => $regNfo['lastName'],
                "RQvalDATEbirthDate"            => "",
                "RQvalALPHgender"               => "",
                "RQvalALPHaddress"              => "",
                "OPvalALPHaddress2"             => "",
                "RQvalALPHcity"                 => "",
                "RQvalALPHprovince"             => "ON",
                "RQvalPOSTpostalCode"           => "",
                "RQvalPHONphoneNumber"          => "",
                "RQvalMAILemail"                => $regNfo['email'],
                "OPvalMAILaltEmail"             => "",
                "OPvalALPHparentName"           => "",
                "RQvalALPHemergencyContact"     => "",
                "RQvalPHONemergencyPhone"       => "",
                "RQvalALPHhandedness"           => "",
                "OPvalALPHnotes"                => ""
                ); 
                include_once __DIR__ ."/registrationFormFields.php";
                global $quipp;
                $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/registration.js";
        }
    }
    else{
            print alert_box("It looks like you already completed your registration! Please speak with Brannon Kelly if you have any issues with your registration", 3);
    }
}
else{
        print alert_box("You do not have permission to view this page", 2);
}